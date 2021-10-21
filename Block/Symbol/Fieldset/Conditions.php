<?php

namespace MageSuite\ProductSymbols\Block\Symbol\Fieldset;

class Conditions extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions_serialized'; //phpcs:ignore

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \MageSuite\ProductSymbols\Model\SymbolFactory
     */
    protected $symbolFactory;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $rendererFieldset;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \MageSuite\ProductSymbols\Model\SymbolFactory $symbolFactory,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions = $conditions;
        $this->symbolFactory = $symbolFactory;

        parent::__construct($context, $registry, $formFactory, $data);
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
            ->setFieldSetId($conditionsFieldSetId);

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

    private function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);

        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }

    public function getTabClass()
    {
        return null;
    }

    public function getTabUrl()
    {
        return null;
    }

    public function isAjaxLoaded()
    {
        return false;
    }

    public function getTabLabel()
    {
        return __('Conditions');
    }

    public function getTabTitle()
    {
        return __('Conditions');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
