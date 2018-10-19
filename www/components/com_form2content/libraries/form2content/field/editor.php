<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldEditor extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'mle';
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData = array();
		
		if(!count($parms))
		{
			$parms = JFactory::getApplication()->isSite() ? array('100%', '400', '70', '15') : array(500, 350, 50, 20);
		}

		$width	= $parms[0];
		$height = $parms[1];
		$col	= $parms[2];
		$row	= $parms[3];
		
		if(	$this->settings->get('mle_num_rows') || 
			$this->settings->get('mle_num_cols') || 
			$this->settings->get('mle_height') || 
			$this->settings->get('mle_width'))
		{
			$width	= $this->settings->get('mle_width');
			$height = $this->settings->get('mle_height');
			$col	= $this->settings->get('mle_num_cols');
			$row	= $this->settings->get('mle_num_rows');
		}

		$displayData['editor']	= JEditor::getInstance(JFactory::getConfig()->get('editor'));
		$displayData['width']	= $width;
		$displayData['height']	= $height;
		$displayData['col']		= $col;
		$displayData['row']		= $row;

		return $this->renderLayout('editor', $displayData, $translatedFields, $contentTypeSettings);				
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['fieldcontentid'] = $jinput->getInt('hid'.$this->elementId);
		$this->values['VALUE'] = $jinput->get($this->elementId, '', 'RAW');
		
		return $this;
	}
	
	public function store($formid)
	{
		$content 	= array();					
		$fieldId 	= $this->internal['fieldcontentid'];
		$value 		= $this->values['VALUE'];		
		$action 	= ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');
		$content[] 	= new F2cFieldHelperContent($fieldId, 'VALUE', $value, $action);
		
		return $content;		
	}
	
	public function validate()
	{
		if($this->settings->get('requiredfield') && empty($this->values['VALUE']))
		{
			throw new Exception($this->getRequiredFieldErrorMessage());
		}
	}
	
	public function getClientSideValidationScript(&$validationCounter)
	{
		$script = '';
		$editor	= JEditor::getInstance(JFactory::getConfig()->get('editor'));

		if($this->settings->get('requiredfield'))
		{
			$script .= 'var editorText'.$this->fieldname.'='.$editor->getContent($this->elementId).';';
			$script .= 'if(!editorText'.$this->fieldname.'.trim().length) {alert(\''.addslashes($this->getRequiredFieldErrorMessage()).'\');return false;} ';
		}
		$script .= $editor->save($this->elementId);
		
		return $script;
	}
	
	public function export($xmlFields, $formId)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentSingleTextValue');
      	$xmlFieldContent->value = $this->values['VALUE'];
    }
    
	public function import($xmlField, $existingInternalData, $formId)
	{
		$this->values['VALUE'] = (string)$xmlField->contentSingleTextValue->value;
		$this->internal['fieldcontentid'] = $formId ? $existingInternalData['fieldcontentid'] : 0;
	}
	
	public function addTemplateVar($templateEngine, $form)
	{
		$templateEngine->addVar($this->fieldname, $this->values['VALUE']);
	}
	
	public function setData($data)
	{
		if($data->attribute)
		{
			$this->values[$data->attribute] 	= $data->content;
			$this->internal['fieldcontentid'] 	= $data->fieldcontentid;
		}
	}
}
?>