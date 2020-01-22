<?php

namespace MageSuite\ProductSymbols\Model\Source;

class GroupList implements \Magento\Framework\Data\OptionSourceInterface
{
    const CACHE_TAG = 'symbol_options_store_%s';

    /**
     * @var \MageSuite\ProductSymbols\Model\ResourceModel\Group\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MageSuite\ProductSymbols\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    public function __construct(
        \MageSuite\ProductSymbols\Model\ResourceModel\Group\CollectionFactory $collectionFactory,
        \MageSuite\ProductSymbols\Api\GroupRepositoryInterface $groupRepository
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->groupsRepository = $groupRepository;
    }

    public function toOptionArray()
    {
        return $this->getAllGroups();
    }

    private function getAllGroups()
    {
        $groupCollection = $this->collectionFactory->create();
        $options = [];
        foreach ($groupCollection as $group) {
            $group = $this->groupRepository->getById($group->getEntityId());

            $options[] = [
                'label' => $group->getGroupName(),
                'value' => $group->getEntityId()
            ];
        }

        return $options;
    }
}
