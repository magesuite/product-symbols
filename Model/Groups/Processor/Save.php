<?php
namespace MageSuite\ProductSymbols\Model\Groups\Processor;

class Save
{
    const DEFAULT_STORE_ID = 0;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $eventManager;
    /**
     * @var \MageSuite\ProductSymbols\Model\GroupsFactory
     */
    protected $groupsFactory;
    /**
     * @var \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface
     */
    protected $groupsRepository;
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;


    public function __construct(
        \MageSuite\ProductSymbols\Model\GroupsFactory $groupsFactory,
        \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface $groupsRepository,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    )
    {
        $this->eventManager = $eventManager;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->groupsFactory = $groupsFactory;
        $this->groupsRepository = $groupsRepository;
    }

    public function processSave($params)
    {
        $isNew = (!isset($params['entity_id'])) || (isset($params['entity_id']) && $params['entity_id'] == "") ? true : false;

        if ($isNew) {
            $group = $this->groupsFactory->create();
            $group
                ->setGroupCode($params['group_code'])
                ->setGroupName($params['group_name']);
        } else {
            $group = $this->groupsRepository->getById($params['entity_id']);
            $group->setGroupName($params['group_name']);
        }

        $group = $this->groupsRepository->save($group);

        return $group;
    }
}