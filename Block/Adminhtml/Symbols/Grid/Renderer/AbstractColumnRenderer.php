<?php
namespace MageSuite\ProductSymbols\Block\Adminhtml\Symbols\Grid\Renderer;

abstract class AbstractColumnRenderer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var
     */
    protected $symbol;

    /**
     * @var \MageSuite\ProductSymbols\Api\SymbolsRepositoryInterface
     */
    protected $symbolsRepository;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    static $symbolData = [];


    public function __construct(
        \Magento\Backend\Block\Context $context,
        \MageSuite\ProductSymbols\Api\SymbolsRepositoryInterface $symbolsRepository,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->symbolsRepository = $symbolsRepository;
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
        if(!isset(self::$symbolData[$entityId])) {
            self::$symbolData[$entityId] = $this->symbolsRepository->getById($entityId);
        }

        return self::$symbolData[$entityId];
    }

    abstract public function getColumnValue($columnId, $entityId);
}