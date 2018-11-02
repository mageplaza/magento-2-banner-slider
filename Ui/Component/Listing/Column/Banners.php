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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\BannerSlider\Helper\Data as bannerHelper;

class Banners extends Column
{
    protected $helperData;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        bannerHelper $helperData,
        array $components = [],
        array $data = []
    )
    {
        $this->helperData = $helperData;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['slider_id'])) {
                    $id     = $item['slider_id'];
                    $data = $this->helperData->getBannerCollection($id)->getSize();
                    $item[$this->getData('name')] = ($data > 0) ? $data .'<span> banners </span>' : '<b>No banner added</b>';
                }
            }
        }

        return $dataSource;
    }

}