<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.controllerform');

class Form2ContentControllerImagegallery extends JControllerForm
{
	public function getModel($name = 'ImageGallery', $prefix = 'Form2ContentModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}	
}
?>