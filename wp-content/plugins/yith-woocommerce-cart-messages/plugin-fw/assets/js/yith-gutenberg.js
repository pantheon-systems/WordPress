(function ($) {
    // Get registerBlockType() from wp.blocks in the global scope
    var __ = wp.i18n.__,
        el = wp.element.createElement,
        Fragment = wp.element.Fragment,
        registerBlockType = wp.blocks.registerBlockType,
        RichText = wp.editor.RichText,
        BlockControls = wp.editor.BlockControls,
        InspectorControls = wp.editor.InspectorControls,
        AlignmentToolbar = wp.editor.AlignmentToolbar,
        Components = wp.components,
        RawHTML = wp.element.RawHTML,
        SelectControl = wp.components.SelectControl,
        ToggleControl = wp.components.ToggleControl,
        CheckboxControl = wp.components.CheckboxControl,
        RangeControl = wp.components.RangeControl,
        ColorPicker = wp.components.ColorPicker,
        RadioControl = wp.components.RadioControl,
        TextControl = wp.components.TextControl,
        TextareaControl = wp.components.TextareaControl;
    
    const yith_icon = el('svg', {width: 22, height: 22},
        el('path', {d: "M 18.24 7.628 C 17.291 8.284 16.076 8.971 14.587 9.688 C 15.344 7.186 15.765 4.851 15.849 2.684 C 15.912 0.939 15.133 0.045 13.514 0.003 C 11.558 -0.06 10.275 1.033 9.665 3.284 C 10.007 3.137 10.359 3.063 10.723 3.063 C 11.021 3.063 11.267 3.184 11.459 3.426 C 11.651 3.668 11.736 3.947 11.715 4.262 C 11.695 5.082 11.276 5.961 10.46 6.896 C 9.644 7.833 8.918 8.3 8.282 8.3 C 7.837 8.3 7.625 7.922 7.646 7.165 C 7.667 6.765 7.804 5.955 8.056 4.735 C 8.287 3.579 8.403 2.801 8.403 2.401 C 8.403 1.707 8.224 1.144 7.867 0.713 C 7.509 0.282 6.994 0.098 6.321 0.161 C 5.858 0.203 5.175 0.624 4.27 1.422 C 3.596 2.035 2.923 2.644 2.25 3.254 L 2.976 4.106 C 3.564 3.664 3.922 3.443 4.048 3.443 C 4.448 3.443 4.637 3.717 4.617 4.263 C 4.617 4.306 4.427 4.968 4.049 6.251 C 3.671 7.534 3.471 8.491 3.449 9.122 C 3.407 9.985 3.565 10.647 3.924 11.109 C 4.367 11.677 5.106 11.919 6.142 11.835 C 7.366 11.751 8.591 11.298 9.816 10.479 C 10.323 10.142 10.808 9.753 11.273 9.311 C 11.105 10.153 10.905 10.868 10.673 11.457 C 8.402 12.487 6.762 13.37 5.752 14.107 C 4.321 15.137 3.554 16.241 3.449 17.419 C 3.259 19.459 4.29 20.479 6.541 20.479 C 8.055 20.479 9.517 19.554 10.926 17.703 C 12.125 16.126 13.166 14.022 14.049 11.394 C 15.578 10.635 16.87 9.892 17.928 9.164 C 17.894 9.409 18.319 7.308 18.24 7.628 Z  M 7.393 16.095 C 7.056 16.095 6.898 15.947 6.919 15.653 C 6.961 15.106 7.908 14.38 9.759 13.476 C 8.791 15.221 8.002 16.095 7.393 16.095 Z"})
    );

    function create_shortcode(sc_args, props, callback) {
        var gt_block = '',
            gutenberg_preview = '';

        if (typeof props.callback != 'undefined' && typeof $[props.callback] == 'function') {
            gt_block = $[props.callback](sc_args, props);
        }

        else {
            var sc_name = props.shortcode_name,
                sc = '[' + sc_name,
                do_shortcode = null;


            $.each(sc_args.attributes, function ($v, $k) {
                if ($v != 'className') {
                    sc += ' ' + $v + '=';
                    var arg = props.attributes[$v],
                        remove_quotes = arg.remove_quotes;

                    if( remove_quotes == true ){
                        sc += $k;
                    }

                    else {
                        sc += '"' + $k + '"';
                    }
                }
            });

            sc += ']';

            var block_id = md5(sc);

            gutenberg_preview = '<span class="yith_block_' + block_id + '">' + sc + '</span>';

            if (callback == 'edit' && props.do_shortcode != false) {
                do_shortcode = (function (block_id) {
                    var ajax_call_date = null;
                    $(document).trigger( 'yith_plugin_fw_gutenberg_before_do_shortcode', [sc, block_id] );
                    $.ajax({
                        async: true,
                        url: yith_gutenberg_ajax.ajaxurl,
                        method: 'post',
                        data: {action: 'yith_plugin_fw_gutenberg_do_shortcode', shortcode: sc},
                        success: function (data) {
                            ajax_call_date = data;
                            if (ajax_call_date != '') {
                                $('.yith_block_' + block_id).html(ajax_call_date);
                                $(document).trigger( 'yith_plugin_fw_gutenberg_success_do_shortcode', [sc, block_id, ajax_call_date] );
                            }
                        }
                    });
                    $(document).trigger( 'yith_plugin_fw_gutenberg_after_do_shortcode', [sc, block_id, ajax_call_date] );
                    return ajax_call_date;
                })(block_id);
            }

            gt_block = el(RawHTML, null, gutenberg_preview);
        }

        return gt_block;
    }

    function onChangeEvent(new_value, attribute_name, args, block_type) {
        var attributes = {};

        if (block_type == 'colorpicker' || block_type == 'color') {
            new_value = new_value.hex;
        }

        attributes[attribute_name] = new_value;
        args.setAttributes(attributes);
        return args;
    }

    $.each(yith_gutenberg, function ($block, $props) {
        registerBlockType("yith/" + $block, {
            title: $props.title,
            description: $props.description,
            category: $props.category,
            attributes: $props.attributes,
            icon: typeof $props.icon != 'undefined' ? $props.icon : yith_icon,
            keywords: $props.keywords,
            edit: function edit(args) {
                var elements = new Array();

                $.each($props.attributes, function ($attribute_name, $attribute_args) {
                    var ComponentControl = null,
                        block_type = $attribute_args.blocktype;
                    if (typeof block_type != 'undefined') {
                        switch (block_type) {
                            case 'select':
                                ComponentControl = SelectControl;
                                break;

                            case 'text':
                                ComponentControl = TextControl;
                                break;

                            case 'textarea':
                                ComponentControl = TextareaControl;
                                break;

                            case 'toggle':
                                ComponentControl = ToggleControl;
                                break;

                            case 'checkbox':
                                ComponentControl = CheckboxControl;
                                break;

                            case 'number':
                            case 'range':
                                ComponentControl = RangeControl;
                                break;

                            case 'color':
                            case 'colorpicker':
                                ComponentControl = ColorPicker;
                                break;

                            case 'radio':
                                ComponentControl = RadioControl;
                                break;
                        }

                        if (ComponentControl != null) {
                            var helpMessageChecked = helpMessageUncheked = '';
                            if (typeof $attribute_args.helps != 'undefined' && typeof $attribute_args.helps.checked != 'undefined' && typeof $attribute_args.helps.unchecked != 'undefined') {
                                helpMessageChecked = $attribute_args.helps.checked;
                                helpMessageUncheked = $attribute_args.helps.unchecked;
                            }

                            else if (typeof $attribute_args.help != 'undefined') {
                                helpMessageChecked = helpMessageUncheked = $attribute_args.help;
                            }

                            elements.push(
                                el(
                                    ComponentControl,
                                    {
                                        value: args.attributes[$attribute_name],
                                        options: $attribute_args.options,
                                        label: $attribute_args.label,
                                        checked: args.attributes[$attribute_name],
                                        selected: args.attributes[$attribute_name],
                                        help: args.attributes[$attribute_name] ? helpMessageChecked : helpMessageUncheked,
                                        disableAlpha: $attribute_args.disableAlpha,
                                        min: $attribute_args.min,
                                        max: $attribute_args.max,
                                        multiple: $attribute_args.multiple,
                                        onChange: function (new_value, attribute_name = $attribute_name) {
                                            args = onChangeEvent(new_value, attribute_name, args, block_type);
                                        },
                                        onChangeComplete: function (new_value, attribute_name = $attribute_name) {
                                            args = onChangeEvent(new_value, attribute_name, args, block_type);
                                        },
                                    },
                                )
                            );
                        }
                    }
                });

                sc = create_shortcode(args, $props, 'edit');

                return [
                    el(
                        Fragment,
                        null,
                        el(
                            InspectorControls,
                            null,
                            elements,
                        ),
                        sc,
                    )];
            },
            save: function save(args) {
                return create_shortcode(args, $props, 'save');
            }
        });
    });
})(jQuery);