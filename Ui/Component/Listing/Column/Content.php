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

namespace Mageplaza\BannerSlider\Ui\Component\Listing\Column;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\BannerSlider\Model\Config\Source\Image;
use Mageplaza\BannerSlider\Model\Config\Source\Type;

/**
 * Class Content
 * @package Mageplaza\BannerSlider\Ui\Component\Listing\Column
 */
class Content extends Column
{
    /**
     * @var FilterProvider
     */
    public $filterProvider;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Image
     */
    protected $imageModel;

    /**
     * Content constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FilterProvider $filterProvider
     * @param UrlInterface $urlBuilder
     * @param Image $imageModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FilterProvider $filterProvider,
        UrlInterface $urlBuilder,
        Image $imageModel,
        array $components = [],
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        $this->urlBuilder = $urlBuilder;
        $this->imageModel = $imageModel;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     * @throws Exception
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $path = $this->imageModel->getBaseUrl();
            foreach ($dataSource['data']['items'] as & $item) {
                $banner = new DataObject($item);
                if ($item['type'] === Type::IMAGE && $item['image']) {
                    $item[$fieldName . '_src'] = $path . $item['image'];
                } else {
                    $item[$fieldName] = $this->filterProvider->getPageFilter()->filter($item[$fieldName]);
                }

                $item[$fieldName . '_type'] = $item['type'];
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'mpbannerslider/banner/edit',
                    ['banner_id' => $banner->getBannerId(), 'store' => $this->context->getRequestParam('store')]
                );
            }
        }

        return $dataSource;
    }
}
