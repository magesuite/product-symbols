<?php
namespace MageSuite\ProductSymbols\Model\Group\Processor;

class Save
{
    const DEFAULT_STORE_ID = 0;

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
    )
    {
        $this->eventManager = $eventManager;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->groupFactory = $groupFactory;
        $this->groupRepository = $groupRepository;
    }

    public function processSave($params)
    {
        $isNew = (!isset($params['entity_id'])) || (isset($params['entity_id']) && $params['entity_id'] == "") ? true : false;

        if ($isNew) {
            $group = $this->groupFactory->create();
            $group
                ->setGroupCode($params['group_code'])
                ->setGroupName($params['group_name']);
        } else {
            $group = $this->groupRepository->getById($params['entity_id']);
            $group->setGroupName($params['group_name']);
        }

        $group = $this->groupRepository->save($group);

        return $group;
    }
}