jQuery.extend({
    count: 1,
    option: {
        'successClass': 'alert-success',
        'errorClass': 'alert-danger',
    },
    success: function (msg, callback, timeout) {
        if (timeout == null) {
            timeout = 3000;
        }
        var alertC = 'alert' + this.count;
        this.count++;
        var html = '<div class="alert ' + this.option.successClass + ' alert-dismissible fade show ' + alertC + '" role="alert">' +
            '<i class="glyphicon glyphicon-ok-circle" style="color: #52c41a;font-size: 1rem;top: 3px;margin-right: 0.1rem"></i>' +
            '<span style="font-size: 0.8rem">' + msg + '</span>' +
            '</button></div>';
        var popupContent = $('body').find('.popup-content');
        if (!$('body').find('.popup-content').get(0)) {
            $('body').append('<div class="popup-content"></div>');
            popupContent = $('body').find('.popup-content');
        }
        popupContent.append(html);
        setTimeout(function () {
            $('.' + alertC).alert('close');
            if (typeof callback == 'function') {
                callback();
            }
        }, timeout);

    },
    error: function (msg, callback, timeout) {
        if (timeout == null) {
            timeout = 3000;
        }
        var alertC = 'alert' + this.count;
        this.count++;
        var html = '<div class="alert ' + this.option.errorClass + ' alert-dismissible fade show ' + alertC + '" role="alert">' +
            '<i class="glyphicon glyphicon-remove-circle" style="color: #ff4d4f;font-size: 1rem;top: 3px;margin-right: 0.1rem"></i>' +
            '<span style="font-size: 0.8rem;">' + msg + '</span>' +
            '</button></div>';
        var popupContent = $('body').find('.popup-content');
        if (!$('body').find('.popup-content').get(0)) {
            $('body').append('<div class="popup-content"></div>');
            popupContent = $('body').find('.popup-content');
        }
        popupContent.append(html);
        setTimeout(function () {
            $('.' + alertC).alert('close');
            if (typeof callback == 'function') {
                callback();
            }
        }, timeout);
    },
    loading: function (type, msg) {
        if (type == null) {
            type = 'show';
        }
        if (msg == null) {
            msg = '数据加载中，请稍后...';
        }
        if (type == 'show') {
            if (!$('#loadingModel').get(0)) {
                var html = '<div id="loadingModel" class="modal">' +
                    '<div class="modal-body">' +
                    '<span><img src="/static/images/load.gif"/></span>' +
                    '<span style="color:red;font-size:15px;">' + msg + '</span>' +
                    '</div></div>';
                $('body').append(html);
                var width = document.documentElement.clientWidth || document.body.clientWidth,
                    height = document.documentElement.clientHeight || document.body.clientHeight,
                    loadingModal = $('#loadingModel');
                var top = (height - loadingModal.height()) / 2;
                var left = (width - loadingModal.width()) / 2;
                loadingModal.css({
                    top: top,
                    left: left
                });
                loadingModal.find('.modal-body').css('padding', 0);
                loadingModal.modal({backdrop: 'static'});
                loadingModal.modal('show');
            }
        } else {
            if ($('#loadingModel').get(0)) {
                $('#loadingModel').modal('hide');
                $('#loadingModel').remove();
            }
        }
    },
    showModal: function (options) {
        if (options === 'loading') {
            if ($('#modal-event-show').get(0)) {
                $('#modal-event-show > div').css('display', 'none');
                $('#modal-event-show').append('<div id="loadingBar" style="text-align: center;height:50px;line-height: 50px;">' +
                    '<span><img src="/images/common/load.gif"/></span>' +
                    '<span style="color:red;font-size:15px;margin-left:10px;">数据处理中</span></div>');
            }
            return;
        } else if (options === 'recover') {
            if ($('#modal-event-show').get(0)) {
                $('#modal-event-show > div').css('display', '');
                $('#loadingBar').remove();
            }
            return;
        } else if (options === 'hide') {
            if ($('#modal-event-show').get(0)) {
                $('#modal-event-show').remove();
            }
            return;
        }
        var opts = {
            title: '添加模块',
            content: '',
            width: 'auto',
            afterShowCallback: false,
            cancelCallback: false,
            cancelTitle: '取消',
            okCallback: false,
            okTitle: '确定'
        };
        for (var k in options) {
            opts[k] = options[k];
        }
        var html = '<div id="modal-event">' +
            '<div class="modal-event-backdrop"></div>' +
            '<div class="modal-event-show">' +
            '<div class="modal-event-header">' +
            '<div class="modal-event-header-title">' + opts['title'] + '</div>' +
            '<div class="modal-event-header-close"><i' +
            'class="glyphicon glyphicon-remove"></i></div>' +
            '</div>' +
            '<div class="modal-event-body">' + opts['content'] + '</div>' +
            '<div class="modal-event-footer">' +
            '<div class="btn btn-outline-secondary modal-event-close">' + opts['cancelTitle'] + '</div>' +
            '<div class="btn btn-outline-primary modal-event-confirm">' + opts['okTitle'] + '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        $(document.body).append(html);
        var modalEventView = $('#modal-event');
        modalEventView.show();
        if (options['width'] != 'auto') {
            var width = document.documentElement.clientWidth || document.body.clientWidth;
            if(width>768){
                modalEventView.find('.modal-event-show').css('width', options['width']);
            }
        }
        if (options['afterShowCallback'] && typeof options['afterShowCallback'] === 'function') {
            options['afterShowCallback']();
        }
        $('.modal-event-header-close,.modal-event-close').click(function (e) {
            e.preventDefault();
            if (options['cancelCallback'] && typeof options['cancelCallback'] === 'function') {
                options['cancelCallback']();
            }
            $(document.body).css({"overflow-y": "auto"});
            modalEventView.remove();
        });
        $('.modal-event-confirm').click(function (e) {
            e.preventDefault();
            if (options['okCallback'] && typeof options['okCallback'] === 'function') {
                var bol = options['okCallback']();
                if (bol !== false) {
                    modalEventView.remove();
                }
            }
        });
    }
});