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

use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BannerSlider\Model\BannerFactory;
use Mageplaza\BannerSlider\Model\Config\Source\Effect;
use Mageplaza\BannerSlider\Model\ResourceModel\Banner\Collection;
use Mageplaza\BannerSlider\Model\Slider;
use Mageplaza\BannerSlider\Model\SliderFactory;
use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\BannerSlider\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpbannerslider';

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

    /**
     * Data constructor.
     *
     * @param DateTime $date
     * @param Context $context
     * @param HttpContext $httpContext
     * @param BannerFactory $bannerFactory
     * @param SliderFactory $sliderFactory
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        DateTime $date,
        Context $context,
        HttpContext $httpContext,
        BannerFactory $bannerFactory,
        SliderFactory $sliderFactory,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager
    ) {
        $this->date = $date;
        $this->httpContext = $httpContext;
        $this->bannerFactory = $bannerFactory;
        $this->sliderFactory = $sliderFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param Slider $slider
     *
     * @return false|string
     */
    public function getBannerOptions($slider)
    {
        if ($slider && $slider->getDesign() === '1') { //not use Config
            $config = $slider->getData();
        } else {
            $config = $this->getModuleConfig('mpbannerslider_design');
        }

        $defaultOpt = $this->getDefaultConfig($config);
        $responsiveOpt = $this->getResponsiveConfig($slider);
        $effectOpt = $this->getEffectConfig($slider);

        $sliderOptions = array_merge($defaultOpt, $responsiveOpt, $effectOpt);

        return self::jsonEncode($sliderOptions);
    }

    /**
     * @param array $configs
     *
     * @return array
     */
    public function getDefaultConfig($configs)
    {
        $basicConfig = [];
        foreach ($configs as $key => $value) {
            if (in_array(
                $key,
                ['autoWidth', 'autoHeight', 'loop', 'nav', 'dots', 'lazyLoad', 'autoplay', 'autoplayTimeout']
            )) {
                $basicConfig[$key] = (int)$value;
            }
        }

        return $basicConfig;
    }

    /**
     * @param null $slider
     *
     * @return array
     */
    public function getResponsiveConfig($slider = null)
    {
        $defaultResponsive = $this->getModuleConfig('mpbannerslider_design/responsive');
        $sliderResponsive = $slider->getIsResponsive();

        if ((!$defaultResponsive && !$sliderResponsive) || (!$sliderResponsive && $slider->getDesign())) {
            return ['items' => 1];
        }

        $responsiveItemsValue = $slider->getDesign()
            ? $slider->getResponsiveItems()
            : $this->getModuleConfig('mpbannerslider_design/item_slider');

        try {
            $responsiveItems = $this->unserialize($responsiveItemsValue);
        } catch (Exception $e) {
            $responsiveItems = [];
        }

        $result = [];
        foreach ($responsiveItems as $config) {
            $size = $config['size'] ?: 0;
            $items = $config['items'] ?: 0;
            $result[$size] = ['items' => $items];
        }

        return ['responsive' => $result];
    }

    /**
     * @param $slider
     *
     * @return array
     */
    public function getEffectConfig($slider)
    {
        if (!$slider) {
            return [];
        }

        if ($slider->getEffect() === Effect::SLIDER) {
            return [];
        }

        return ['animateOut' => $slider->getEffect()];
    }

    /**
     * @param null $id
     *
     * @return Collection
     */
    public function getBannerCollection($id = null)
    {
        $collection = $this->bannerFactory->create()->getCollection();

        $collection->join(
            ['banner_slider' => $collection->getTable('mageplaza_bannerslider_banner_slider')],
            'main_table.banner_id=banner_slider.banner_id AND banner_slider.slider_id=' . $id,
            ['position']
        );

        $collection->addOrder('position', 'ASC');

        return $collection;
    }

    /**
     * @return \Mageplaza\BannerSlider\Model\ResourceModel\Slider\Collection
     * @throws NoSuchEntityException
     */
    public function getActiveSliders()
    {
        /** @var \Mageplaza\BannerSlider\Model\ResourceModel\Slider\Collection $collection */
        $collection = $this->sliderFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_group_ids', [
                'finset' => $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP)
            ])
            ->addFieldToFilter('status', 1)
            ->addOrder('priority');

        $collection->getSelect()
            ->where('FIND_IN_SET(0, store_ids) OR FIND_IN_SET(?, store_ids)', $this->storeManager->getStore()->getId())
            ->where('from_date is null OR from_date <= ?', $this->date->date())
            ->where('to_date is null OR to_date >= ?', $this->date->date());

        return $collection;
    }
}
