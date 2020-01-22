<?php
namespace MageSuite\ProductSymbols\Block\Adminhtml\Group\Grid\Renderer;

abstract class AbstractColumnRenderer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \MageSuite\ProductSymbols\Api\GroupRepositoryInterface
     */
    protected $groupRepository;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    protected $groupData = [];

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \MageSuite\ProductSymbols\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->groupRepository = $groupRepository;
        $this->filesystem = $filesystem;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $column = $this->getColumn()->getIndex();

        $value = $this->getColumnValue($column, $row->getEntityId());

        return $value;
    }

    public function getGroupData($entityId)
    {
        if (!isset($this->groupData[$entityId])) {
            $this->groupData[$entityId] = $this->groupRepository->getById($entityId);
        }

        return $this->groupData[$entityId];
    }

    abstract public function getColumnValue($columnId, $entityId);
}
