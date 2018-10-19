<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldGeocoder extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'gcd';
	}
	
	public function reset()
	{
		$this->values['ADDRESS']		= '';
		$this->values['LAT']			= '';
		$this->values['LON']			= '';						
		$this->internal['addressid']	= null;
		$this->internal['latid']		= null;
		$this->internal['lonid']		= null;
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData						= array();
		$displayData['latLonDisplay'] 		= ($this->values['LAT'] && $this->values['LON']) ? '('.$this->values['LAT'].', '.$this->values['LON'].')' : '';
		$displayData['latOnMap'] 			= $displayData['latLonDisplay'] ? $this->values['LAT'] : $this->settings->get('gcd_map_lat', '55.166085');
		$displayData['lonOnMap'] 			= $displayData['latLonDisplay'] ? $this->values['LON'] : $this->settings->get('gcd_map_lon', '10.712890');
		$displayData['attributesLookup']	= $this->settings->get('gcd_attributes_lookup_lat_lon', 'class="btn"');
		$displayData['attributesClear']		= $this->settings->get('gcd_attributes_clear_results', 'class="btn"');
		$displayData['attributesAddress']	= $this->settings->get('gcd_attributes_address', 'class="inputbox"');		
		
		return $this->renderLayout('geocoder', $displayData, $translatedFields, $contentTypeSettings);
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['addressid'] 	= $jinput->getInt('hid'.$this->elementId.'_address');
		$this->internal['latid'] 		= $jinput->getInt('hid'.$this->elementId.'_lat');
		$this->internal['lonid'] 		= $jinput->getInt('hid'.$this->elementId.'_lon');
		$this->values['ADDRESS']		= $jinput->getString($this->elementId.'_address');
		$this->values['LAT']			= $jinput->getString($this->elementId.'_hid_lat');
		$this->values['LON']			= $jinput->getString($this->elementId.'_hid_lon');
		
		return $this;
	}
	
	public function store($formid)
	{
		$addressId		= $this->internal['addressid'];
		$addressValue 	= $this->values['ADDRESS'];		
		$latId			= $this->internal['latid'];
		$latValue		= $this->values['LAT'];
		$lonId			= $this->internal['lonid'];
		$lonValue 		= $this->values['LON'];		
				
		if($addressId)
		{
			// existing record
			$action = (!$addressValue && !$latValue && !$lonValue) ? 'DELETE' : 'UPDATE';
		}
		else
		{
			// new record
			$action = ($addressValue || $latValue || $lonValue) ? 'INSERT' : '';
		}
		
		$content 	= array();					
		$content[] 	= new F2cFieldHelperContent($addressId, 'ADDRESS', $addressValue, $action);
		$content[] 	= new F2cFieldHelperContent($latId, 'LAT', $latValue, $action);
		$content[] 	= new F2cFieldHelperContent($lonId, 'LON', $lonValue, $action);
		
		return $content;					
	}
	
	public function validate()
	{
		if($this->settings->get('requiredfield'))
		{
			if(!(trim($this->values['ADDRESS']) && $this->values['LAT'] && $this->values['LON']))		
			{
				throw new Exception($this->getRequiredFieldErrorMessage());
			}
		}
	}
	
	public function getClientSideInitializationScript()
	{
		static $initialized = false;
		
		$script = '';
		
		if(!$initialized)
		{
			$key = $this->settings->get('api_key');
			
			if($key)
			{
				$key = '&key=' . $key;
			}
			
			$script .= parent::getClientSideInitializationScript();
			JHtml::script('com_form2content/f2c_google.js', false, true);
			JHtml::script(JUri::getInstance()->getScheme().'://maps.google.com/maps/api/js?sensor=false'.$key);		
			JFactory::getDocument()->addScriptDeclaration('window.addEvent(\'load\', function() { geocoder = new google.maps.Geocoder(); });');		
			$initialized = true;
			$script .= "var geocoder;\n";
		}
		
		$script .= "var t".$this->id."_map=null;\n";	
		$script .= "var t".$this->id."_marker=null;\n";										
		
		return $script;
	}
	
	public function copy($formId)
	{
		$this->internal['addressid'] = null;
		$this->internal['latid'] = null;
		$this->internal['lonid'] = null;
	}
	
	public function export($xmlFields, $formId)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentGeocoder');
      	$xmlFieldContent->address = $this->values['ADDRESS'];
      	$xmlFieldContent->lat = $this->values['LAT'];
      	$xmlFieldContent->lon= $this->values['LON'];
    }
    
	public function import($xmlField, $existingInternalData, $formId)
	{
		$this->values['ADDRESS'] = (string)$xmlField->contentGeocoder->address;
		$this->values['LAT'] = (string)$xmlField->contentGeocoder->lat;
		$this->values['LON'] = (string)$xmlField->contentGeocoder->lon;
		$this->internal['addressid'] = $formId ? $existingInternalData['addressid'] : 0;
		$this->internal['latid'] = $formId ? $existingInternalData['latid'] : 0;
		$this->internal['lonid'] = $formId ? $existingInternalData['lonid'] : 0;
	}
	
	public function addTemplateVar($templateEngine, $form)
	{
		if($this->values)
		{
			$templateEngine->addVar($this->fieldname.'_ADDRESS', $this->stringHTMLSafe($this->values['ADDRESS']));
			$templateEngine->addVar($this->fieldname.'_LAT', $this->values['LAT']);
			$templateEngine->addVar($this->fieldname.'_LON', $this->values['LON']);
		}
		else
		{
			$templateEngine->addVar($this->fieldname.'_ADDRESS', '');
			$templateEngine->addVar($this->fieldname.'_LAT', '');
			$templateEngine->addVar($this->fieldname.'_LON', '');
		}
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(	strtoupper($this->fieldname).'_ADDRESS',
						strtoupper($this->fieldname).'_LAT', 
						strtoupper($this->fieldname).'_LON');
		
		return $names;
	}
	
	public function setData($data)
	{
		$this->values[$data->attribute] = $data->content;
		
		switch($data->attribute)
		{
			case 'ADDRESS':
				$this->internal['addressid'] = $data->fieldcontentid;
				break;
			case 'LAT':
				$this->internal['latid'] = $data->fieldcontentid;
				break;
			case 'LON':
				$this->internal['lonid'] = $data->fieldcontentid;
				break;
		}						
	}
}
?>