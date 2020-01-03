<?php

namespace MageSuite\ProductSymbols\Model\Source;

class GroupsList implements \Magento\Framework\Data\OptionSourceInterface
{
    const CACHE_TAG = 'symbol_options_store_%s';
    /**
     * @var \MageSuite\ProductSymbols\Model\ResourceModel\Groups\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface
     */
    protected $groupsRepository;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Groups\CollectionFactory $collectionFactory,
        \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface $groupsRepository
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->groupsRepository = $groupsRepository;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllGroups();
    }

    private function getAllGroups()
    {
        $groupsCollection = $this->collectionFactory->create();
        $options = [];
        foreach ($groupsCollection as $group) {
            $group = $this->groupsRepository->getById($group->getEntityId());

            $options[] = [
                'label' => $group->getGroupName(),
                'value' => $group->getEntityId()
            ];
        }

        return $options;
    }
}
