<?php
namespace Alstoone\Hours\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;


/**
 * Class Ranges
 */
class OpenHours extends AbstractFieldArray
{

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('day', ['label' => __('Day'), 'class' => 'required-entry']);        
        $this->addColumn('from_hour', ['label' => __('From'), 'class' => 'required-entry']);
        $this->addColumn('to_hour', ['label' => __('To'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $row->setData('option_extra_attrs', $options);
    }
}
