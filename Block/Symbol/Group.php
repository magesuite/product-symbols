<?php
namespace MageSuite\ProductSymbols\Block\Symbol;

class Group extends \Magento\Framework\View\Element\Template
{
    const BASE_VIEW_MODEL = \MageSuite\ProductSymbols\ViewModel\Symbol\Group::class;

    protected $_template = 'MageSuite_ProductSymbols::symbols/group.phtml';
    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    protected $viewModelInstance;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->context = $context;
        $this->objectManager = $objectManager;
    }

    public function getViewModel()
    {
        $viewModel = self::BASE_VIEW_MODEL;

        $data = $this->getData();

        if (isset($data['view_model'])) {
            $viewModel = $data['view_model'];
        }

        if (!$this->viewModelInstance) {
            $this->viewModelInstance = $this->objectManager->create($viewModel, ['data' => $data]);
        }

        return $this->viewModelInstance;
    }
}
