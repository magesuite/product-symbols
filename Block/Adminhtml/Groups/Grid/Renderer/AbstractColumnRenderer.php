<?php
namespace MageSuite\ProductSymbols\Block\Adminhtml\Groups\Grid\Renderer;

abstract class AbstractColumnRenderer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface
     */
    protected $groupsRepository;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    static $groupData = [];


    public function __construct(
        \Magento\Backend\Block\Context $context,
        \MageSuite\ProductSymbols\Api\GroupsRepositoryInterface $symbolsRepository,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->groupsRepository = $symbolsRepository;
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
        if(!isset(self::$groupData[$entityId])) {
            self::$groupData[$entityId] = $this->groupsRepository->getById($entityId);
        }

        return self::$groupData[$entityId];
    }

    abstract public function getColumnValue($columnId, $entityId);
}