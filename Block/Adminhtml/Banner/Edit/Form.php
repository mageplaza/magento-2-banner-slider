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

namespace Mageplaza\BannerSlider\Block\Adminhtml\Banner\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\BannerSlider\Model\Config\Source\Template;

/**
 * Class Form
 * @package Mageplaza\BannerSlider\Block\Adminhtml\Banner\Edit
 */
class Form extends Generic
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_BannerSlider::widget/form.phtml';

    /**
     * @var Template
     */
    protected $template;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Template $template
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Template $template,
        array $data = []
    ) {
        $this->template = $template;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get Demo Template
     *
     * @return mixed
     */
    public function getTemplateHtml()
    {
        return $this->template->getTemplateHtml();
    }

    /**
     * @return false|string
     */
    public function getImageUrls()
    {
        return $this->template->getImageUrls();
    }

    /**
     * from Magento Core
     *
     * @param string $string
     *
     * @return string|string[]|null
     */
    public function escapeJs($string)
    {
        if ($string === '' || ctype_digit($string)) {
            return $string;
        }

        return preg_replace_callback(
            '/[^a-z0-9,\._]/iSu',
            function ($matches) {
                $chr = $matches[0];
                if (strlen($chr) != 1) {
                    $chr = mb_convert_encoding($chr, 'UTF-16BE', 'UTF-8');
                    $chr = ($chr === false) ? '' : $chr;
                }

                return sprintf('\\u%04s', strtoupper(bin2hex($chr)));
            },
            $string
        );
    }
}
