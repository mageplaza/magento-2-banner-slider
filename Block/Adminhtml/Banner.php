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

namespace Mageplaza\BannerSlider\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Banner
 * @package Mageplaza\BannerSlider\Block\Adminhtml
 */
class Banner extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'Mageplaza_BannerSlider';
        $this->_headerText = __('Banners');
        $this->_addButtonLabel = __('Create New Banner');

        parent::_construct();
    }
}
