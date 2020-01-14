<?php
namespace MageSuite\ProductSymbols\Block\Symbol;

class Group extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_ProductSymbols::symbols/group.phtml';
    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \MageSuite\ProductSymbols\ViewModel\Symbol\Group
     */
    protected $viewModel;

    protected $viewModelInstance;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        string $viewModel = null,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->context = $context;
        $this->objectManager = $objectManager;
        $this->viewModel = $viewModel;
    }


    public function getViewModel()
    {
        $viewModel = $this->viewModel;
        $data = $this->getData();

        if(isset($data['view_model'])){
            $viewModel = $data['view_model'];
        }

        if (!$this->viewModelInstance) {
            $this->viewModelInstance = $this->objectManager->create($viewModel, ['data' => $data]);
        }

        return $this->viewModelInstance;
    }
}