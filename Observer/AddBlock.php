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

namespace Mageplaza\BannerSlider\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\BannerSlider\Helper\Data;

/**
 * Class AddBlock
 * @package Mageplaza\AutoRelated\Observer
 */
class AddBlock implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Mageplaza\BannerSlider\Helper\Data
     */
    protected $helperData;

    /**
     * AddBlock constructor.
     *
     * @param RequestInterface $request
     * @param Data $helperData
     */
    public function __construct(
        RequestInterface $request,
        Data $helperData
    )
    {
        $this->request    = $request;
        $this->helperData = $helperData;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (!$this->helperData->isEnabled()) {
            return $this;
        }

        $type = array_search($observer->getEvent()->getElementName(), [
            'header'           => 'header',
            'content'          => 'content',
            'page-top'         => 'page.top',
            'footer-container' => 'footer-container',
            'sidebar'          => 'catalog.leftnav'
        ]);

        if ($type !== false) {
            /** @var \Magento\Framework\View\Layout $layout */
            $layout         = $observer->getEvent()->getLayout();
            $fullActionName = $this->request->getFullActionName();
            $output         = $observer->getTransport()->getOutput();

            foreach ($this->helperData->getActiveSliders() as $slider) {
                $locations = explode(",", $slider->getLocation());
                foreach ($locations as $value) {
                    list($pageType, $location) = explode('.', $value);
                    if (($fullActionName == $pageType || $pageType == 'allpage') &&
                        strpos($location, $type) !== false
                    ) {
                        $content = $layout->createBlock(\Mageplaza\BannerSlider\Block\Slider::class)
                            ->setSlider($slider)
                            ->toHtml();

                        if (strpos($location, 'top') !== false) {
                            $output = "<div id=\"mageplaza-bannerslider-block-before-{$type}-{$slider->getId()}\">$content</div>" . $output;
                        } else {
                            $output .= "<div id=\"mageplaza-bannerslider-block-after-{$type}-{$slider->getId()}\">$content</div>";
                        }
                    }
                }
            }

            $observer->getTransport()->setOutput($output);
        }

        return $this;
    }
}
