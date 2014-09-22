<!DOCTYPE html>
<html lang=en>
<head>
    <meta charset=utf-8>
    <title>Sample Shop</title>

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

            OneGo.events.on('UserIsSignedIn', function (arg) {
                if (!SampleShop.user) {
                    var src = '?a=authRequest&background=1';
                    $('body').prepend($('<iframe></iframe>').attr('src', src).css({
                        height: '1px',
                        width: '1px',
                        position: 'absolute',
                        visibility: 'hidden'
                    }));
                } else {
                    SampleShop.enable();
                }
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

            <div id="shelf" class="col-md-6">
                <div class="page-header">
                    <h1>Sample Shop</h1>
                </div>

                <?php foreach ($shelf as $item): ?>
                    <div class="item">
                        <figure>
                            <img src="img/<?=$html(strtolower($item['code']))?>.png" alt="<?=$html($item['title'])?>">
                            <figcaption class="title"><?=$html($item['title'])?></figcaption>
                        </figure>
                        <b class="price pull-left"><?=$html($item['price'])?></b>
                        <form action="?a=addToCart" method="post" class="add-to-cart">
                            <?php foreach ($item as $k => $v): ?>
                                <input type="hidden" name="<?=$html("item[$k]")?>" value="<?=$html($v)?>" />
                            <?php endforeach ?>
                            <button type="submit" disabled class="btn btn-success btn-xs pull-right">Add to cart</button>
                        </form>
                        <div class="clearfix"></div>
                    </div>
                <?php endforeach ?>

                <div class="clearfix"></div>
            </div>

            <div class="col-md-3">

                <div class="page-header">
                    <h1>Cart</h1>
                </div>

                <div id="cart">

                    <?php if ($cart): ?>

                        <?php
                        include __DIR__ . '/cart.php';
                        ?>

                        <form action="" method="GET">
                            <input type="hidden" name="a" value="checkout" />
                            <button type="submit" disabled class="checkout btn btn-success">Checkout</button>
                        </form>

                    <?php else: ?>

                        <div class="placeholder">
                            <p><em>Your cart is empty. Grab something from our shelf!</em></p>
                        </div>

                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <div id="loginTip" style="position:fixed; left:410px; top:80px; color:#fff; z-index:2000; display:none;">
        <p class="lead" style="text-shadow: 0px 0px 6px rgba(0, 0, 0, 0.75);">
            <em>Please login to OneGo.</em>
        </p>
    </div>

    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
    <script>
    $(function() { SampleShop.initShop(<?=$js(@$_SESSION['OneGo']['user'])?>); });
    </script>
</body>
</html>
