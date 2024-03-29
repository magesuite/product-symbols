<?php

namespace MageSuite\ProductSymbols\Model\Symbol;

class SymbolDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const HIDDEN_ATTRIBUTES = [
        'entity_id',
        'store_id',
        'created_at',
        'updated_at'
    ];

    protected string $requestScopeFieldName = 'store';

    protected \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository;

    protected \MageSuite\ProductSymbols\Api\Data\SymbolInterfaceFactory $symbolFactory;

    protected \Magento\Framework\App\RequestInterface $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $symbolCollectionFactory,
        \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository,
        \MageSuite\ProductSymbols\Api\Data\SymbolInterfaceFactory $symbolFactory,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $symbolCollectionFactory->create();
        $this->symbolRepository = $symbolRepository;
        $this->symbolFactory = $symbolFactory;
        $this->request = $request;
    }

    public function getCurrentSymbol()
    {
        $requestId = $this->request->getParam($this->requestFieldName);
        $requestScope = $this->request->getParam($this->requestScopeFieldName, \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        if ($requestId) {
            $symbol = $this->symbolRepository->getById($requestId, $requestScope);
        } else {
            $symbol = $this->symbolFactory->create();
        }

        return $symbol;
    }

    public function getData()
    {
        $result = [];
        $symbol = $this->getCurrentSymbol();

        if (!$symbol || !$symbol->getId()) {
            return $result;
        }

        $useConfig = [];
        $symbolResource = $symbol->getResource();

        if ($symbol->getStoreId() === \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            $useConfig = [
                'symbol_name' => false,
                'is_enabled' => false,
                'symbol_icon' => false,
                'symbol_short_description' => false,
                'symbol_description' => false,
                'symbol_groups' => false,
                'cms_block_identifier' => false,
                'sort_order' => false
            ];
        } else {
            foreach ($symbol->getData() as $attrName => $value) {
                if (in_array($attrName, self::HIDDEN_ATTRIBUTES)) {
                    continue;
                }

                $rawValue = $symbolResource->getAttributeRawValue($symbol->getEntityId(), $attrName, $symbol->getStoreId());
                $useConfigValue = $rawValue === false;
                $useConfig[$attrName] = $useConfigValue;
            }
        }
        $symbolData = $symbol->getData();
        $symbolData['use_config'] = $useConfig;
        $result = [$symbol->getEntityId() => $symbolData];

        if ($symbol->getSymbolIcon()) {
            $name = $symbol->getSymbolIcon();
            $size = 0;

            $filePath = sprintf('media/symbol/%s', $name);

            if (file_exists($filePath)) { //phpcs:ignore
                $size = filesize($filePath); //phpcs:ignore
            }

            $result[$symbol->getEntityId()]['symbol_icon'] = [
                0 => [
                    'url' => $symbol->getSymbolIconUrl(),
                    'name' => $name,
                    'size' => $size
                ]
            ];
        }

        return $result;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        $params = $this->request->getParams();
        $groupsToFields = [
            'symbol_name_group' => 'use_config.symbol_name',
            'is_enabled_group' => 'use_config.is_enabled',
            'symbol_icon_group' => 'use_config.symbol_icon',
            'symbol_short_description_group' => 'use_config.symbol_short_description',
            'symbol_description_group' => 'use_config.symbol_description',
            'symbol_groups_group' => 'use_config.symbol_groups',
            'cms_block_identifier_group' => 'use_config.cms_block_identifier',
            'sort_order_group' => 'use_config.sort_order'
        ];

        if (!isset($params['store']) || (isset($params['store']) && $this->checkIsDefaultScope($params['store']))) {
            foreach ($groupsToFields as $group => $field) {
                $meta['symbol_details']['children'][$group]['children'][$field]['arguments']['data']['config']['visible'] = false;
                $meta['symbol_details']['children'][$group]['children'][$field]['arguments']['data']['config']['default'] = false;
            }
        }

        return $meta;
    }

    protected function checkIsDefaultScope($storeId)
    {
        return $storeId == \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }
}
