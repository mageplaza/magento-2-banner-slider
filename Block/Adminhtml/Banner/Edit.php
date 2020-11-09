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

namespace Mageplaza\BannerSlider\Block\Adminhtml\Banner;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mageplaza\BannerSlider\Model\Banner;

/**
 * Class Edit
 * @package Mageplaza\BannerSlider\Block\Adminhtml\Banner
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
        parent::__construct($context, $data);

        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Initialize Banner edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'banner_id';
        $this->_blockGroup = 'Mageplaza_BannerSlider';
        $this->_controller = 'adminhtml_banner';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Banner'));
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
        $this->buttonList->update('delete', 'label', __('Delete Banner'));
    }

    /**
     * Retrieve text for header element depending on loaded Banner
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Banner $banner */
        $banner = $this->getBanner();
        if ($banner->getId()) {
            return __("Edit Banner '%1'", $this->escapeHtml($banner->getName()));
        }

        return __('New Banner');
    }

    /**
     * @return mixed
     */
    public function getBanner()
    {
        return $this->coreRegistry->registry('mpbannerslider_banner');
    }
}
