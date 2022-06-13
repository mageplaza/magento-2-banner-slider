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

namespace Mageplaza\BannerSlider\Block\Adminhtml\Slider\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\BannerSlider\Block\Adminhtml\Slider\Edit\Tab\Renderer\Responsive;
use Mageplaza\BannerSlider\Model\Config\Source\Effect;

/**
 * Class Design
 * @package Mageplaza\BannerSlider\Block\Adminhtml\Slider\Edit\Tab
 */
class Design extends Generic implements TabInterface
{
    /**
     * @var Effect
     */
    protected $_effect;

    /**
     * @var Yesno
     */
    protected $_yesno;

    /**
     * Design constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Effect $effect
     * @param Yesno $yesno
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Effect $effect,
        Yesno $yesno,
        array $data = []
    ) {
        $this->_effect = $effect;
        $this->_yesno = $yesno;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $slider = $this->_coreRegistry->registry('mpbannerslider_slider');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slider_');
        $form->setFieldNameSuffix('slider');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Design'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('effect', 'select', [
            'name' => 'effect',
            'label' => __('Animation Effect'),
            'title' => __('Animation Effect'),
            'values' => $this->_effect->toOptionArray()
        ]);
        $design = $fieldset->addField('design', 'select', [
            'name' => 'design',
            'label' => __('Manually Design'),
            'title' => __('Manually Design'),
            'options' => [
                '0' => __('Use Config'),
                '1' => __('Yes')
            ]
        ]);
        $responsive = $fieldset->addField('is_responsive', 'select', [
            'name' => 'is_responsive',
            'label' => __('Is Responsive'),
            'title' => __('Is Responsive'),
            'values' => $this->_yesno->toOptionArray()
        ]);
        $responsiveItem = $fieldset->addField('responsive_items', Responsive::class, [
            'name' => 'responsive_items',
            'label' => __('Max Items Slider'),
            'title' => __('Max Items Slider'),
        ]);
        $autoWidth = $fieldset->addField('autoWidth', 'select', [
            'name' => 'autoWidth',
            'label' => __('Auto Width'),
            'title' => __('Auto Width'),
            'values' => $this->_yesno->toOptionArray()
        ]);
        $autoHeight = $fieldset->addField('autoHeight', 'select', [
            'name' => 'autoHeight',
            'label' => __('Auto Height'),
            'title' => __('Auto Height'),
            'values' => $this->_yesno->toOptionArray()
        ]);
        $loop = $fieldset->addField('loop', 'select', [
            'name' => 'loop',
            'label' => __('Infinity Loop'),
            'title' => __('Infinity Loop'),
            'values' => $this->_yesno->toOptionArray()
        ]);
        $nav = $fieldset->addField('nav', 'select', [
            'name' => 'nav',
            'label' => __('Show Next/Prev Buttons'),
            'title' => __('Show Next/Prev Buttons'),
            'values' => $this->_yesno->toOptionArray()
        ]);
        $dots = $fieldset->addField('dots', 'select', [
            'name' => 'dots',
            'label' => __('Show Dots Navigation'),
            'title' => __('Show Dots Navigation'),
            'values' => $this->_yesno->toOptionArray()
        ]);
        $lazyload = $fieldset->addField('lazyLoad', 'select', [
            'name' => 'lazyLoad',
            'label' => __('Lazy Loading Images'),
            'title' => __('Lazy Loading Images'),
            'values' => $this->_yesno->toOptionArray()
        ]);
        $autoplay = $fieldset->addField('autoplay', 'select', [
            'name' => 'autoplay',
            'label' => __('Autoplay'),
            'title' => __('Autoplay'),
            'values' => $this->_yesno->toOptionArray()
        ]);
        $timeout = $fieldset->addField('autoplayTimeout', 'text', [
            'name' => 'autoplayTimeout',
            'label' => __('Autoplay Time-Out'),
            'title' => __('Autoplay Time-Out'),
            'class' => 'validate-number validate-zero-or-greater validate-digits',
        ]);

        $dependencies = $this->getLayout()->createBlock(Dependence::class)
            ->addFieldMap($design->getHtmlId(), $design->getName())
            ->addFieldMap($responsive->getHtmlId(), $responsive->getName())
            ->addFieldMap($responsiveItem->getHtmlId(), $responsiveItem->getName())
            ->addFieldMap($autoWidth->getHtmlId(), $autoWidth->getName())
            ->addFieldMap($autoHeight->getHtmlId(), $autoHeight->getName())
            ->addFieldMap($loop->getHtmlId(), $loop->getName())
            ->addFieldMap($nav->getHtmlId(), $nav->getName())
            ->addFieldMap($dots->getHtmlId(), $dots->getName())
            ->addFieldMap($lazyload->getHtmlId(), $lazyload->getName())
            ->addFieldMap($autoplay->getHtmlId(), $autoplay->getName())
            ->addFieldMap($timeout->getHtmlId(), $timeout->getName())
            ->addFieldDependence($responsive->getName(), $design->getName(), '1')
            ->addFieldDependence($responsiveItem->getName(), $design->getName(), '1')
            ->addFieldDependence($autoWidth->getName(), $design->getName(), '1')
            ->addFieldDependence($autoHeight->getName(), $design->getName(), '1')
            ->addFieldDependence($loop->getName(), $design->getName(), '1')
            ->addFieldDependence($nav->getName(), $design->getName(), '1')
            ->addFieldDependence($dots->getName(), $design->getName(), '1')
            ->addFieldDependence($lazyload->getName(), $design->getName(), '1')
            ->addFieldDependence($autoplay->getName(), $design->getName(), '1')
            ->addFieldDependence($timeout->getName(), $design->getName(), '1')
            ->addFieldDependence($responsiveItem->getName(), $responsive->getName(), '1')
            ->addFieldDependence($timeout->getName(), $autoplay->getName(), '1');

        // define field dependencies
        $this->setChild('form_after', $dependencies);

        $form->addValues($slider->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Design');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }
}
