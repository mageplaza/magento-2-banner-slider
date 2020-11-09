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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Widget
 * @package Mageplaza\BannerSlider\Block
 */
class Widget extends Slider implements BlockInterface
{
    /**
     * @return array|AbstractCollection
     * @throws NoSuchEntityException
     */
    public function getBannerCollection()
    {
        $sliderId = $this->getData('slider_id');
        if (!$sliderId || !$this->helperData->isEnabled()) {
            return [];
        }

        $sliderCollection = $this->helperData->getActiveSliders();
        $slider = $sliderCollection->addFieldToFilter('slider_id', $sliderId)->getFirstItem();
        $this->setSlider($slider);

        return parent::getBannerCollection();
    }
}
