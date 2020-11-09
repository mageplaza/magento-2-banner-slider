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

namespace Mageplaza\BannerSlider\Block\Adminhtml\Banner\Edit\Tab\Render;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Framework\Escaper;
use Mageplaza\BannerSlider\Helper\Data;
use Mageplaza\BannerSlider\Model\ResourceModel\Slider\Collection;
use Mageplaza\BannerSlider\Model\ResourceModel\Slider\CollectionFactory as SliderCollectionFactory;

/**
 * Class Slider
 * @package Mageplaza\BannerSlider\Block\Adminhtml\Banner\Edit\Tab\Render
 */
class Slider extends Multiselect
{
    /**
     * Authorization
     *
     * @var AuthorizationInterface
     */
    public $authorization;

    /**
     * @var SliderCollectionFactory
     */
    public $collectionFactory;

    /**
     * Slider constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param SliderCollectionFactory $collectionFactory
     * @param AuthorizationInterface $authorization
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        SliderCollectionFactory $collectionFactory,
        AuthorizationInterface $authorization,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->authorization = $authorization;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getElementHtml()
    {
        $html = '<div class="admin__field-control admin__control-grouped">';
        $html .= '<div id="banner-slider-select" class="admin__field" data-bind="scope:\'bannerslider\'" data-index="index">';
        $html .= '<!-- ko foreach: elems() -->';
        $html .= '<input name="banner[sliders_ids]" data-bind="value: value" style="display: none"/>';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '<!-- /ko -->';
        $html .= '</div></div>';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * Attach Blog Tag suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = '<script type="text/x-magento-init">
            {
                "*": {
                    "Magento_Ui/js/core/app": {
                        "components": {
                            "bannerslider": {
                                "component": "uiComponent",
                                "children": {
                                    "banner_select_slider": {
                                        "component": "Magento_Catalog/js/components/new-category",
                                        "config": {
                                            "filterOptions": true,
                                            "disableLabel": true,
                                            "chipsEnabled": true,
                                            "levelsVisibility": "1",
                                            "elementTmpl": "ui/grid/filters/elements/ui-select",
                                            "options": ' . Data::jsonEncode($this->getSliderCollection()) . ',
                                            "value": ' . Data::jsonEncode($this->getValues()) . ',
                                            "config": {
                                                "dataScope": "banner_select_slider",
                                                "sortOrder": 10
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        </script>';

        return $html;
    }

    /**
     * @return mixed
     */
    public function getSliderCollection()
    {
        /* @var $collection Collection */
        $collection = $this->collectionFactory->create();
        $sliderById = [];
        foreach ($collection as $slider) {
            $sliderId = $slider->getId();
            $sliderById[$sliderId]['value'] = $sliderId;
            $sliderById[$sliderId]['is_active'] = 1;
            $sliderById[$sliderId]['label'] = $slider->getName();
        }

        return $sliderById;
    }

    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $values = $this->getValue();

        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        if (empty($values)) {
            return [];
        }

        /* @var $collection Collection */
        $collection = $this->collectionFactory->create()->addIdFilter($values);

        $options = [];
        foreach ($collection as $slider) {
            $options[] = $slider->getId();
        }

        return $options;
    }
}
