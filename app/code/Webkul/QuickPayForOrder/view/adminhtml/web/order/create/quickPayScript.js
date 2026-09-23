/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_QuickPayForOrder
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define([
    "jquery",
    "mage/apply/main",
    "mage/template",
    "Magento_Ui/js/modal/alert",
    "mage/translate",
    "mage/mage",
    "prototype"
], function ($, main, mageTemplate, alert) {
    var self = this;
    var count = 0;
    main.apply();
    $(document).ready(function() {
        if (order.quickpay == undefined || order.quickpay == false) {
            $('.wk-hidden').hide();
            if ($('#order-data').is(':visible')) {
                $('.quick_pay_order_main_submit').show();
            }
        } else if (order.quickpay == 1) {
            $('.quick_pay_order_main_submit').hide();
            $('#send-order-link').trigger('click');
            $('.admin__payment-methods-radio_button').hide();
        } else {
            $('.quick_pay_order_main_submit').show();
            $('#send-order-link').prop('checked', false);
            $('.wk-hidden').hide();
            if ($('#order-data').is(':visible')) {
                $('.admin__payment-methods-radio_button').show();
            }
        }
        $(document).on('change', '#send-order-link', function() {
            if ($(this).prop('checked') == true) {
                order.quickpay = 1;
                window.quickpay = 1;
                if (order.quickpayAllowAllShipping == 0) {
                    order.quickpayAllowAllShipping = 0;
                } else {
                    order.quickpayAllowAllShipping = 1;
                }

                if (order.quickpayAllowAllPayment == 0) {
                    order.quickpayAllowAllPayment = 0;
                } else {
                    order.quickpayAllowAllPayment = 1;
                }

                payment.switchMethod('checkmo');
                if (window.selectedPayments && window.selectedPayments.length > 0) {
                    $.each(window.selectedPayments, function(i, v) {
                        let value = v.value;
                        $('input[type=checkbox][value='+value+']').prop("checked", true);
                    });
                } else {
                    $('.wk-payment-methods-checkbox').prop('checked', true);
                }
                $('.admin__payment-methods-radio_button').hide();
                $('.wk-hidden').show();
                $('.quick_pay_order_main_submit').hide();

                if (order.isOnlyVirtualProduct) {
                    $("#allow-all-shipping-block").hide();
                }

                if (order.quickpayAllowAllShipping) {
                    $("#allow-all-shipping").prop('checked', true).trigger('change');
                    $("#order-shipping_method").hide();
                } else {
                    $("#order-shipping_method").show();
                }

                if (order.quickpayAllowAllPayment) {
                    $("#allow-all-payment").prop('checked', true).trigger('change');
                    $("#payement-list-with-checkbox").hide();
                } else {
                    $("#payement-list-with-checkbox").show();
                }
            } else {
                order.quickpay = 0;
                window.quickpay = 0;
                $("#order-shipping_method").show();
                $('.admin__payment-methods-radio_button .admin__field-option').find('input[type=radio]:checked').trigger('click');
                $('.admin__payment-methods-radio_button').show();
                $('.wk-hidden').hide();
                $('.quick_pay_order_main_submit').show();
            }
        });

        $(document).on('change', '#allow-all-payment', function() {
            if ($(this).prop('checked') == true) {
                order.quickpayAllowAllPayment = 1;
                $("#payement-list-with-checkbox").hide();
            } else {
                order.quickpayAllowAllPayment = 0;
                $("#payement-list-with-checkbox").show();
            }
        });

        $(document).on('change', '#allow-all-shipping', function() {
            if (!order.isOnlyVirtualProduct) {
                if ($(this).prop('checked') == true) {
                    if ($("input[name='order[has_shipping]']").length) {
                        if ($("input[name='order[shipping_method]']").length) {
                            $("input[name='order[shipping_method]']:first").trigger('click');
                            order.quickpayAllowAllShipping = 1;
                            $("#order-shipping_method").hide();
                        } else {
                            order.loadShippingRates();
                            if ($("input[name='order[shipping_method]']").length) {
                                $("input[name='order[shipping_method]']:first").trigger('click');
                                order.quickpayAllowAllShipping = 1;
                                $("#order-shipping_method").hide();
                            }
                        }
                    } else {
                        if ($("input[name='order[shipping_method]']:checked").length) {
                            order.quickpayAllowAllShipping = 1;
                            $("#order-shipping_method").hide();
                        } else {
                            if ($("input[name='order[shipping_method]']").length) {
                                $("input[name='order[shipping_method]']:first").trigger('click');
                                order.quickpayAllowAllShipping = 1;
                                $("#order-shipping_method").hide();
                            }
                        }
                    }
                } else {
                    order.quickpayAllowAllShipping = 0;
                    $("#order-shipping_method").show();
                    order.loadShippingRates();
                }
            }
        })

        $(document).on('change', '.wk-payment-methods-checkbox', function() {
            let selectedPayments = $('.wk-payment-methods-checkbox').serializeArray();
            window.selectedPayments = selectedPayments;
        });

        $('#service-model').delegate('#add-service-row', 'click', function () {
            count++ ;
            var progressTmpl = mageTemplate('#service-row-template'),
                tmpl;
            tmpl = progressTmpl({
                data: {
                    index: count,
                }
            });
            $('#model-form-table #service-tbody-row').append(tmpl);
        });
        $('#model-form-table').delegate('.remove-service-row', 'click', function () {
            $(this).parents('.service-row-tr').remove();
        });
        $('#model-form-table').delegate('.wkcheckjs', 'blur', function() {
            var input = $(this).val();
            var value = String(input)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
            $(this).val(value);
        });

        $(document).on('mousedown', '.quick_pay_order', function() {
            $('#send_quick_pay').val(1);
            if (!order.isOnlyVirtualProduct) {
                if ($("input[name='order[has_shipping]']").length) {
                    order.loadShippingRates();
                    alert({
                        content: $.mage.__('Select Shipping Method First')
                    });
                } else {
                    if ($("input[name='order[shipping_method]']:checked").length) {
                        submitOrder();
                    } else {
                        alert({
                            content: $.mage.__('Select Shipping Method First')
                        });
                    }
                }
            } else {
                // order.loadShippingRates();
                submitOrder();
            }
        });
    });

    function submitOrder() {
        if ($('.wk-payment-methods-checkbox:checked').length > 0) {
            order.submit();
        } else {
            alert({
                content: $.mage.__('Select Payment Methods to allow at checkout page')
            });
        }
    }
});
