(function(_, $) {

    $.ceEvent('on', 'ce.commoninit', function(context) {
        var maskList = $.masksSort(_.qiwi_phone_masks_list, ['#'], /[0-9]|#/, "mask");

        var maskOpts = {
            inputmask: {
                definitions: {
                    '#': {
                        validator: "[0-9]",
                        cardinality: 1
                    }
                },
                //clearIncomplete: true,
                showMaskOnHover: false,
                autoUnmask: true
            },
            match: /[0-9]/,
            replace: '#',
            list: maskList,
            listKey: "mask"
        };

        $('.cm-mask', context).each(function() {
            $(this).inputmasks(maskOpts);
        });
    });

}(Tygh, Tygh.$));