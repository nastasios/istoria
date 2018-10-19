<?php
$parms 		= $displayData['parms'];
$element 	= $displayData['element'];

$value 			= $element->values['VALUE'];
$size			= $element->settings->get('slt_size', $parms[0]);	
$maxLength		= $element->settings->get('slt_max_length', $parms[1]);	
$attributes		= $element->settings->get('slt_attributes');	
$htmlInputType	= $element->settings->get('html_inputtype', 'text');

echo $element->renderTextBox($element->elementId, $value, $size, $maxLength, $attributes, $htmlInputType);

if(JFactory::getApplication()->isSite())
{
//	echo $element->renderRequiredText($contentTypeSettings);
//	echo $element->getFieldDescription($translatedFields);
}
?>
<input type="hidden" name="hid<?php echo $element->elementId; ?>" value="<?php echo $element->internal['fieldcontentid']; ?>" />
