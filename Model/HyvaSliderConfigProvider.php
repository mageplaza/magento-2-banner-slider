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

namespace Mageplaza\BannerSlider\Model;

use Exception;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BannerSlider\Model\Config\Source\Effect;
use Mageplaza\BannerSlider\Helper\Data;

class HyvaSliderConfigProvider
{
    /**
     * @var BannerFactory
     */
    protected BannerFactory $bannerFactory;
    /**
     * @var SliderFactory
     */
    protected SliderFactory $sliderFactory;
    /**
     * @var DateTime
     */
    protected DateTime $date;
    /**
     * @var HttpContext
     */
    protected HttpContext $httpContext;
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;
    /**
     * @var ObjectManagerInterface
     */
    protected ObjectManagerInterface $objectManager;
    /**
     * @var Data
     */
    protected Data $mpHelper;

    /**
     * @param DateTime $date
     * @param HttpContext $httpContext
     * @param BannerFactory $bannerFactory
     * @param SliderFactory $sliderFactory
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     * @param Data $mpHelper
     */
    public function __construct(
        DateTime               $date,
        HttpContext            $httpContext,
        BannerFactory          $bannerFactory,
        SliderFactory          $sliderFactory,
        StoreManagerInterface  $storeManager,
        ObjectManagerInterface $objectManager,
        Data                   $mpHelper
    )
    {
        $this->date = $date;
        $this->httpContext = $httpContext;
        $this->bannerFactory = $bannerFactory;
        $this->sliderFactory = $sliderFactory;
        $this->storeManager = $storeManager;
        $this->objectManager = $objectManager;
        $this->mpHelper = $mpHelper;
    }

    /**
     * @param Slider $slider
     * @return string
     */
    public function getBannerOptions(Slider $slider)
    {
        if ($slider->getDesign()) { //not use Config
            $config = $slider->getData();
        } else {
            $config = $this->mpHelper->getModuleConfig('mpbannerslider_design');
        }

        $defaultOpt = $this->setupDefaultConfig($config);
        $responsiveOpt = $this->setupResponsiveConfig($slider);
        $effectOpt = $this->setupEffectConfig($slider, $defaultOpt);

        $sliderOptions = array_merge($defaultOpt, $responsiveOpt, $effectOpt);
        $sliderOptions = $this->reCalcSliderOpt($sliderOptions);

        return Data::jsonEncode($sliderOptions);
    }

    /**
     * @param $sliderOptions
     * @return mixed
     */
    public function reCalcSliderOpt($sliderOptions)
    {
        /**
         * splidejs
         * If true, the width/height of slides are determined by their width/height.
         * Do not provide perPage and perMove options (or set them to 1).
         */
        if (
            ($sliderOptions['autoWidth'] || $sliderOptions['autoHeight']) &&
            (
                (($sliderOptions['perPage'] ?? 1) > 1) ||
                isset($sliderOptions['breakpoints'])
            )
        ) {
            unset($sliderOptions['perPage'], $sliderOptions['breakpoints'], $sliderOptions['mediaQuery']);
        }
        /**
         * splidejs
         * A carousel with the fade transition. This does not support the perPage option
         * To keep the Loop, remove the fade transition
         */
        if (
            $sliderOptions['type'] === 'fade' &&
            (
                (($sliderOptions['perPage'] ?? 1) > 1) ||
                isset($sliderOptions['breakpoints'])
            )
        ) {
            $sliderOptions['type'] = 'loop';
            unset($sliderOptions['rewind']);
        }

        return $sliderOptions;
    }

    /**
     * @param $configs
     * @return array
     */
    public function setupDefaultConfig($configs)
    {
        $basicConfig = ['perMove' => 1];
        foreach ($configs as $key => $value) {
            switch ($key) {
                case 'autoWidth':
                case 'autoHeight':
                case 'autoplay':
                    $basicConfig[$key] = (bool) $value;
                    break;
                case 'loop':
                    $basicConfig['type'] = (bool) $value ? 'loop' : 'slide';
                    break;
                case 'nav':
                    $basicConfig['arrows'] = (bool) $value;
                    break;
                case 'dots':
                    $basicConfig['pagination'] = (bool) $value;
                    break;
                case 'lazyLoad':
                    $basicConfig['lazyLoad'] = (bool) $value ? 'nearby' : false;
                    break;
                case 'autoplayTimeout':
                    $basicConfig['interval'] = (int) $value;
                    break;
            }
        }
        return $basicConfig;
    }

    /**
     * @param $slider
     * @return array|int[]
     */
    public function setupResponsiveConfig($slider = null)
    {
        $defaultResponsive = $this->mpHelper->getModuleConfig('mpbannerslider_design/responsive');
        $sliderResponsive  = $slider ? $slider->getIsResponsive() : false;

        if ((!$defaultResponsive && !$sliderResponsive) || ($slider && !$sliderResponsive && $slider->getDesign())) {
            return ['perPage' => 1];
        }

        $responsiveItemsValue = ($slider && $slider->getDesign())
            ? $slider->getResponsiveItems()
            : $this->mpHelper->getModuleConfig('mpbannerslider_design/item_slider');

        try {
            $responsiveItems = $this->mpHelper->unserialize($responsiveItemsValue);
        } catch (Exception $e) {
            $responsiveItems = [];
        }

        $breakpoints = [];
        foreach ($responsiveItems as $config) {
            $size = isset($config['size']) && $config['size'] ? (int)$config['size'] : 0;
            if (!$size) {
                continue;
            }
            $items = isset($config['items']) && $config['items'] ? (int)$config['items'] : 1;
            $breakpoints[$size] = ['perPage' => $items];
        }

        return [
            'perPage'       => 1,
            'mediaQuery'    => 'min',
            'breakpoints'   => $breakpoints
        ];
    }

    /**
     * @param $slider
     * @param $defaultOpt
     * @return array|string[]
     */
    public function setupEffectConfig($slider, $defaultOpt)
    {
        if (!$slider || $slider->getEffect() === Effect::SLIDER) return [];

        $effect = $slider->getEffect();
        if ($effect === Effect::FADE_OUT) {
            $config = ['type' => 'fade'];
            if ($defaultOpt['type'] === 'loop') {
                $config['rewind'] = true;
            }
            return $config;
        }

        return ['customEffect' => $effect];
    }

    /**
     * @param $id
     * @return AbstractDb|AbstractCollection|null
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
     * @return Data
     */
    public function getMpHelper()
    {
        return $this->mpHelper;
    }
}
