<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldEmail extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'eml';
	}
	
	public function reset()
	{
		$this->values['EMAIL'] 				= '';
		$this->values['DISPLAY_AS'] 		= '';
		$this->internal['fieldcontentid']	= null;
	}
		
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData						= array();
		$displayData['EmailInputType']		= $this->settings->get('html_inputtype', 'text');
		$displayData['attribsEmail']		= $this->settings->get('eml_attributes_email') ? $this->settings->get('eml_attributes_email') : 'class="inputbox"';
		$displayData['attribsDisplayAs']	= $this->settings->get('eml_attributes_display_as') ? $this->settings->get('eml_attributes_display_as') : 'class="inputbox"';
		
		return $this->renderLayout('email', $displayData, $translatedFields, $contentTypeSettings);						
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['fieldcontentid'] = $jinput->getInt('hid'.$this->elementId);
		
		$this->values['EMAIL'] = $jinput->getString($this->elementId);
		$this->values['DISPLAY_AS'] = $jinput->getString($this->elementId . '_display');		
		
		return $this;
	}
	
	public function store($formid)
	{
		$content 		= array();					
		$email 			= new JRegistry();
		$fieldId 		= $this->internal['fieldcontentid'];
				
		$email->set('email', $this->values['EMAIL']);
		$email->set('display', $this->values['DISPLAY_AS']);
		
		$value 			= $email->toString();
		$action 		= ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');
		$content[] 		= new F2cFieldHelperContent($fieldId, 'VALUE', $value, $action);
		
		return $content;		
	}
	
	public function validate()
	{
		if($this->settings->get('requiredfield') && empty($this->values['EMAIL']))
		{
			throw new Exception($this->getRequiredFieldErrorMessage());
		}
	}
	
	public function export($xmlFields, $formId)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
     	$xmlFieldContent = $xmlField->addChild('contentEmail');
      	$xmlFieldContent->email = $this->values['EMAIL'];
      	$xmlFieldContent->display_as = $this->values['DISPLAY_AS'];
    }
    
	public function import($xmlField, $existingInternalData, $formId)
	{
		$this->values['EMAIL'] = (string)$xmlField->contentEmail->email;
		$this->values['DISPLAY_AS'] = (string)$xmlField->contentEmail->display_as;
		$this->internal['fieldcontentid'] = $formId ? $existingInternalData['fieldcontentid'] : 0;
	}
	
	public function addTemplateVar($templateEngine, $form)
	{
		$emailTag = '';
		$emailAddress = '';
		$emailDisplay = '';
				
		if($this->values['EMAIL'])
		{
			$emailDisplay = $this->values['DISPLAY_AS'] ? $this->values['DISPLAY_AS'] : $this->values['EMAIL'];
			$emailTag = '<a href="mailto:' . $this->values['EMAIL'] . '">' . $this->stringHTMLSafe($emailDisplay) . '</a>';
			$emailAddress = $this->values['EMAIL'];
		}
			
		$templateEngine->addVar($this->fieldname, $emailTag);
		$templateEngine->addVar($this->fieldname.'_ADDRESS', $this->values['EMAIL']);
		$templateEngine->addVar($this->fieldname.'_DISPLAY', $this->values['DISPLAY_AS']);
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(	strtoupper($this->fieldname).'_ADDRESS',
						strtoupper($this->fieldname).'_DISPLAY');
		
		return array_merge($names, parent::getTemplateParameterNames());
	}
	
	public function setData($data)
	{
		$this->internal['fieldcontentid']	= $data->fieldcontentid;					
		$values 							= new JRegistry($data->content);
		$this->values['EMAIL'] 				= $values->get('email');
		$this->values['DISPLAY_AS'] 		= $values->get('display');
	}
}
?>