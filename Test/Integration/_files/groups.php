<?php
/** @var  \MageSuite\ProductSymbols\Api\GroupRepositoryInterface $groupRepository */
$groupRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\ProductSymbols\Api\GroupRepositoryInterface');

/** @var  \MageSuite\ProductSymbols\Model\Group $group */
$group = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\ProductSymbols\Model\Group');
$group
    ->setEntityId(100)
    ->setGroupName('first group')
    ->setGroupCode('first_group');


$groupRepository->save($group);

/** @var  \MageSuite\ProductSymbols\Model\Group $group */
$group = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\ProductSymbols\Model\Group');
$group
    ->setEntityId(200)
    ->setGroupName('second group')
    ->setGroupCode('second_group');


$groupRepository->save($group);

/** @var  \MageSuite\ProductSymbols\Model\Group $group */
$group = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\ProductSymbols\Model\Group');
$group
    ->setEntityId(300)
    ->setGroupName('third group')
    ->setGroupCode('third_group');


$groupRepository->save($group);
