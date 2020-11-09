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

namespace Mageplaza\BannerSlider\Block\Adminhtml\Slider;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mageplaza\BannerSlider\Model\Slider;

/**
 * Class Edit
 * @package Mageplaza\BannerSlider\Block\Adminhtml\Slider
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * constructor
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Slider edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'slider_id';
        $this->_blockGroup = 'Mageplaza_BannerSlider';
        $this->_controller = 'adminhtml_slider';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Slider'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Slider'));
    }

    /**
     * Retrieve text for header element depending on loaded Slider
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Slider $slider */
        $slider = $this->getSlider();
        if ($slider->getId()) {
            return __("Edit Slider '%1'", $this->escapeHtml($slider->getName()));
        }

        return __('New Slider');
    }

    /**
     * @return mixed
     */
    public function getSlider()
    {
        return $this->coreRegistry->registry('mpbannerslider_slider');
    }
}
