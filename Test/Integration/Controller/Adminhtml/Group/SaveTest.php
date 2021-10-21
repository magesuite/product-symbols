<?php

namespace MageSuite\ProductSymbols\Test\Integration\Controller\Adminhtml\Group;

/**
 * @magentoAppArea adminhtml
 */
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * @var \MageSuite\ProductSymbols\Api\GroupRepositoryInterface
     */
    protected $groupRepositoryInterface;

    /**
     * @var \MageSuite\ProductSymbols\Model\GroupFactory
     */
    protected $groupFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->groupRepositoryInterface = $this->_objectManager->create(\MageSuite\ProductSymbols\Api\GroupRepositoryInterface::class);
        $this->groupFactory = $this->_objectManager->create(\MageSuite\ProductSymbols\Model\GroupFactory::class);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveGroup()
    {
        $editData = [
            'group_name' => 'group 1',
            'group_code' => 'group_1'
        ];

        $this->getRequest()->setPostValue($editData);
        $this->dispatch('backend/symbol/group/save');

        $group = $this->groupRepositoryInterface->getByCode('group_1');

        $this->assertEquals('group 1', $group->getGroupName());
        $this->assertEquals('group_1', $group->getGroupCode());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testEditGroup()
    {
        $group = $this->groupFactory->create();

        $group
            ->setGroupName('group 1')
            ->setGroupCode('group_1');

        $group = $this->groupRepositoryInterface->save($group);

        $editData = [
            'entity_id' => $group->getEntityId(),
            'group_name' => 'group 1 edited',
            'group_code' => 'group_1',
            'ignore_product_assignment' => 1
        ];

        $this->getRequest()->setPostValue($editData);
        $this->dispatch('backend/symbol/group/save');

        $group = $this->groupRepositoryInterface->getById($group->getEntityId());

        $this->assertEquals('group 1 edited', $group->getGroupName());
        $this->assertEquals('group_1', $group->getGroupCode());
        $this->assertTrue((bool)$group->getIgnoreProductAssignment());
    }
}
