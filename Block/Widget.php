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

class Widget extends Slider
{
    /**
     * @return bool|\Mageplaza\BannerSlider\Model\ResourceModel\Banner\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBannerCollection()
    {
        $sliderId = $this->getData('slider_id');
        if (!$this->helperData->isEnabled() || !$sliderId) {
            return false;
        }

        $sliders = $this->helperData->getCustomSlider();
        foreach ($sliders as $slider) {
            if ($slider->getData('location') != 'custom') {
                continue;
            }

            if ($slider->getId() == $sliderId) {
                $this->setSlider($slider);
                break;
            }
        }
        return parent::getBannerCollection();
    }
}