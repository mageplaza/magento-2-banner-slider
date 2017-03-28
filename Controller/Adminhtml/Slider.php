<?php
/**
 * Mageplaza_BetterSlider extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the Mageplaza License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 * 
 *                     @category  Mageplaza
 *                     @package   Mageplaza_BetterSlider
 *                     @copyright Copyright (c) 2016
 *                     @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\BetterSlider\Controller\Adminhtml;

abstract class Slider extends \Magento\Backend\App\Action
{
    /**
     * Slider Factory
     * 
     * @var \Mageplaza\BetterSlider\Model\SliderFactory
     */
    protected $sliderFactory;

    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     * 
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */

    /**
     * constructor
     * 
     * @param \Mageplaza\BetterSlider\Model\SliderFactory $sliderFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mageplaza\BetterSlider\Model\SliderFactory $sliderFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->sliderFactory         = $sliderFactory;
        $this->coreRegistry          = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init Slider
     *
     * @return \Mageplaza\BetterSlider\Model\Slider
     */
    protected function initSlider()
    {
        $sliderId  = (int) $this->getRequest()->getParam('slider_id');
        /** @var \Mageplaza\BetterSlider\Model\Slider $slider */
        $slider    = $this->sliderFactory->create();
        if ($sliderId) {
            $slider->load($sliderId);
        }
        $this->coreRegistry->register('mageplaza_betterslider_slider', $slider);
        return $slider;
    }
}
