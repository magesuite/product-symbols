<?php

declare(strict_types=1);

namespace MageSuite\ProductSymbols\Provider\Data;

class SymbolToProduct
{
    public const CACHE_KEY = 'google_structured_data_product_%s_%s';
    public const CACHE_GROUP = 'google_structured_data_product';

    protected array $symbolsList = [];

    public function __construct(
        protected \MageSuite\ProductSymbols\Model\ResourceModel\Symbol\CollectionFactory $symbolsCollectionFactory
    ) {}

    public function getSymbolsWithConditions(int $storeId): array
    {
        if (!($this->symbolsList[$storeId] ?? false)) {
            $symbols = $this->symbolsCollectionFactory->create()
                ->setStoreId($storeId)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter(\MageSuite\ProductSymbols\Setup\Patch\Data\AddStatusAttribute::IS_ENABLED_ATTRIBUTE_CODE, 1)
                ->getItems();

            /** @var \MageSuite\ProductSymbols\Model\Symbol $symbol */
            foreach ($symbols as $index => $symbol) {
                $symbol->setForceValidation(true);

                if ($symbol->hasConditions()) {
                    continue;
                }

                unset($symbols[$index]);
            }

            $this->symbolsList[$storeId] = $symbols;
        }

        return $this->symbolsList[$storeId];
    }
}
