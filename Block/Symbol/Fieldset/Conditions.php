<?php

namespace MageSuite\ProductSymbols\Block\Symbol\Fieldset;

class Conditions extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_nameInLayout = 'conditions_serialized'; //phpcs:ignore

    protected \Magento\Rule\Block\Conditions $conditions;

    protected \MageSuite\ProductSymbols\Model\SymbolFactory $symbolFactory;

    protected \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \MageSuite\ProductSymbols\Model\SymbolFactory $symbolFactory,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->rendererFieldset = $rendererFieldset;
        $this->conditions = $conditions;
        $this->symbolFactory = $symbolFactory;
    }

    protected function _prepareForm()
    {
        $symbol = $this->symbolFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $symbol->load($this->getRequest()->getParam('id'));
        }

        $form = $this->addTabToForm($symbol);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function addTabToForm($symbol)
    {
        $formName = 'symbol_edit_form';

        $conditionsFieldSetId = $symbol->getConditionsFieldSetId($formName);

        $newChildUrl = $this->getUrl(
            sprintf('sales_rule/promo_quote/newConditionHtml/form/%s', $conditionsFieldSetId),
            ['form_namespace' => $formName]
        );

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('product_symbol_entity_');

        $renderer = $this->rendererFieldset
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($newChildUrl)
            ->setFieldSetId($conditionsFieldSetId)
            ->setNameInLayout($conditionsFieldSetId);

        $fieldset = $form
            ->addFieldset('conditions_serialized', [])
            ->setRenderer($renderer);

        $fieldset
            ->addField(
                'conditions',
                'text',
                [
                    'name' => 'conditions',
                    'label' => __('Conditions'),
                    'title' => __('Conditions'),
                    'required' => true,
                    'data-form-part' => $formName
                ]
            )
            ->setRule($symbol)
            ->setRenderer($this->conditions);

        $form->setValues($symbol->getData());
        $this->setConditionFormName($symbol->getConditions(), $formName);

        return $form;
    }

    protected function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);

        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
