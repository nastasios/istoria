<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];

echo HtmlHelper::renderCalendar($displayData['displayValue'], $field->values['VALUE'], $field->elementId, $field->elementId, $field->f2cConfig->get('date_format'), $displayData['attributes']);
?>

<?php if(JFactory::getApplication()->isSite() && $displayData['requiredText'] != '') : ?>
	<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
<?php endif; ?>

<?php if(JFactory::getApplication()->isSite() && $displayData['description'] != '') : ?>
	&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
<?php endif; ?>

<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $field->internal['fieldcontentid']; ?>" >