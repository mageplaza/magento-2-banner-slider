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

namespace Mageplaza\BannerSlider\Controller\Adminhtml\Banner;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\BannerSlider\Model\Banner;
use Mageplaza\BannerSlider\Model\BannerFactory;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Mageplaza\BannerSlider\Controller\Adminhtml\Banner
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
     * @var BannerFactory
     */
    protected $bannerFactory;

    /**
     * constructor
     *
     * @param JsonFactory $jsonFactory
     * @param BannerFactory $bannerFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        BannerFactory $bannerFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->bannerFactory = $bannerFactory;

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
        foreach (array_keys($postItems) as $bannerId) {
            /** @var Banner $banner */
            $banner = $this->bannerFactory->create()->load($bannerId);
            try {
                $bannerData = $postItems[$bannerId];//todo: handle dates
                $banner->addData($bannerData);
                $banner->save();
            } catch (RuntimeException $e) {
                $messages[] = $this->getErrorWithBannerId($banner, $e->getMessage());
                $error = true;
            } catch (Exception $e) {
                $messages[] = $this->getErrorWithBannerId(
                    $banner,
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
     * Add Banner id to error message
     *
     * @param Banner $banner
     * @param string $errorText
     *
     * @return string
     */
    protected function getErrorWithBannerId(Banner $banner, $errorText)
    {
        return '[Banner ID: ' . $banner->getId() . '] ' . $errorText;
    }
}
