<?php
namespace MageSuite\ProductSymbols\Block\Adminhtml\Symbol\Grid\Renderer;

abstract class AbstractColumnRenderer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface
     */
    protected $symbolRepository;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    protected $symbolData = [];

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \MageSuite\ProductSymbols\Api\SymbolRepositoryInterface $symbolRepository,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->symbolRepository = $symbolRepository;
        $this->filesystem = $filesystem;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $column = $this->getColumn()->getIndex();
        $value = $this->getColumnValue($column, $row->getEntityId());

        return $value;
    }

    public function getSymbolData($entityId)
    {
        return $this->symbolRepository->getById($entityId);
    }

    abstract public function getColumnValue($columnId, $entityId);
}
