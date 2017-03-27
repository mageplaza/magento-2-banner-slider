<?php
namespace Mageplaza\BetterSlider\Helper;

use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends CoreHelper
{
    public function __construct(
		\Magento\Framework\App\Helper\Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context, $objectManager, $storeManager);
    }
}
