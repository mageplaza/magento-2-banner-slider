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

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem;
use Mageplaza\BannerSlider\Helper\Data as HelperData;

class Load extends Action
{
    /**
     * @type \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Mageplaza\BannerSlider\Helper\Data
     */
    protected $helperData;

    /**
     * Load constructor.
     *
     * @param Action\Context $context
     * @param Filesystem $filesystem
     * @param JsonFactory $resultJsonFactory
     * @param HelperData $helperData
     */
    public function __construct(
        Action\Context $context,
        Filesystem $filesystem,
        JsonFactory $resultJsonFactory,
        HelperData $helperData
    )
    {
        $this->filesystem        = $filesystem;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperData        = $helperData;

        parent::__construct($context);
    }

    public function execute()
    {
        $templateHtml = '';
        $templateId   = $this->getRequest()->getParam('templateId');

        try {
            $templateHtml = $this->helperData->readFile($this->helperData->getBaseTemplatePath() . $templateId . '.html');
            $status       = true;
            $message      = __('Load message success!');
        } catch (\Exception $e) {
            $status  = false;
            $message = __("Cannot load template.");
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData(['status' => $status, 'message' => $message, 'templateHtml' => $templateHtml]);
    }
}