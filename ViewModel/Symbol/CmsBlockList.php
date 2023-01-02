<?php
declare(strict_types=1);

namespace MageSuite\ProductSymbols\ViewModel\Symbol;

class CmsBlockList implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository;

    protected \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $collectionFactory;

    protected \Magento\Cms\Model\Template\FilterProvider $filterProvider;

    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct(
        \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository,
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $collectionFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->symbolRepository = $symbolRepository;
        $this->collectionFactory = $collectionFactory;
        $this->filterProvider = $filterProvider;
        $this->storeManager = $storeManager;
    }

    public function getList(\Magento\Catalog\Api\Data\ProductInterface $product): array
    {
        $blockIds = [];
        $blocks = [];
        $store = $this->storeManager->getStore();

        foreach ($this->symbolRepository->getAllSymbols() as $symbol) {
            if (!$symbol->getIsEnabled() || !$symbol->getCmsBlockIdentifier() || !$symbol->validate($product)) {
                continue;
            }

            $blockIds[] = $symbol->getCmsBlockIdentifier();
        }

        if (empty($blockIds)) {
            return $blocks;
        }

        /** @var \Magento\Cms\Model\ResourceModel\Block\Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addStoreFilter($store)
            ->addFieldToFilter('identifier', ['in' => $blockIds]);

        foreach ($collection as $block) {
            try {
                $blocks[] = $this->filterProvider->getBlockFilter()
                    ->setStoreId($store->getId())
                    ->filter($block->getContent());
            } catch (\Exception $e) {
                continue;
            }
        }

        return $blocks;
    }
}
