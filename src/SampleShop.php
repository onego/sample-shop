<?php


class SampleShop
{
    private $cfg;

    function __construct()
    {
        $this->cfg = require __DIR__ . '/config.php';

        session_start();

        $this->oauth = OneGoSDK_OAuth::init(new OneGoSDK_OAuthConfig(
            $this->cfg['OneGo']['apiKey'],
            $this->cfg['OneGo']['apiSecret'],
            $this->cfg['OneGo']['baseOAuthUrl']
        ));

        $this->api = OneGoSDK_API::init(new OneGoSDK_APIConfig(
            $this->cfg['OneGo']['apiKey'],
            $this->cfg['OneGo']['apiSecret'],
            $this->cfg['OneGo']['storeId'],
            $this->cfg['OneGo']['baseApiUrl']
        ));

        // If we have access token but it is expired, let's try to refresh it
        // using refresh token.
        if (!empty($_SESSION['OneGo']['auth'])
            && $_SESSION['OneGo']['auth']->isExpired()) {

            try {
                $_SESSION['OneGo']['auth'] = $this->oauth->refreshAccessToken(
                    $_SESSION['OneGo']['auth']->refreshToken
                );
            } catch (Exception $e) {
                error_log($e);

                // We don't need invalid access token in session.
                unset($_SESSION['OneGo']['auth']);

                // Cancel current OneGo transaction (if any).
                try {
                    // Note: We are not using access token here.
                    if ($tx = $this->getCurrentTransaction(false)) {
                        // Note: cancel() does not use access token.
                        $tx->cancel();
                    }
                } catch (Exception $e) {
                    error_log($e);
                    // Token refresh failed.
                    // Transaction cancel failed.
                    // There's only so much cleanup we can do.
                    // Continue silently.
                }
                // Clean OneGo session data.
                $this->deleteCurrentTransaction();

                // Note: we are keeping internal cart in session. On login it
                // will trigger transaction start and all cart will restored.
            }
        }

        // If we have access token, set it to API.
        if (!empty($_SESSION['OneGo']['auth'])) {
            $this->api->setOAuthToken($_SESSION['OneGo']['auth']);
        }
    }

    /**
     * Displays shop page.
     */
    function main()
    {
        $this->render('main', array(
            'baseJsSdkUrl' => $this->cfg['OneGo']['baseJsSdkUrl'],
            'shelf' => $this->getInventory(),
            'cart' => @$_SESSION['OneGo']['cart'],
        ));
    }

    /**
     * Displays checkout page.
     */
    function checkout() {

        if (!$this->checkAuth() || !$this->checkCart()) {
            return $this->redirect($this->baseUri());
        }

        $this->render('checkout', array(
            'baseJsSdkUrl' => $this->cfg['OneGo']['baseJsSdkUrl'],
            'shopUri' => $this->baseUri(),
            'cart' => $_SESSION['OneGo']['cart'],
            'prepaid' => $_SESSION['OneGo']['prepaid'],
        ));
    }


    /**
     * Redirects to OneGo OAuth autorization page.
     *
     * When prepaid spending is not required, 'autologin' can be requested and
     * OneGo can redirect back with authorization code immediately without any
     * user action. This all can be done in the background using hidden iframe.
     *
     * When spending is required (SCOPE_USE_BENEFITS) OneGo displays login
     * form for user to confirm with password that spending is allowed. This
     * cannot be done in the background and user must see OneGo login form.
     */
    function authRequest()
    {
        // Do we need spending scope?
        if (!empty($_GET['spending'])) {
            // SCOPE_USE_BENEFITS cannot be requested with 'autologin'.
            $scopes = array(
                OneGoSDK_Impl_OneGoOAuth::SCOPE_RECEIVE_ONLY,
                OneGoSDK_Impl_OneGoOAuth::SCOPE_USE_BENEFITS,
            );
            $autologin = false;
        } else {
            $scopes = array(
                OneGoSDK_Impl_OneGoOAuth::SCOPE_RECEIVE_ONLY,
            );
            $autologin = true;
        }

        // This URL will receive authorization code response from OneGo.
        // Same URL will be needed to request access token. It may be generated
        // again but we will just store it in session along with request ID.
        $proto = !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
            ? $_SERVER['HTTP_X_FORWARDED_PROTO']
            : (!empty($_SERVER['HTTPS']) ? 'https' : 'http');
        $backUrl = $proto . '://' . $_SERVER['HTTP_HOST']
            . $this->baseUri() . '?a=authResponse';

        // Request ID can be any random string. It will be used to check that
        // response actually originated from our request.
        $requestId = md5(openssl_random_pseudo_bytes(64));

        // Response action will use this to validate request
        // and to get access token.
        $_SESSION['OneGo']['authRequest'] = array(
            'id' => $requestId,
            'backUrl' => $backUrl,
        );

        $oneGoAuthUri = $this->oauth->getAuthorizationUrl(
            $backUrl,
            $scopes,
            $requestId,
            $autologin
        );

        $this->redirect($oneGoAuthUri);
    }


    /**
     * Receives OneGo OAuth autorization code.
     */
    function authResponse()
    {
        $request = @$_SESSION['OneGo']['authRequest'];

        // Simply respond with error if response does not match our request ID.
        if (empty($_GET['state']) || $_GET['state'] != $request['id']) {
            return $this->forbidden('Invalid state');
        } else {
            unset($_SESSION['OneGo']['authRequest']);
        }

        // If OneGo didn't pass authorization code to us then some error must
        // have happened. In error case there will be 'error' and
        // 'error_description' in query parameters.
        if (empty($_GET['code'])) {
            // 'error_descirption' contains error description that developers
            // should understand. Since this is sample shop for developers we
            // will simply display that.
            // More sophisticated solution should display user friendly message
            // using error code from 'error' parameter.
            return $this->forbidden($_GET['error_description']);
        }

        // Note that we need to pass same URL to requestAccessToken() as we used
        // to request authorization code.
        $newAccessToken = $this->oauth->requestAccessToken(
            $_GET['code'],
            $request['backUrl']
        );

        // We will need this to revoke current access token (if any) later.
        $oldAccessToken = @$_SESSION['OneGo']['auth'];

        // Store our new access token to sessoin.
        $_SESSION['OneGo']['auth'] = $newAccessToken;
        $_SESSION['OneGo']['user'] = array(
            'canSpend' => $newAccessToken->hasScope(
                OneGoSDK_Impl_OneGoOAuth::SCOPE_USE_BENEFITS),
        );

        session_regenerate_id(true);

        // OK, the following might be a bit involving but try to follow. We
        // want to do some cleanup from previous state.

        // Try update current OneGo transaction after authorization. If it
        // fails due to different user login on scope elevation request,
        // recreate transaction for current user.

        $items = $this->getInternalCart();

        // Try to fetch previous transaction.
        // Note: Still using old access token.
        $tx = $this->getCurrentTransaction();

        // Using new access token from now on.
        $this->api->setOAuthToken($newAccessToken);

        if ($tx) {
            try {
                // This fetch will be using new access token.
                $this->getCurrentTransaction();
            } catch (OneGoSDK_OperationNotAllowedException $e) {
                error_log($e);
                // Since we can no longer use previous transaction, cancel it
                // and start new one with current cart items.
                // Note: It is OK to cancel old transaction even after access
                // token change because cancel() does not use access token.
                $tx->cancel();
                $this->createOrUpdateCurrentTransaction($items);
            }
        } elseif ($items) {
            // If there was no OneGo transaction (or it was expired) but we
            // have items in internal cart, start new one.
            $this->createOrUpdateCurrentTransaction($items);
        }

        // Revoke old access token if any.
        if ($oldAccessToken) {
            try {
                $this->oauth->revokeAccessToken($oldAccessToken->accessToken);
            } catch (Exception $e) {
                error_log($e);
                // If token revoke failed, continue silently. There's no reason
                // to prevent user from logging in.
            }
        }

        // Finally inform parent page about user login.
        echo '<script>parent.SampleShop.login('
            . json_encode($_SESSION['OneGo']['user']) . ');</script>';
    }


    /**
     * Cleans session data and revokes OneGo access token if any.
     *
     * This is called from JavaScript when user logout from OneGo is detected.
     */
    function logout()
    {
        // If we have valid access token, revoke it.
        if (!empty($_SESSION['OneGo']['auth'])
            && !$_SESSION['OneGo']['auth']->isExpired()) {

            try {
                $this->oauth->revokeAccessToken(
                    $_SESSION['OneGo']['auth']->accessToken
                );
            } catch (Exception $e) {
                error_log($e);
                // If token revoke failed, continue silently. There's no reason
                // to prevent user from logging out.
            }
        }

        $_SESSION = array();
        session_regenerate_id(true);
        $this->redirect($this->baseUri());
    }


    /**
     * Adds item to cart and updates OneGo transaction.
     */
    function addToCart()
    {
        if (!$this->checkPost() || !$this->checkAuth()) {
            return;
        }

        $item = $_POST['item'];
        $code = $item['code'];

        $items = $this->getInternalCart();
        if (!empty($items[$code])) {
            $items[$code]['quantity'] += 1;
        } else {
            $items[$code] = $item + array('quantity' => 1);
        }
        $this->saveInternalCart($items);

        $this->createOrUpdateCurrentTransaction($items);

        $this->redirectBack();
    }


    /**
     * Removes item from cart and updates OneGo transaction.
     */
    function removeFromCart() {

        if (!$this->checkPost() || !$this->checkAuth() || !$this->checkCart()) {
            return;
        }

        if (empty($_POST['item']['code'])) {
            return $this->forbidden('Item code required');
        }

        $code = $_POST['item']['code'];

        $items = $this->getInternalCart();
        unset($items[$code]);
        $this->saveInternalCart($items);

        if ($items) {
            $this->createOrUpdateCurrentTransaction($items);
        } else {
            if ($tx = $this->getCurrentTransaction()) {
                $tx->cancel();
                $this->deleteCurrentTransaction();
            }
        }

        $this->redirectBack();
    }


    /**
     * Spends user OneGo prepaid.
     */
    function spendPrepaid()
    {
        if (!$this->checkPost() || !$this->checkAuth(true) || !$this->checkCart()) {
            return;
        }

        $tx = $this->getOrRecoverCurrentTransaction();
        if (!$tx) {
            return $this->transactionRecoverFailure();
        }

        if ($tx->getPrepaidAvailable() > 0) {
            // We could spend any amount of available prepaid but for simplicity
            // let's spend it all.
            // Note: If user has more prepaid then the cart total, OneGo will
            // adjust spend amount to cart total.
            $tx->spendPrepaid($tx->getPrepaidAvailable());
            $this->saveCurrentTransaction($tx);
        }

        $this->redirectBack();
    }


    /**
     * Cancels user OneGo prepaid spending.
     */
    function cancelPrepaidSpend()
    {
        if (!$this->checkPost() || !$this->checkAuth(true) || !$this->checkCart()) {
            return;
        }

        $tx = $this->getOrRecoverCurrentTransaction();
        if (!$tx) {
            return $this->transactionRecoverFailure();
        }

        $tx->cancelSpendingPrepaid();
        $this->saveCurrentTransaction($tx);

        $this->redirectBack();
    }


    /**
     * Confirms OneGo transaction.
     */
    function confirmOrder()
    {
        if (!$this->checkPost() || !$this->checkAuth() || !$this->checkCart()) {
            return;
        }

        $tx = $this->getCurrentTransaction();
        // If old transaction is gone, recover it, but since order
        // confirmation is serious business, do not continue without user
        // confirmation.
        if (!$tx = $this->getCurrentTransaction()) {
            if (!$tx = $this->recoverCurrentTransaction()) {
                return $this->transactionRecoverFailure();
            } else {
                return $this->redirectBack(array(
                    'type' => 'warning',
                    'text' => 'Transaction has expired.'
                        . ' Please review and confirm your order again.',
                ));
            }
        }

        // This is where shop order would be persisted to database.

        // Note that there is no undo after transaction was confirmed.
        $tx->confirm();
        $this->deleteCurrentTransaction();
        $this->deleteInternalCart();

        // This is where shop order database transaction would be commited.

        $this->redirect($this->baseUri(), array(
            'type' => 'success',
            'text' => 'Order confirmed. Our hamsters are on'
                . ' their unicycles shipping your order directly to you.',
        ));
    }


    private function buildNewCart($items)
    {
        $newCart = $this->api->newCart();
        foreach ($items as $item) {
            $newCart->addEntry(
                $item['code'],
                $item['price'],
                $item['quantity'],
                false,
                $item['title']
            );
        }
        return $newCart;
    }

    private function createTransaction($newCart)
    {
        $receiptId = date('YmdHis') . '-' . uniqid();
        return $this->api->beginTransaction($receiptId, $newCart);
    }

    private function createOrUpdateCurrentTransaction($items)
    {
        $newCart = $this->buildNewCart($items);
        if ($tx = $this->getCurrentTransaction()) {
            $tx->updateCart($newCart);
        } else {
            $tx = $this->createTransaction($newCart);
            if ($_SESSION['OneGo']['spent'] > 0) {
                $tx->spendPrepaid($_SESSION['OneGo']['spent']);
            }
        }
        $this->saveCurrentTransaction($tx);
        return $tx;
    }

    private function getCurrentTransaction($useAccessToken = true)
    {
        if (!empty($_SESSION['OneGo']['txId'])) {
            try {
                return $this->api->fetchTransactionById(
                    $_SESSION['OneGo']['txId'],
                    !$useAccessToken
                );
            } catch (OneGoSDK_TransactionExpiredException $e) {
                // Ignoring expired transaction exception. New transaction will
                // be started by caller if needed.
                error_log($e);
                $this->deleteCurrentTransaction();
            }
        }
        return null;
    }

    private function getOrRecoverCurrentTransaction()
    {
        if ($tx = $this->getCurrentTransaction()) {
            return $tx;
        } else if ($items = $this->getInternalCart()){
            return $this->createOrUpdateCurrentTransaction($items);
        } else {
            return null;
        }
    }

    private function recoverCurrentTransaction()
    {
        if ($items = $this->getInternalCart()) {
            return $this->createOrUpdateTransaction($items);
        } else {
            return null;
        }
    }

    private function saveCurrentTransaction($tx)
    {
        $_SESSION['OneGo']['txId'] = $tx->getId()->id;
        $_SESSION['OneGo']['cart'] = $tx->getModifiedCart();
        $_SESSION['OneGo']['prepaid'] = $tx->getPrepaidAvailable();
        $_SESSION['OneGo']['spent'] = $tx->getPrepaidSpent();
    }

    private function deleteCurrentTransaction()
    {
        unset($_SESSION['OneGo']['txId']);
        unset($_SESSION['OneGo']['cart']);
        unset($_SESSION['OneGo']['prepaid']);
        unset($_SESSION['OneGo']['spent']);
    }


    private function getInternalCart()
    {
        return !empty($_SESSION['SampleShop']['cart'])
            ? $_SESSION['SampleShop']['cart']
            : array();
    }

    private function saveInternalCart($cart)
    {
        $_SESSION['SampleShop']['cart'] = $cart;
    }

    private function deleteInternalCart()
    {
        unset($_SESSION['SampleShop']['cart']);
    }


    private function checkAuth($spending = false)
    {
        if (empty($_SESSION['OneGo']['auth'])) {
            $this->redirectBack(array(
                'type' => 'warning',
                'text' => 'Please login to OneGo.'
            ));
            return false;
        } elseif ($spending && !$_SESSION['OneGo']['user']['canSpend']) {
            $this->redirectBack(array(
                'type' => 'warning',
                'text' => 'Prepaid spending is not allowed.'
            ));
            return false;
        } else {
            return true;
        }
    }

    private function checkCart()
    {
        if (!$this->getInternalCart()) {
            $this->redirectBack(array(
                'type' => 'warning',
                'text' => 'Please add something to cart first.'
            ));
            return false;
        } else {
            return true;
        }
    }

    private function checkPost()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            header('Allow: POST');
            echo 'Method Not Allowed';
            return false;
        } else {
            return true;
        }
    }

    private function transactionRecoverFailure()
    {
        return $this->redirectBack(array(
            'type' => 'warning',
            'text' => 'Sorry, transaction has expired'
                . ' and we were unable to recover it.',
        ));
    }


    private function baseUri()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    private function redirect($uri, $message = null)
    {
        if ($message !== null) {
            $_SESSION['message'] = $message;
        }
        header("Location: $uri");
    }

    private function redirectBack($message = null)
    {
        if ($message !== null) {
            $_SESSION['message'] = $message;
        }
        if (!empty($_SERVER['HTTP_REFERER'])
            && $_SERVER['HTTP_HOST'] == parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->redirect($this->baseUri());
        }
    }

    private function forbidden($message)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        echo $message;
    }

    private function render($view, $vars = array()) {
        extract($vars);
        $cfg = $this->cfg;
        $html = 'htmlspecialchars';
        $js = 'json_encode';
        $message = @$_SESSION['message'];
        unset($_SESSION['message']);
        include "views/$view.php";
    }

    private function getInventory()
    {
        return array(
            array(
                'code' => 'LATTE',
                'title' => 'Latte',
                'price' => 6,
            ),
            array(
                'code' => 'CAPPUCCINO_BIG',
                'title' => 'Cappuccino (big)',
                'price' => 7,
            ),
            array(
                'code' => 'ICED',
                'title' => 'Iced Coffee',
                'price' => 4,
            ),
            array(
                'code' => 'PANINI',
                'title' => 'Panini',
                'price' => 9,
            ),
            array(
                'code' => 'BROWNIE',
                'title' => 'Brownie cake',
                'price' => 12,
            ),
        );
    }
}
