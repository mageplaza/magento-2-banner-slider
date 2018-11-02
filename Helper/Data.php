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
 * @package     Mageplaza_Bannerslider
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\BannerSlider\Helper;

use Magento\Framework\App\Helper\Context;
use Mageplaza\Core\Helper\AbstractData;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Mageplaza\BannerSlider\Model\BannerFactory;
use Mageplaza\BannerSlider\Model\SliderFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Http\Context as HttpContext;

class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'bannerslider';

    /**
     * @var BannerFactory
     */
    public $bannerFactory;

    /**
     * @var SliderFactory
     */
    public $sliderFactory;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        BannerFactory $bannerFactory,
        SliderFactory $sliderFactory,
        DateTime $date,
        HttpContext $httpContext
    )
    {
        $this->bannerFactory = $bannerFactory;
        $this->sliderFactory = $sliderFactory;
        $this->date          = $date;
        $this->httpContext   = $httpContext;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Retrieve all configuration options for banner slider
     *
     * @return string
     * @throws \Zend_Serializer_Exception
     */
    public function getAllOptions()
    {
        $sliderOptions = '';
        $allConfig     = $this->getModuleConfig('slider_design');
        foreach ($allConfig as $key => $value) {
            if ($key == 'item_slider') {
                $sliderOptions = $sliderOptions . $this->getResponseValue();
            } else if ($key != 'responsive') {
                if(in_array($key, ['autoWidth','autoHeight','loop', 'nav', 'dots', 'lazyLoad', 'autoplay', 'autoplayHoverPause'])){
                    $value = $value ? 'true' : 'false';
                }
                $sliderOptions = $sliderOptions . $key . ':' . $value . ',';
            }
        }

        return $sliderOptions;
    }

    /**
     * @return bool
     */
    public function isResponsive()
    {
        if ($this->getModuleConfig('slider_design/responsive') == 1) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve responsive values for banner slider
     *
     * @return string
     * @throws \Zend_Serializer_Exception
     */
    public function getResponseValue()
    {
        $responsiveOptions = '';

        if ($this->isResponsive()) {
            $responsiveConfig = $this->unserialize($this->getModuleConfig('slider_design/item_slider'));

            foreach ($responsiveConfig as $config) {
                if ($config['size'] && $config['items']) {
                    $responsiveOptions = $responsiveOptions . $config['size'] . ':{items:' . $config['items'] . '},';
                }
            }

            $responsiveOptions = rtrim($responsiveOptions, ',');

            return 'responsive:{' . $responsiveOptions . '}';
        }
        else return 'items: 1';
    }

    /**
     * @param null $id
     * @return \Mageplaza\BannerSlider\Model\ResourceModel\Banner\Collection
     */
    public function getBannerCollection($id = null)
    {

        $collection = $this->bannerFactory->create()->getCollection();

        $collection->join(
            ['banner_slider' => $collection->getTable('mageplaza_bannerslider_banner_slider')],
            'main_table.banner_id=banner_slider.banner_id AND banner_slider.slider_id=' . $id,
            ['position']
        );

        $collection->addOrder('position','ASC');

        return $collection;
    }

    /**
     * @return Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getActiveSliders()
    {
        /** @var Collection $collection */
        $collection = $this->sliderFactory->create()
                                          ->getCollection()
                                          ->addFieldToFilter('customer_group_ids', ['finset' => $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP)])
                                          ->addFieldToFilter('status', 1)
                                          ->addOrder('priority');

        $collection->getSelect()
                   ->where('FIND_IN_SET(0, store_ids) OR FIND_IN_SET(?, store_ids)', $this->storeManager->getStore()->getId())
                   ->where('from_date is null OR from_date <= ?', $this->date->date())
                   ->where('to_date is null OR to_date >= ?', $this->date->date());

        return $collection;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomSlider()
    {
        $collection = $this->getActiveSliders()->addFieldToFilter('location','custom');

        return $collection;
    }
}
