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

use Mageplaza\BannerSlider\Helper\Data;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
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

        $this->_formScripts[] = "
    require(['jquery'], function($){
        var element = $('#slider_location'),
            widgetGuide = " . $this->getLocationNote() . ";
        
        changeNote(element.val());
        
        element.change(function () {
            changeNote($(this).val());
        });
        
        function changeNote(option) {
            var optionNote = widgetGuide.hasOwnProperty(option) ? widgetGuide[option] : widgetGuide['default'];
            $('#location-note').html(optionNote);
        }
    })";
    }

    /**
     * Get Html of Widget Guide
     *
     * @return string
     */
    public function getLocationNote()
    {
        $model  = $slider = $this->getSlider();
        $sliderId = $model->getId() ?: '1';

        $customHtml = <<<HTML
<h3>How to use</h3>
<ul class="banner-location-display">
    <li>
        <span>Add Widget with name "Banner Slider widget" and set "Slider Id" for it.</span>
        </li>
    <li>
        <span>CMS Page, CMS Static Block</span>
        <code>{{block class="Mageplaza\BannerSlider\Block\Widget" slider_id="{$sliderId}"}}</code>
        <p>You can paste the above block of snippet into any page in Magento 2 and set SliderId for it.</p>
    </li>
    <li>
        <span>Template .phtml file</span>
        <code>{$this->_escaper->escapeHtml('<?php echo $block->getLayout()->createBlock(\Mageplaza\BannerSlider\Block\Widget::class)->setSliderId(' . $sliderId . ')->toHtml();?>')}</code>
        <p>Open a .phtml file and insert where you want to display Banner Slider.</p>
    </li>
</ul>
HTML;
        $noteGuide  = [
            'default'             => 'Select the position to display block.',
            'custom'              => $customHtml,
        ];

        return Data::jsonEncode($noteGuide);
    }

    /**
     * Retrieve text for header element depending on loaded Slider
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Mageplaza\BannerSlider\Model\Slider $slider */
        $slider = $this->getSlider();
        if ($slider->getId()) {
            return __("Edit Slider '%1'", $this->escapeHtml($slider->getName()));
        }
        return __('New Slider');
    }

    public function getSlider()
    {
        $slider = $this->coreRegistry->registry('mpbannerslider_slider');

        return $slider;
    }
}
