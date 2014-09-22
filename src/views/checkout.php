<!DOCTYPE html>
<html lang=en>
<head>
    <meta charset=utf-8>
    <title>Checkout - Sample Shop</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <script id="OneGoSdkLoader">
    (function() {
        var initializer = function() {
            OneGo.init({});

            // ... your code to be run after OneGo JS SDK is loaded

            OneGo.events.on('UserIsSignedIn', function (sessionToken) {
                SampleShop.enable();
            });

            OneGo.events.on('UserIsSignedOut', function () {
                if (SampleShop.user) {
                    SampleShop.user = null;
                    window.location = '?a=logout';
                } else {
                    SampleShop.enable();
                }
            });

            window.oneGoSlideInWidget = OneGo.plugins.slideInWidget.init({
                topOffset: 35,
                isFixed: true,
                showOnFirstView: false
            });
        };
        var onError = function(error) {
            // your error handling code, i.e.:
            alert('OneGo SDK error: ' + error.message);
        }; // please note that this semicolon is required

        (function(d, successCallback, errorCallback){
            var apiKey = <?=$js($cfg['OneGo']['apiKey'])?> // change this to your own API key
            var id = 'onego-jssdk', ref = d.getElementById('OneGoSdkLoader');
            if (d.getElementById(id)) {return;}
            var js = d.createElement('script'); js.id = id; js.async = true;
            js.src = <?=$js($baseJsSdkUrl)?>+'?apikey='+apiKey;
            ref.parentNode.insertBefore(js, ref);
            window.oneGoAsyncInit = successCallback;
            window.oneGoAsyncOnError = errorCallback;
        }(document, initializer, onError));
    })();
    </script>

    <div class="container">

        <?php if ($message): ?>
            <div class="row">
                <div class="col-md-9">
                    <div class="alert alert-<?=$html($message['type'])?> alert-dismissible" role="alert" style="margin-top: 1em">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <?=$html($message['text'])?>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <div class="row">
            <div class="col-md-9">
                <div style="width:200px; height:20px; margin-top:1em;" class="onego-authwidget"
                     data-textcolor="#000"
                     data-linkcolor="#428bca"
                     data-fontsize="14px"
                     data-font="'Helvetica Neue',Helvetica,Arial,sans-serif"
                     data-height="20"
                     data-width="200"
                     data-text="{%NAME%} | {%LOGOUT|Logout%}">
                </div>
            </div>
        </div>


        <div class="row">

            <div class="col-md-6">
                <div class="page-header">
                    <h1>Checkout <small>Sample Shop</small></h1>
                </div>

                <div id="cart" class="checkout">

                    <?php
                    include __DIR__ . '/cart.php';
                    ?>

                    <form action="?a=confirmOrder" method="POST">
                        <input type="hidden" name="order" value="1" />
                        <button type="submit" disabled class="order btn btn-success pull-left">Confirm &amp; Order</button>
                        <div class="clearfix"></div>
                    </form>

                    <?php if ($prepaid > 0): ?>
                        <div class="prepaid panel panel-default">
                            <div class="panel-body">
                                <form action="?a=spendPrepaid" method="POST" class="spend-prepaid">
                                    <input type="hidden" name="spend" value="1" />
                                    <button type="submit" disabled class="btn btn-xs btn-success pull-right">Spend</button>
                                </form>
                                <div>You have <b class="price"><?=$html(sprintf('%0.2f', $prepaid))?></b> in your prepaid account.</div>
                            </div>
                        </div>
                    <?php endif ?>

                </div>

                <div style="margin: 2em 0">
                    ... or <a href="<?=$html($shopUri)?>">continue shopping</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmSpend" tabindex="-1" role="dialog" aria-labelledby="Confirm Spend" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Enter your password to confirm prepaid spending</h4>
          </div>
          <div class="modal-body">
          </div>
        </div>
      </div>
    </div>

    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
    <script>
    $(function() { SampleShop.initCheckout(<?=$js(@$_SESSION['OneGo']['user'])?>); });
    </script>
</body>
</html>
