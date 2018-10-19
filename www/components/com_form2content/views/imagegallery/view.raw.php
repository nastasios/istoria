<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentViewImagegallery extends JViewLegacy
{
	function display($cachable = false, $urlparams = array())
	{
		echo 'testerdetest';	

		$user = JFactory::getUser();
		
		echo $user->name;
	}
}