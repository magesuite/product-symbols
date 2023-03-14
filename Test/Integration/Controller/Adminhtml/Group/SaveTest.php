<?php

namespace MageSuite\ProductSymbols\Test\Integration\Controller\Adminhtml\Group;

/**
 * @magentoDbIsolation enabled
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
     * @group symbolGroupSaving
     */
    public function testSaveGroupWithPascalCase()
    {
        $this->saveGroup('Group', 'RandomGroup');
    }

    /**
     * @group symbolGroupSaving
     */
    public function testSaveGroupWithCamelCase()
    {
        $this->saveGroup('group 1', 'groupOne');
    }

    /**
     * @group symbolGroupSaving
     */
    public function testSaveGroupWithSnakeCase()
    {
        $this->saveGroup('group 2', 'group_2');
    }

    /**
     * @group symbolGroupSaving
     */
    public function testSaveGroupWithKebabCase()
    {
        $this->saveGroup('group 3', 'group-3', false);
    }

    /**
     * @group symbolGroupSaving
     */
    public function testSaveGroupWithSlashCase()
    {
        $this->saveGroup('group 4', 'group/4', false);
    }

    /**
     * @group symbolGroupSaving
     */
    public function testSaveGroupWithSpaces()
    {
        $this->saveGroup('group 5', 'group 5', false);
    }

    protected function saveGroup(string $name, string $code, bool $isSuccessExpected = true): void
    {
        $this->getRequest()->setPostValue([
            'group_name' => $name,
            'group_code' => $code
        ]);
        $this->dispatch('backend/symbol/group/save');

        $group = $this->groupRepositoryInterface->getByCode($code);

        if ($isSuccessExpected) {
            $this->assertEquals($name, $group->getGroupName());
            $this->assertEquals($code, $group->getGroupCode());
        } else {
            $this->assertEmpty($group);
        }


    }

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
