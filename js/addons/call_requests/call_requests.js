(function(_, $){

    $.ceEvent('on', 'ce.commoninit', function(context) {

        // inputmask
        $('.cm-cr-mask-time').mask('99:99');

        // inputmask-multi
        var mask_elements = $('.cm-cr-mask-phone');
        if (mask_elements.length && _.call_requests_phone_masks_list) {
            var maskList = $.masksSort(_.call_requests_phone_masks_list, ['#'], /[0-9]|#/, "mask");
            var maskOpts = {
                inputmask: {
                    definitions: {
                        '#': {
                            validator: "[0-9]",
                            cardinality: 1
                        }
                    },
                    showMaskOnHover: false,
                    autoUnmask: false,
                },
                match: /[0-9]/,
                replace: '#',
                list: maskList,
                listKey: "mask"
            };

            mask_elements.each(function() {
                if (_.call_phone_mask) {
                    $(this).inputmask({
                        mask: _.call_phone_mask,
                        showMaskOnHover: false,
                        autoUnmask: false
                    });

                } else {
                    $(this).inputmasks(maskOpts);
                }

            });


        }
        
    });

    $.ceEvent('on', 'ce.formpre_call_requests_form', function(form, elm) {
        var val_email = form.find('[name="call_data[email]"]').val(),
            val_phone = form.find('[name="call_data[phone]"]').val(),
            allow = !!(val_email || val_phone),
            error_box = form.find('.cm-cr-error-box'),
            dlg = $.ceDialog('get_last');
        
        error_box.toggle(!allow);
        dlg.ceDialog('reload');

        if (allow) {
            var product_data = $('[name="' + form.data('caProductForm') + '"]').serializeObject();
            
            $.each(product_data, function(key, value){
                if (key.match(/product_data/)) {
                    form.append('<input type="hidden" name="' + key + '" value="' + value + '" />');
                }
            });
        }

        return allow;
    });

})(Tygh, Tygh.$);

