
SampleShop = {
    user: null,
    initialized: false,
    on: {
        login: null,
        init: null
    },

    enable: function() {
        var enable = function() {
            $('form [type=submit]:disabled').prop('disabled', false);
        };
        if (this.initialized) {
            enable();
        } else {
            this.on.init = enable;
        }
    },

    initShop: function(user) {
        this.user = user;

        var self = this;
        $('#shelf').on('submit', '.add-to-cart', function(ev) {
            if (!self.user) {
                var $form = $(this);
                self.promptLogin(function() { $form.submit() });
                return false;
            }
        });

        this.initCommon();
    },

    initCheckout: function(user) {
        this.user = user;

        var self = this;
        $('body').on('submit', '.spend-prepaid', function(ev) {
            if (!self.user.canSpend) {
                var $form = $(this);
                self.promptSpend(function() { $form.submit() });
                return false;
            }
        });

        this.initCommon();
    },

    initCommon: function() {
        $('body').on('submit', 'form', function(ev) {
            $('[type=submit]').prop('disabled', true);
        });

        this.initialized = true;
        if (this.on.init) {
            this.on.init();
            this.on.init = null;
        }
    },

    promptLogin: function(func) {
        $('#loginTip').modal();
        $('#onego_slideinwidget_container').css('z-index', 2000);
        var hideTip = function() {
            $('#loginTip').modal('hide');
            $('#onego_slideinwidget_container').css('z-index', 999);
        };
        this.on.login = function() {
            func();
            hideTip();
        };
        window.oneGoSlideInWidget.loadSignInPage();
        window.oneGoSlideInWidget.show();
        var self = this;
        window.oneGoSlideInWidget.onHide(function() {
            hideTip();
            self.enable();
            self.on.login = null;
        });
    },

    promptSpend: function(func) {
        var self = this;
        var w = window.outerWidth | 0;
        var h = window.outerHeight | 0;
        var x = (window.screenX != undefined ? window.screenX : window.screenLeft) | 0;
        var y = (window.screenY != undefined ? window.screenY : window.screenTop) | 0;
        var left = x + w / 2 - 250;
        var top = y + h / 2 - 350;
        var promptSpendWindow = window.open(
            '?a=authRequest&spending=1',
            'promptSpendWindow',
            'width=500,height=500,left=' + left + ',top=' + top);
        if (promptSpendWindow.focus) {
            promptSpendWindow.focus();
        }
        var closeTimer = setInterval(function() {
            if (promptSpendWindow.closed) {
                clearInterval(closeTimer);
                self.enable();
                self.on.login = null;
            }
        }, 700);
        this.on.login = function() {
            func();
            promptSpendWindow.close();
        };
        return false;
    },

    login: function(user) {
        this.user = user;
        this.enable();
        if (this.on.login) {
            this.on.login();
        }
        this.on.login = null;

        var $authwidget = $('.onego-authwidget iframe');
        $authwidget.hide().attr('src', $authwidget.attr('src'))
            .on('load', function() { $authwidget.fadeIn(); });
    },
}
