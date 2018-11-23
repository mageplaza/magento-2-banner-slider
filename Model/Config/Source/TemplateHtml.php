<?php
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

namespace Mageplaza\BannerSlider\Model\Config\Source;


class TemplateHtml implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            'image' => '<div class="item" style="background:url({{media url="mageplaza/bannerslider/banner/demo/demo1.jpg"}}) center center no-repeat;background-size:cover;">
                            <div class="container" style="position:relative">
                                <img src="{{media url="mageplaza/bannerslider/banner/demo/demo1.jpg"}}" alt="">
                            </div>
                        </div>'
        ];

        return $options;
    }
}