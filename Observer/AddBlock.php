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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Layout;
use Mageplaza\BannerSlider\Block\Slider;
use Mageplaza\BannerSlider\Helper\Data;
use Mageplaza\BannerSlider\Model\Config\Source\Location;

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
     * @var Data
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
    ) {
        $this->request = $request;
        $this->helperData = $helperData;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if (!$this->helperData->isEnabled()) {
            return $this;
        }

        $type = array_search($observer->getEvent()->getElementName(), [
            'header' => 'header',
            'content' => 'content',
            'page-top' => 'page.wrapper',
            'footer-container' => 'footer-container',
            'sidebar' => 'catalog.leftnav'
        ], true);

        if ($type !== false) {
            /** @var Layout $layout */
            $layout = $observer->getEvent()->getLayout();
            $fullActionName = $this->request->getFullActionName();
            $output = $observer->getTransport()->getOutput();

            foreach ($this->helperData->getActiveSliders() as $slider) {
                $locations = array_filter(explode(',', $slider->getLocation()));
                foreach ($locations as $value) {
                    if ($value === Location::USING_SNIPPET_CODE) {
                        continue;
                    }
                    [$pageType, $location] = explode('.', $value);
                    if (($fullActionName === $pageType || $pageType === 'allpage') &&
                        strpos($location, $type) !== false
                    ) {
                        $content = $layout->createBlock(Slider::class)
                            ->setSlider($slider)
                            ->toHtml();

                        if (strpos($location, 'top') !== false) {
                            if ($type === 'sidebar') {
                                $output = "<div class=\"mp-banner-sidebar\" id=\"mageplaza-bannerslider-block-before-{$type}-{$slider->getId()}\">
                                        $content</div>" . $output;
                            } else {
                                $output = "<div id=\"mageplaza-bannerslider-block-before-{$type}-{$slider->getId()}\">
                                        $content</div>" . $output;
                            }
                        } else {
                            if ($type === 'sidebar') {
                                $output .= "<div class=\"mp-banner-sidebar\" id=\"mageplaza-bannerslider-block-after-{$type}-{$slider->getId()}\">
                                        $content</div>";
                            } else {
                                $output .= "<div id=\"mageplaza-bannerslider-block-after-{$type}-{$slider->getId()}\">
                                        $content</div>";
                            }
                        }
                    }
                }
            }

            $observer->getTransport()->setOutput($output);
        }

        return $this;
    }
}
