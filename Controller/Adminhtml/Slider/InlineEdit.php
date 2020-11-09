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

namespace Mageplaza\BannerSlider\Controller\Adminhtml\Slider;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\BannerSlider\Model\Slider;
use Mageplaza\BannerSlider\Model\SliderFactory;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Mageplaza\BannerSlider\Controller\Adminhtml\Slider
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * Banner Factory
     *
     * @var SliderFactory
     */
    protected $sliderFactory;

    /**
     * InlineEdit constructor.
     *
     * @param JsonFactory $jsonFactory
     * @param SliderFactory $sliderFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        SliderFactory $sliderFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->sliderFactory = $sliderFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!(!empty($postItems) && $this->getRequest()->getParam('isAjax'))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($postItems) as $sliderId) {
            /** @var Slider $slider */
            $slider = $this->sliderFactory->create()->load($sliderId);
            try {
                $sliderData = $postItems[$sliderId];
                $slider->addData($sliderData);
                $slider->save();
            } catch (RuntimeException $e) {
                $messages[] = $this->getErrorWithSliderId($slider, $e->getMessage());
                $error = true;
            } catch (Exception $e) {
                $messages[] = $this->getErrorWithSliderId(
                    $slider,
                    __('Something went wrong while saving the Banner.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add slider id to error message
     *
     * @param Slider $slider
     * @param $errorText
     *
     * @return string
     */
    protected function getErrorWithSliderId(Slider $slider, $errorText)
    {
        return '[Slider ID: ' . $slider->getId() . '] ' . $errorText;
    }
}
