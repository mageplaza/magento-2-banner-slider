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

use Mageplaza\BannerSlider\Controller\Adminhtml\Slider;

/**
 * Class Delete
 * @package Mageplaza\BannerSlider\Controller\Adminhtml\Slider
 */
class Delete extends Slider
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            /** @var \Mageplaza\BannerSlider\Model\Banner $banner */
            $this->sliderFactory->create()
                ->load($this->getRequest()->getParam('slider_id'))
                ->delete();
            $this->messageManager->addSuccess(__('The slider has been deleted.'));
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addErrorMessage($e->getMessage());
            // go back to edit form
            $resultRedirect->setPath('mpbannerslider/*/edit', ['slider_id' => $this->getRequest()->getParam('slider_id')]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('mpbannerslider/*/');

        return $resultRedirect;
    }
}
