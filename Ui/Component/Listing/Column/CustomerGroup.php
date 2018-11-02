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

use Magento\Ui\Component\Listing\Columns\Column;

class CustomerGroup extends Column
{
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
                if (isset($item[$this->getData('name')])) {
                    $item[$this->getData('name')] = explode(',', $item[$this->getData('name')]);
                    $item[$this->getData('name')] = $this->prepareItem($item);
                }
            }
        }

        return $dataSource;
    }

    public function prepareItem(array $item)
    {
        $content    = [];
        $out = [];
        $origGroup = $item['customer_group_ids'];

        if (!is_array($origGroup)) {
            $origGroup = [$origGroup];
        }

        foreach ($origGroup as $group) {
            switch ($group) {
                case 0:
                    $out[$group] = ['value' => $group, 'label' => 'NOT LOGGED IN'];
                    break;
                case 1:
                    $out[$group] = ['value' => $group, 'label' => 'General'];
                    break;
                case 2:
                    $out[$group] = ['value' => $group, 'label' => 'Wholesale'];
                    break;
                case 3:
                    $out[$group] = ['value' => $group, 'label' => 'Retailer'];
                    break;
                default:
                    $out[$group] = ['value' => $group, 'label' => ''];
            }
            $content[] = $out[$group]['label'];
        }

        return implode(", ",$content);
    }
}