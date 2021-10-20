<?php
namespace MageSuite\ProductSymbols\Model\Group\Processor;

class Save
{
    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $eventManager;
    /**
     * @var \MageSuite\ProductSymbols\Model\GroupFactory
     */
    protected $groupFactory;
    /**
     * @var \MageSuite\ProductSymbols\Api\GroupRepositoryInterface
     */
    protected $groupRepository;
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    public function __construct(
        \MageSuite\ProductSymbols\Model\GroupFactory $groupFactory,
        \MageSuite\ProductSymbols\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        $this->eventManager = $eventManager;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->groupFactory = $groupFactory;
        $this->groupRepository = $groupRepository;
    }

    public function processSave($params)
    {
        $id = $params['entity_id'] ?? null;

        if ($id) {
            $group = $this->groupRepository->getById($params['entity_id']);
        } else {
            $group = $this->groupFactory->create();
        }

        $group->setData($params);
        return $this->groupRepository->save($group);
    }
}
