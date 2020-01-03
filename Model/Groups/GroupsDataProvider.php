<?php
namespace MageSuite\ProductSymbols\Model\Groups;

class GroupsDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface
     */
    protected $groupsRepository;
    /**
     * @var \MageSuite\ProductSymbols\Api\Data\GroupsInterfaceFactory
     */
    protected $groupsFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \MageSuite\ProductSymbols\Model\ResourceModel\Symbols\CollectionFactory $symbolsCollectionFactory,
        \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface $groupsRepository,
        \MageSuite\ProductSymbols\Api\Data\GroupsInterfaceFactory $groupsFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry,
        array $meta = [],
        array $data = []
    ) {


        $this->collection = $symbolsCollectionFactory->create();
        $this->request = $request;
        $this->registry = $registry;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->groupsRepository = $groupsRepository;
        $this->groupsFactory = $groupsFactory;
    }

    public function getCurrentGroup()
    {
        $group = $this->registry->registry('group');
        if ($group) {
            return $group;
        }
        $requestId = $this->request->getParam($this->requestFieldName);

        if ($requestId) {
            $group = $this->groupsRepository->getById($requestId);
        } else {
            $group = $this->groupsFactory->create();
        }

        return $group;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $result = [];
        $group = $this->getCurrentGroup();

        if (!$group || !$group->getEntityId()) {
            return $result;
        }

        $result = [
            $group->getEntityId() => [
                'entity_id' => $group->getEntityId(),
                'group_name' => $group->getGroupName(),
                'group_code' => $group->getGroupCode(),
                'use_config' => [
                    'group_name' => false,
                    'group_code' => false
                ]
            ]
        ];

        return $result;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        $params = $this->request->getParams();

        if(isset($params['id'])) {
            $meta['group_details']['children']['group_code_group']['children']['group_code']['arguments']['data']['config']['disabled'] = true;
            $meta['group_details']['children']['group_code_group']['children']['group_code']['arguments']['data']['config']['notice'] = __('This field can be modified only during group creation');
        }

        return $meta;
    }
}
