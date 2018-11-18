/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BannerSlider
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
],function ($, modal, $t){
    "use strict";

    $.widget('mageplaza.bannerslider',{
        options: {
            loadTemplateUrl: ''
        },

        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function () {
            this.initObserve();
        },

        /**
         * Init observe
         */
        initObserve: function () {
            this.loadTemplate();
            this.changeImageUrl();
        },

        /**
         * Change image url
         */
        changeImageUrl: function () {
            $("#banner_default_template").change(function () {
                var imageUrls = JSON.parse($("#banner_images-urls").val());
                $("#mp-demo-image").attr('src', imageUrls[$("#banner_default_template").val()]);
            })
        },

        /**
         * Load template
         */
        loadTemplate: function () {
            var self = this;

            $("#banner_load_template").click(function () {
                var params = {
                    templateId: $("#banner_default_template").val()
                };
                self.sendAjax(self, params, self.options.loadTemplateUrl);
            });
        },

        /**
         * Send Ajax
         * @param params
         * @param url
         */
        sendAjax: function (self, params, url) {
            $.ajax({
                method: 'POST',
                url: url,
                data: params,
                showLoader: true
            }).done(function (response) {
                if (response.status) {
                    document.getElementById("banner_content").setValue(response.templateHtml)
                }
            }).always(function () {
            });
        }
    });
    return $.mageplaza.bannerslider;
});