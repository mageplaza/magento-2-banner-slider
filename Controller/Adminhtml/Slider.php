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

namespace Mageplaza\BannerSlider\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\BannerSlider\Model\SliderFactory;

/**
 * Class Slider
 * @package Mageplaza\BannerSlider\Controller\Adminhtml
 */
abstract class Slider extends Action
{
    /**
     * Slider Factory
     *
     * @var SliderFactory
     */
    protected $sliderFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Slider constructor.
     *
     * @param SliderFactory $sliderFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        SliderFactory $sliderFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->sliderFactory = $sliderFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * Init Slider
     *
     * @return \Mageplaza\BannerSlider\Model\Slider
     * @throws LocalizedException
     */
    protected function initSlider()
    {
        $sliderId = (int)$this->getRequest()->getParam('slider_id');
        /** @var \Mageplaza\BannerSlider\Model\Slider $slider */
        $slider = $this->sliderFactory->create();
        if ($sliderId) {
            $slider->load($sliderId);
            if (!$slider->getId()) {
                throw new LocalizedException(__('The wrong slider is specified.'));
            }
        }
        $this->coreRegistry->register('mpbannerslider_slider', $slider);

        return $slider;
    }
}
