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

namespace Mageplaza\BannerSlider\Block;

/**
 * Class Widget
 * @package Mageplaza\BannerSlider\Block
 */
class Widget extends Slider
{
    /**
     * @return array|bool|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getBannerCollection()
    {
        $sliderId = $this->getData('slider_id');
        if (!$this->helperData->isEnabled() || !$sliderId) {
            return false;
        }

        $sliderCollection = $this->helperData->getActiveSliders();
        $slider           = $sliderCollection->addFieldToFilter('slider_id', $sliderId)->getFirstItem();
        $this->setSlider($slider);

        return parent::getBannerCollection();
    }
}