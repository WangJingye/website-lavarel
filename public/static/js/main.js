$(function () {
    $('.select2').select2();
    $('form .select2,.select').on('change', function () {
        $(this).valid();
    });
    jQuery.validator.setDefaults({
        errorClass: "invalid-feedback",
        errorElement: "div",
        highlight: function (element, errorClass, validClass) {
            if ($(element).hasClass('select2')) {
                $(element).next('span').find('.select2-selection').removeClass('is-valid').addClass('is-invalid');
            } else {
                $(element).parent().parent().find('input,select').removeClass('is-valid').addClass('is-invalid');
            }
        },
        unhighlight: function (element, errorClass, validClass) {
            if ($(element).hasClass('select2')) {
                $(element).next('span').find('.select2-selection').removeClass('is-invalid').addClass('is-valid');
            } else {
                $(element).parent().parent().find('input,select').removeClass('is-invalid').addClass('is-valid');
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr('type') == 'radio' || element.attr('type') == 'checkbox') {
                error.insertAfter(element.parents('.form-radio-group'));
            } else if (element.hasClass('select2')) {
                error.insertAfter(element.next('span'));
            } else {
                error.insertAfter(element);
            }
        }
    });
    $('.fileinput-box-list').on('change', '.fileinput-input', function () {
        var $this = $(this);
        var box = $this.parents('.fileinput-box');
        box.find('input[type=hidden]').remove();
        var boxContainer = $this.parents('.fileinput-box-list');
        var maxNumber = boxContainer.attr('data-max');
        var nowNumber = boxContainer.find('.fileinput-box').length;
        if ($this.val()) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if ($this.attr('data-type') == 'image' && box.find('img').length) {
                    box.find('img').attr('src', e.target.result);
                } else if ($this.attr('data-type') == 'video' && box.find('video').length) {
                    box.find('video').attr('src', e.target.result);
                } else {
                    var imgHtml = $this.attr('data-type') == 'image' ? '<img src="' + e.target.result + '">' : '<video src="' + e.target.result + '" controls="controls"></video>';
                    box.find('.fileinput-button').before(imgHtml);
                    var btnHtml = '<div class="file-remove-btn"><div class="btn btn-sm btn-outline-danger" style="font-size: 0.5rem;">删除</div></div>';
                    box.find('.fileinput-button').after(btnHtml);
                    box.find('.plus-symbol').hide();
                }
            };
            if ($this.hasClass('add-new') && maxNumber > nowNumber) {
                var newBox = box.clone();
                box.after('<div class="fileinput-box">' + newBox.html() + '</div>');
            }
            if ($this[0].files.length) {
                reader.readAsDataURL($this[0].files[0]);
                $this.removeClass('add-new');
            }
        } else {
            box.find('.plus-symbol').show();
            box.find('img').remove();
            box.find('video').remove();
        }
    }).on('click', '.file-remove-btn', function () {
        var $this = $(this);
        var boxContainer = $this.parents('.fileinput-box-list');
        var box = $this.parents('.fileinput-box');
        var emptyBox = box.clone();
        if (!boxContainer.find('.add-new').length) {
            emptyBox.find('img').remove();
            emptyBox.find('video').remove();
            emptyBox.find('input[type=hidden]').remove();
            emptyBox.find('.fileinput-input').addClass('add-new');
            emptyBox.find('.file-remove-btn').remove();
            emptyBox.find('.plus-symbol').show();
            boxContainer.find('.fileinput-box').last().after('<div class="fileinput-box">' + emptyBox.html() + '</div>')
        }
        box.remove();
    });
    $('.main-item').click(function () {
        $('.sub-item').collapse('hide');
        $(this).next('.sub-item').collapse('toggle')
    });
    $('.list-sub-item .list-group-item').click(function () {
        location.href = $(this).data('url');
    });
    $('.search-form').on('click', '.search-btn,.search-with-export-btn', function () {
        var form = $(this).parents('form');
        if ($('#page-size').get(0)) {
            form.append('<input type="hidden" name="page_size" value="' + $('#page-size').val() + '"/>');
        }
        if ($('input[name=export_type]').get(0)) {
            form.find('input[name=export_type]').val(0);
        }
        form.submit();
    }).on('click', '.export-btn', function () {
        var $this = $(this);
        var html = '<form>' +
            '<div class="custom-control custom-radio">' +
            '<input type="radio" name="export_type" id="export-type1" value="1" checked class="custom-control-input">' +
            '<label class="custom-control-label" for="export-type1">条件导出</label>' +
            '</div>' +
            '<div class="custom-control custom-radio">' +
            '<input type="radio" name="export_type" id="export-type2" value="2" class="custom-control-input">' +
            '<label class="custom-control-label" for="export-type2">勾选导出</label>' +
            '</div>' +
            '</form>';
        $.showModal({
            title: '导出', content: html, width: '30vw', okCallback: function () {
                var type = $('#modal-event').find('input[name=export_type]:checked').val();
                var form = $this.parents('form');
                if (type == 1) {
                    if (!form.find('input[name=export_type]').get(0)) {
                        form.append('<input type="hidden" name="export_type" value="' + type + '">');
                    } else {
                        form.find('input[name=export_type]').val(type);
                    }
                    form.submit();
                } else {
                    var ids = [];
                    $('.check-one:checked').each(function () {
                        ids.push($(this).val())
                    });
                    if (!ids.length) {
                        $.error('请至少选择一条记录');
                        return false;
                    }
                    $('#export-form').remove();
                    var html = '<form id="export-form" action="' + form.attr('action') + '" method="post">' +
                        '<input type="hidden" name="export_type" value="' + type + '">' +
                        '<input type="hidden" name="ids" value="' + ids.join(',') + '">' +
                        '</form>';
                    $('body').append(html);
                    $('#export-form').submit();
                }

            }
        })
    });
    $('#page-size').change(function () {
        var form = $('.search-form');
        if (!form.find('[name=page_size]').get(0)) {
            form.append('<input type="hidden" name="page_size" value="' + $(this).val() + '">');
        } else {
            form.find('[name=page_size]').val($(this).val());
        }
        form.submit();
    });
    if ($('.kindeditor').get(0)) {
        KindEditor.create('.kindeditor', {
            allowFileManager: false,
            uploadJson: '/system/upload/index',
            afterBlur: function () {
                this.sync();
            }
        });
    }
    $('body').on('click', '.check-all', function () {
        if ($(this).prop('checked') == true) {
            $(this).parents('table').find('.check-one').prop('checked', true);
        }
    });
    $('.select-ztree').each(initZTree).click(function (e) {
        var ztreeId = $(this).attr('data-ztree-id');
        if ($("#menuContent" + ztreeId).is(':hidden')) {
            $("#menuContent" + ztreeId).slideDown("fast");
        } else {
            $("#menuContent" + ztreeId).fadeOut("fast");
        }
        $('body').bind("mousedown", function (event) {
            if (!(event.target.id == ("menuContent" + ztreeId) || $(event.target).parents("#menuContent" + ztreeId).length > 0)) {
                $("#menuContent" + ztreeId).fadeOut("fast");
                $("body").unbind("mousedown");
            }
        });
    });
});
function initZTree() {
    var selectZtreeSetting = {
        check: {
            enable: true,
            chkboxType: {"Y": "s", "N": "s"}
        },
        view: {
            dblClickExpand: false
        },
        data: {
            simpleData: {
                enable: true
            }
        },
        callback: {
            onCheck: function (e, treeId, treeNode) {
                var zTree = $.fn.zTree.getZTreeObj(treeId),
                    nodes = zTree.getCheckedNodes(true),
                    zTreeId = treeId.replace('select-ztree-content', ''),
                    vList = [],
                    idList = [],
                    obj = $('.select-ztree[data-ztree-id="' + zTreeId + '"]');
                for (var i = 0, l = nodes.length; i < l; i++) {
                    if (obj.attr('data-step') && obj.attr('data-step') == 'last') {
                            vList.push(nodes[i].name);
                            idList.push(nodes[i].id);
                    } else {
                        vList.push(nodes[i].name);
                        idList.push(nodes[i].id);
                    }

                }

                obj.attr("value", vList.join(','));
                obj.next().attr('value', idList.join(','));
            }
        }
    };

    $(this).attr('data-ztree-id', $('.select-ztree[data-ztree-id=1]').length + 1);
    var ztreeId = $(this).attr('data-ztree-id');
    var zNodes = selectZtreeData[$(this).attr('data-data_key')],
        vList = [],
        idList = [];
    for (var i in zNodes) {
        if (zNodes[i].hasOwnProperty('checked') && zNodes[i].checked) {
            vList.push(zNodes[i].name);
            idList.push(zNodes[i].id);
        }
    }
    $(this).attr('value', vList.join(','));
    var html = '';
    if ($(this).attr('name')) {
        var nameHtml = 'name="' + $(this).attr('name') + '"';
        html = '<input type="hidden" class="select-ztree-key" ' + nameHtml + ' value="' + idList.join(',') + '">';
        $(this).after(html);
    } else {
        html = '<input type="hidden" class="select-ztree-key" value="' + idList.join(',') + '">';
        $(this).after(html);
    }
    $(this).attr('readonly', true).removeAttr('name');

    html = '<div id="menuContent' + ztreeId + '" class="menuContent" style="display:none; position:' +
        ' absolute;z-index: 999999;box-shadow:0 0 5px 1px #ccc;background: white;height:260px;border-radius: 5px;border: 1px solid #ccc;overflow-y: scroll">' +
        '<ul id="select-ztree-content' + ztreeId + '" class="ztree" style="margin-top:0; width:180px; height: 300px;"></ul>' +
        '</div>';
    $('body').append(html);
    $.fn.zTree.init($('#select-ztree-content' + ztreeId), selectZtreeSetting, zNodes);
    $("#menuContent" + ztreeId).css({
        left: $(this).offset().left + "px",
        top: $(this).offset().top + $(this).outerHeight() + "px"
    });
}

function listDropDown(id, clazz, type) {
    $('.ajaxDropDownView[data-id="' + id + '"]').each(function () {
        if (type == 'show') {
            $(this).show();
        } else {
            $(this).hide();
        }
        var newId = $(this).find('.' + clazz).data('id');
        console.log(newId);
        listDropDown(newId, clazz, type);
    });
}

function getFreightFee(variations) {
    if (typeof freightList == 'undefined') {
        throw new Error('运费列表不能为空');
    }
    var fee = 0;
    for (var i in variations) {
        var v = variations[i];
        if (v['number'] == 0) {
            continue;
        }
        var freight = freightList[v['freight_id']];
        number = v['number'] - freight['number'];
        if (freight['freight_type'] == 2) {
            number = v['number'] * v['product_weight'] - freight['number'];
        }
        fee += parseFloat(freight['start_price']);
        if (number > 0) {
            fee += freight['step_price'] * Math.ceil(number / freight['step_number']);
        }
    }
    return fee.toFixed(2);
}