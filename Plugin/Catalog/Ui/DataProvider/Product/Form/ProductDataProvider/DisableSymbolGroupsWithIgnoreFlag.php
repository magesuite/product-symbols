<?php

namespace MageSuite\ProductSymbols\Plugin\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider;

class DisableSymbolGroupsWithIgnoreFlag
{
    const ELEMENT_DISABLED_PATH_FORMAT = '%s/arguments/data/config/disabled';
    const ELEMENT_NOTICE_PATH_FORMAT = '%s/arguments/data/config/notice';

    /**
     * @var \MageSuite\ProductSymbols\Model\GroupRepository
     */
    protected $groupRepository;

    /**
     * @var \Magento\Framework\Stdlib\ArrayManager
     */
    protected $arrayManager;

    public function __construct(
        \MageSuite\ProductSymbols\Model\GroupRepository $groupRepository,
        \Magento\Framework\Stdlib\ArrayManager $arrayManager
    ) {
        $this->groupRepository = $groupRepository;
        $this->arrayManager = $arrayManager;
    }

    public function afterGetMeta(\Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider $subject, $result)
    {
        $groups = $this->groupRepository->getList();

        if (!$groups->getTotalCount()) {
            return $result;
        }

        /** @var \MageSuite\ProductSymbols\Model\Group $group */
        foreach ($groups->getItems() as $group) {
            if (!$group->getIgnoreProductAssignment()) {
                continue;
            }

            $elementPath = $this->arrayManager->findPath($group->getGroupCode(), $result, null, 'children');

            if (!$elementPath) {
                continue;
            }

            $result = $this->arrayManager->set(
                sprintf(self::ELEMENT_DISABLED_PATH_FORMAT, $elementPath),
                $result,
                true
            );

            $result = $this->arrayManager->set(
                sprintf(self::ELEMENT_NOTICE_PATH_FORMAT, $elementPath),
                $result,
                'This field is disabled because "Ignore product assignment" flag is set in this symbol group. All symbols from this group will be displayed.'
            );
        }

        return $result;
    }
}
