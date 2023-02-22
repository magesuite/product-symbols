<?php

namespace MageSuite\ProductSymbols\Ui\Component\Form;

class SerializedConditionVisibility extends \Magento\Ui\Component\Form\Fieldset
{
    protected \Magento\Framework\Registry $registry;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\Registry $registry,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);

        $this->registry = $registry;
    }

    public function getConfiguration()
    {
        $config = parent::getConfiguration();

        $currentSymbol = $this->getCurrentSymbol();
        if (!$currentSymbol || !$currentSymbol->getStoreId()) {
            return $config;
        }

        $config['visible'] = false;

        return $config;
    }

    protected function getCurrentSymbol()
    {
        return $this->registry->registry('symbol');
    }
}
