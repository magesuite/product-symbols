<?php

namespace MageSuite\ProductSymbols\Model\ResourceModel\Symbol\Attribute;

class Collection extends \Magento\Eav\Model\ResourceModel\Attribute\Collection
{
    protected $_entityTypeCode = 'symbol';

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $storeManager,
            $connection,
            $resource
        )
        ;
    }

    protected function _getEntityTypeCode()
    {
        return $this->_entityTypeCode;
    }

    protected function _getEavWebsiteTable()
    {
        return $this->getTable('symbol_eav_attribute_website');
    }
}
