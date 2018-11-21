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
use Magento\Framework\UrlInterface;
use Mageplaza\BannerSlider\Model\ResourceModel\Slider\CollectionFactory as SliderCollectionFactory;

class Slider extends Multiselect
{
    /**
     * Authorization
     *
     * @var \Magento\Framework\AuthorizationInterface
     */
    public $authorization;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

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
     * @param UrlInterface $urlInterface
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        SliderCollectionFactory $collectionFactory,
        AuthorizationInterface $authorization,
        UrlInterface $urlInterface,
        array $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->authorization     = $authorization;
        $this->_urlBuilder       = $urlInterface;

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
        $html .= '</div>';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * Get no display
     *
     * @return bool
     */
    public function getNoDisplay()
    {
        $isNotAllowed = !$this->authorization->isAllowed('Mageplaza_BannerSlider::slider');

        return $this->getData('no_display') || $isNotAllowed;
    }

    /**
     * @return mixed
     */
    public function getSliderCollection()
    {
        /* @var $collection \Mageplaza\BannerSlider\Model\ResourceModel\Slider\Collection */
        $collection = $this->collectionFactory->create();
        $sliderById = [];
        foreach ($collection as $slider) {
            $sliderById[$slider->getId()]['value']     = $slider->getId();
            $sliderById[$slider->getId()]['is_active'] = 1;
            $sliderById[$slider->getId()]['label']     = $slider->getName();

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

        if (!sizeof($values)) {
            return [];
        }

        /* @var $collection \Mageplaza\BannerSlider\Model\ResourceModel\Slider\Collection */
        $collection = $this->collectionFactory->create()->addIdFilter($values);

        $options = [];
        foreach ($collection as $slider) {
            $options[] = $slider->getId();
        }

        return $options;
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
                                        "component": "Mageplaza_BannerSlider/js/components/new-category",
                                        "config": {
                                            "filterOptions": true,
                                            "disableLabel": true,
                                            "chipsEnabled": true,
                                            "levelsVisibility": "1",
                                            "elementTmpl": "ui/grid/filters/elements/ui-select",
                                            "options": ' . json_encode($this->getSliderCollection()) . ',
                                            "value": ' . json_encode($this->getValues()) . ',
                                            "listens": {
                                                "index=create_tag:responseData": "setParsed",
                                                "newOption": "toggleOptionSelected"
                                            },
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
}