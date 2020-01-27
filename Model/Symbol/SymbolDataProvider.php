<?php
namespace MageSuite\ProductSymbols\Model\Symbol;

class SymbolDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const DEFAULT_STORE_ID = 0;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $requestScopeFieldName = 'store';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
     */
    protected $symbolRepository;

    /**
     * @var \MageSuite\ProductSymbols\Api\Data\SymbolInterfaceFactory
     */
    protected $symbolFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $symbolCollectionFactory,
        \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository,
        \MageSuite\ProductSymbols\Api\Data\SymbolInterfaceFactory $symbolFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $symbolCollectionFactory->create();
        $this->request = $request;
        $this->registry = $registry;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->symbolRepository = $symbolRepository;
        $this->symbolFactory = $symbolFactory;
    }

    public function getCurrentSymbol()
    {
        $symbol = $this->registry->registry('symbol');
        if ($symbol) {
            return $symbol;
        }
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
                'symbol_icon' => false,
                'symbol_short_description' => false,
                'symbol_groups' => false,
            ];
        } else {
            foreach ($symbol->getData() as $attrName => $value) {
                if ($attrName == 'entity_id' || $attrName == 'store_id' || $attrName == 'created_at' || $attrName == 'updated_at') {
                    continue;
                }

                $rawValue = $symbolResource->getAttributeRawValue($symbol->getEntityId(), $attrName, $symbol->getStoreId());
                $useConfigValue = $rawValue === false;
                $useConfig[$attrName] = $useConfigValue;
            }
        }
        $result = [
            $symbol->getEntityId() => [
                'entity_id' => $symbol->getEntityId(),
                'store_id' => $symbol->getStoreId(),
                'symbol_name' => $symbol->getSymbolName(),
                'symbol_short_description' => $symbol->getSymbolShortDescription(),
                'symbol_groups' => $symbol->getSymbolGroups(),
                'use_config' => $useConfig
            ]
        ];

        if ($symbol->getSymbolIcon()) {
            $name = $symbol->getSymbolIcon();
            $url = $symbol->getSymbolIconUrl();
            $size = file_exists('media/symbol/' . $name) ? filesize('media/symbol/' . $name) : 0;
            $result[$symbol->getEntityId()]['symbol_icon'] = [
                0 => [
                    'url' => $url,
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
            'symbol_icon_group' => 'use_config.symbol_icon',
            'symbol_short_description_group' => 'use_config.symbol_short_description',
            'symbol_groups_group' => 'use_config.symbol_groups'
        ];

        if (!isset($params['store']) || (isset($params['store']) && $params['store'] == '0')) {
            foreach ($groupsToFields as $group => $field) {
                $meta['symbol_details']['children'][$group]['children'][$field]['arguments']['data']['config']['visible'] = false;
                $meta['symbol_details']['children'][$group]['children'][$field]['arguments']['data']['config']['default'] = false;
            }
        }

        return $meta;
    }
}
