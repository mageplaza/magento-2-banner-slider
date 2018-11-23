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
    'jquery'
], function($) {
    "use strict";

    $.widget('mageplaza.bannerslider', {
        options: {
            TemplateHtml: ''
        },

        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function() {
            this.initObserve();
        },

        /**
         * Init observe
         */
        initObserve: function() {
            this.loadTemplate();
            this.changeImageUrl();
        },


        /**
         * Load template
         */
        loadTemplate: function() {
            var TemplateHtml = this.options.TemplateHtml,
                bannercontent = $('#banner_content'),
                togglebannercontent = $('#togglebanner_content');
            $("#banner_load_template").click(function() {
                var html = TemplateHtml;
                const regex = /demo\/(.+?).jpg/g;
                let m;

                while ((m = regex.exec(html)) !== null) {
                    // This is necessary to avoid infinite loops with zero-width matches
                    if (m.index === regex.lastIndex) {
                        regex.lastIndex++;
                    }

                    // The result can be accessed through the `m`-variable.
                    if(m[1] !== null) {
                        html = html.replace(m[1], $("#banner_default_template").val());
                    }
                }
                if(bannercontent.css('display') === 'none'){
                    togglebannercontent.trigger('click');
                }
                bannercontent.val(html);
            });
        },

        /**
         * Change image url
         */
        changeImageUrl: function() {
            $("#banner_default_template").change(function() {
                var imageUrls = JSON.parse($("#banner_images-urls").val());
                $("#mp-demo-image").attr('src', imageUrls[$("#banner_default_template").val()]);
            })
        },
    });
    return $.mageplaza.bannerslider;
});