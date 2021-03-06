<?php
namespace Jh\CoreBugCheckoutAgreements\Block\Adminhtml\Agreement\Edit;

use Magento\CheckoutAgreements\Block\Adminhtml\Agreement\Edit\Form as AgreementsForm;

/**
 * Class Form
 * @package Jh\CoreBugCheckoutAgreements\Block\Adminhtml\Agreement\Edit
 * @author Leo Gumbo <leo@wearejh.com>
 */
class Form extends AgreementsForm
{

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('checkout_agreement');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Terms and Conditions Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('agreement_id', 'hidden', ['name' => 'agreement_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Condition Name'),
                'title'    => __('Condition Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label'    => __('Status'),
                'title'    => __('Status'),
                'name'     => 'is_active',
                'required' => true,
                'options'  => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );

        $fieldset->addField(
            'is_html',
            'select',
            [
                'label'    => __('Show Content as'),
                'title'    => __('Show Content as'),
                'name'     => 'is_html',
                'required' => true,
                'options'  => [0 => __('Text'), 1 => __('HTML')]
            ]
        );

        $fieldset->addField(
            'mode',
            'select',
            [
                'label'    => __('Applied'),
                'title'    => __('Applied'),
                'name'     => 'mode',
                'required' => true,
                'options'  => $this->agreementModeOptions->getOptionsArray()
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'stores',
                'multiselect',
                [
                    'name'     => 'stores[]',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->_systemStore->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );

            /**
             * Applying fix for MAGETWO-60143
             * Changed $model->setStoreId to $model->setStores
             */
            $model->setStores($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'checkbox_text',
            'editor',
            [
                'name'     => 'checkbox_text',
                'label'    => __('Checkbox Text'),
                'title'    => __('Checkbox Text'),
                'rows'     => '5',
                'cols'     => '30',
                'wysiwyg'  => false,
                'required' => true
            ]
        );

        $fieldset->addField(
            'content',
            'editor',
            [
                'name'     => 'content',
                'label'    => __('Content'),
                'title'    => __('Content'),
                'style'    => 'height:24em;',
                'wysiwyg'  => false,
                'required' => true
            ]
        );

        $fieldset->addField(
            'content_height',
            'text',
            [
                'name'      => 'content_height',
                'label'     => __('Content Height (css)'),
                'title'     => __('Content Height'),
                'maxlength' => 25,
                'class'     => 'validate-css-length'
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        $grandparent = $this->getGrandparent(self::class);
        return $grandparent::_prepareForm();
    }

    protected function getGrandparent($class) : string
    {
        return get_parent_class(get_parent_class($class));
    }
}
