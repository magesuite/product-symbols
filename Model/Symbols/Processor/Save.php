<?php
namespace MageSuite\ProductSymbols\Model\Symbols\Processor;

class Save
{
    const DEFAULT_STORE_ID = 0;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $eventManager;
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * @var \MageSuite\ProductSymbols\Model\SymbolsFactory
     */
    private $symbolsFactory;
    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolsRepositoryInterface
     */
    private $symbolsRepository;

    public function __construct(
        \MageSuite\ProductSymbols\Model\SymbolsFactory $symbolsFactory,
        \MageSuite\ProductSymbols\Api\SymbolsRepositoryInterface $symbolsRepository,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    )
    {

        $this->eventManager = $eventManager;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->symbolsFactory = $symbolsFactory;
        $this->symbolsRepository = $symbolsRepository;
    }

    public function processSave($params) {
        $originalParams = $params;

        $isNew = (!isset($params['entity_id'])) || (isset($params['entity_id']) && $params['entity_id'] == "") ? true : false;

        if ($isNew) {
            if(!isset($params['store_id'])){
                $params['store_id'] = self::DEFAULT_STORE_ID;
            }
            $symbol = $this->symbolsFactory->create();
            $symbol->setData($params->getData());
        } else {
            if(!$params['is_api']) {
                $matchedParams = $this->matchParams($params);

                $params = $matchedParams;
            }

            $symbol = $this->symbolsRepository->getById($params['entity_id'], $params['store_id']);
            $symbol->setData($params->getData());
        }
        $imagePath = false;

        if(isset($params['symbol_icon'])) {
            if (is_array($params['symbol_icon'])) {
                $imagePath = $params['symbol_icon'][0]['name'];
            } else {
                $imagePath = $params['symbol_icon'];
            }
        }
        if($imagePath){
            $symbol->setSymbolIcon($imagePath);
        } elseif ($symbol->getStoreId() == self::DEFAULT_STORE_ID) {
            $symbol->setSymbolIcon('');
        }

        $symbol = $this->symbolsRepository->save($symbol);

        return $symbol;
    }

    public function matchChangedFields($config)
    {
        $matchedFields = [];
        foreach ($config as $field => $value) {
            if($value == 'false'){
                $matchedFields[] = $field;
            }
        }
        return $matchedFields;
    }

    public function matchParams($params)
    {
        $changedFields = $this->matchChangedFields($params['use_config']);

        $matchedParams = [];

        foreach ($changedFields as $field) {
            if(!isset($params[$field])) {
                continue;
            }

            if($field == 'symbol_icon'){
                $matchedParams[$field] = $params['symbol_icon'][0]['name'];
                continue;
            }

            $matchedParams[$field] = $params[$field];
        }
        $matchedParams['entity_id'] = $params['entity_id'];
        $matchedParams['store_id'] = $params['store_id'];

        return $this->dataObjectFactory->create()->setData($matchedParams);
    }
}