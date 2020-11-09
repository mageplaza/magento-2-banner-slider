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

namespace Mageplaza\BannerSlider\Block\Adminhtml\Slider\Edit\Tab\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Snippet
 * @package Mageplaza\BannerSlider\Block\Adminhtml\Slider\Edit\Tab\Renderer
 */
class Snippet extends AbstractElement
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        $sliderId = $this->getSliderId() ?? 1;
        $html = '<ul class="banner-location-display"><li><span>';
        $html .= __('Add Widget with name "Banner Slider widget" and set "Slider Id" for it.');
        $html .= '</span></li><li><span>';
        $html .= __('CMS Page/Static Block');
        $html .= '</span><code>{{block class="Mageplaza\BannerSlider\Block\Widget" slider_id="' . $sliderId . '"}}</code><p>';
        $html .= __('You can paste the above block of snippet into any page in Magento 2 and set SliderId for it.');
        $html .= '</p></li><li><span>';
        $html .= __('Template .phtml file');
        $html .= '</span><code>' . $this->_escaper->escapeHtml('<?= $block->getLayout()->createBlock(\'Mageplaza\BannerSlider\Block\Widget\')->setSliderId(' . $sliderId . ')->toHtml();?>') . '</code><p>';
        $html .= __('Open a .phtml file and insert where you want to display Banner Slider.');
        $html .= '</p></li></ul>';

        return $html;
    }
}
