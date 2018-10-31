<?php
/**
 * Mageplaza
 * NOTICE OF LICENSE
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * @category    Mageplaza
 * @package     Mageplaza_BannerSlider
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BannerSlider\Block;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Mageplaza\BannerSlider\Helper\Data as bannerHelper;

class Slider extends Template
{
    /**
     * @type \Mageplaza\BannerSlider\Helper\Data
     */
    public $helperData;

    /**
     * @type \Magento\Store\Model\StoreManagerInterface
     */
    protected $store;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    public function __construct(
        Template\Context $context,
        bannerHelper $helperData,
        CustomerRepositoryInterface $customerRepository,
        DateTime $dateTime,
        array $data = []
    )
    {
        $this->helperData         = $helperData;
        $this->customerRepository = $customerRepository;
        $this->store              = $context->getStoreManager();
        $this->_date              = $dateTime;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('Mageplaza_BannerSlider::bannerslider.phtml');
    }

    /**
     * Get Slider Id
     * @return string
     */
    public function getSliderId()
    {
        if ($this->getSlider()) {
            return $this->getSlider()->getSliderId();
        }

        return time();
    }

    /**
     * @return \Mageplaza\BannerSlider\Model\ResourceModel\Banner\Collection|null
     */
    public function getBannerCollection()
    {
        if ($sliderId = $this->getSliderId()) {
            $collection = $this->helperData->getBannerCollection($sliderId);
            return $collection;
        }
        return null;
    }

    /**
     * @return string
     */
//    public function getAllOptions()
//    {
//        $sliderOptions = '';
//        $allConfig     = $this->helperData->getModuleConfig('slider_design');
//
//        foreach ($allConfig as $key => $value) {
//            if ($key == 'item_slider') {
//                $sliderOptions = $sliderOptions . $this->getResponsiveConfig();
//            } else if ($key != 'responsive') {
//                if(in_array($key, ['loop', 'nav', 'dots', 'lazyLoad', 'autoplay', 'autoplayHoverPause'])){
//                    $value = $value ? 'true' : 'false';
//                }
//                $sliderOptions = $sliderOptions . $key . ':' . $value . ',';
//            }
//        }
//
//        return '{' . $sliderOptions . '}';
//    }

    /**
     * @return string
     * @throws \Zend_Serializer_Exception
     */
    public function getAllOptions()
    {
        $allOptionsConfig = $this->helperData->getAllOptions();
        $effect = $this->getEffect();

        return '{' . $allOptionsConfig . ',video:true,center:true,autoHeight:true,' . $effect . '}';
    }

    /**
     * @return string
     */
    public function getResponsiveConfig()
    {
        $slider = $this->getSlider();
        if ($slider && $slider->getIsResponsive()) {
            try {
                if ($slider->getIsResponsive() == 2) {
                    return $responsiveConfig = $this->helperData->getResponseValue();
                } else {
                    $responsiveConfig = $slider->getResponsiveItems() ? $this->helperData->unserialize($slider->getResponsiveItems()) : [];
                }
            } catch (\Exception $e) {
                $responsiveConfig = [];
            }

            $responsiveOptions = '';
            foreach ($responsiveConfig as $config) {
                if ($config['size'] && $config['items']) {
                    $responsiveOptions = $responsiveOptions . $config['size'] . ':{items:' . $config['items'] . '},';
                }
            }
            $responsiveOptions = rtrim($responsiveOptions, ',');

            return 'responsive:{' . $responsiveOptions . '}';
        }

        return '';
    }

    public function getEffect()
    {
        $effect = '';
        if ($this->getSlider()) {
            $effect = $this->getSlider()->getEffect() != 'slider' ? 'animateOut: "' . $this->getSlider()->getEffect() . '"' : '';
        }

        return $effect;
    }

    /**
     * Get End of Day Date
     *
     * @return string
     */
    public function getEndOfDayDate()
    {
        return $this->_date->date(null, '23:59:59');
    }

    /**
     * Get Start of Day Date
     *
     * @return string
     */
    public function getStartOfDayDate()
    {
        return $this->_date->date(null, '0:0:0');
    }
}