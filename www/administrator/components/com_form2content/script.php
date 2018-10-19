<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

/**
 * Script file of Form2Content component
 */
class com_Form2ContentInstallerScript
{
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        function preflight($type, $parent) 
        {
        	$joomlaVersionRequired = '3.1.5';
        	
        	if(!$this->checkJoomlaVersion($joomlaVersionRequired))
        	{
        		JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_FORM2CONTENT_JOOMLA_VERSION_TOO_LOW'), $joomlaVersionRequired), 'error');
        		return false;
        	}

		 	if(!(extension_loaded('gd') && function_exists('gd_info')))
		 	{
		 		JFactory::getApplication()->enqueueMessage(JText::_('COM_FORM2CONTENT_GDI_NOT_INSTALLED'), 'warning');
		 	}
        	
        	return true;
        }
	
    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent) 
    {
    	$this->__createPath(JPATH_SITE . '/images/stories/com_form2content');
    	$this->__createPath(JPATH_SITE . '/media/com_form2content/templates');
    	$this->__createPath(JPATH_SITE . '/media/com_form2content/documents');
    	$this->__createPath(JPATH_SITE . '/media/com_form2content/import/archive');
    	$this->__createPath(JPATH_SITE . '/media/com_form2content/import/error');
    	$this->__createPath(JPATH_SITE . '/media/com_form2content/export');
		?>	
		<div align="left">
		<img src="../media/com_form2content/images/OSD_logo.png" width="350" height="180" border="0">
		<h2><?php JText::_('COM_FORM2CONTENT_WELCOME_TO_F2C'); ?></h2>
		<p>&nbsp;</p>	
		<p><strong><?php echo JText::_('COM_FORM2CONTENT_INSTALL_SAMPLE_DATA_QUESTION'); ?></strong></p>
		<p><?php echo JText::_('COM_FORM2CONTENT_INSTALL_SAMPLE_DATA_RECOMMEND'); ?></p>
		<p>
			<button class="btn btn-large btn-success" onclick="location.href='index.php?option=com_form2content&task=projects.installsamples';return false;" href="#">
				<i class="icon-apply icon-white"></i>
				<?php echo JText::_('COM_FORM2CONTENT_YES_INSTALL_SAMPLE_DATA'); ?>
			</button>
			<button class="btn btn-large btn-danger" onclick="location.href='index.php?option=com_form2content';return false;" href="#">
				<i class="icon-apply icon-white"></i>
				<?php echo JText::_('COM_FORM2CONTENT_NO_DO_NOT_INSTALL_SAMPLE_DATA'); ?>
			</button>
		</p>
		</div>
		<?php        	
        }
 
        /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent) 
    {
    }
 
        /**
     * method to update the component
     *
     * @return void
     */
        function update($parent) 
        {
        	// Update F2C Lite to F2C Pro
	    	$this->__createPath(JPATH_SITE . '/media/com_form2content/documents');
        	$this->__createPath(JPATH_SITE . '/media/com_form2content/import/archive');
	    	$this->__createPath(JPATH_SITE . '/media/com_form2content/import/error');
	    	$this->__createPath(JPATH_SITE . '/media/com_form2content/export');
        				
			$db = JFactory::getDBO();
						
			// Remove the sectionid column
			$db->setQuery('SHOW COLUMNS FROM #__f2c_form LIKE \'sectionid\'');
			
			if($db->loadResult())
			{
				$db->setQuery('ALTER TABLE #__f2c_form DROP COLUMN `sectionid`');
				$db->execute();
			}
			
			// add extended column (release 6.3.0)
			$db->setQuery('SHOW COLUMNS FROM #__f2c_form LIKE \'extended\'');
			
			if(!$db->loadResult())
			{
				$db->setQuery('ALTER TABLE #__f2c_form ADD COLUMN `extended` TEXT NOT NULL  AFTER `language`');
				$db->execute();
			}	

			// add name column to fieldtype table (release 6.8.0)
			$db->setQuery('SHOW COLUMNS FROM #__f2c_fieldtype LIKE \'name\'');
			
			if(!$db->loadResult())
			{
				$db->setQuery('ALTER TABLE #__f2c_fieldtype ADD COLUMN `name` VARCHAR(45) NOT NULL  AFTER `description`');
				$db->execute();

				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Singlelinetext' WHERE description = 'Single line text (textbox)'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Multilinetext' WHERE description = 'Multi-line text (text area)'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Editor' WHERE description = 'Multi-line text (editor)'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Checkbox' WHERE description = 'Check box'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Singleselectlist' WHERE description = 'Single select list'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Image' WHERE description = 'Image'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Iframe' WHERE description = 'IFrame'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Email' WHERE description = 'E-mail'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Hyperlink' WHERE description = 'Hyperlink'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Multiselectlist' WHERE description = 'Multi select list (checkboxes)'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Infotext' WHERE description = 'Info Text'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Datepicker' WHERE description = 'Date Picker'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Displaylist' WHERE description = 'Display List'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Fileupload' WHERE description = 'File Upload'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Databaselookup' WHERE description = 'Database Lookup'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Geocoder' WHERE description = 'Geo Coder'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Databaselookupmulti' WHERE description = 'Database Lookup (Multi select)'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Imagegallery' WHERE description = 'Image Gallery'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Colorpicker' WHERE description = 'Color Picker'");
				$db->execute();
				
				// Change the id column to auto increment
				$db->setQuery('ALTER TABLE #__f2c_fieldtype MODIFY COLUMN id int(10) unsigned NOT NULL auto_increment');
				$db->execute();
			}
			
			// Add missing fields
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Check box', 'Checkbox' FROM #__f2c_fieldtype WHERE name = 'Checkbox' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();			
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'IFrame', 'Iframe' FROM #__f2c_fieldtype WHERE name = 'Iframe' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'E-mail', 'Email' FROM #__f2c_fieldtype WHERE name = 'Email' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Hyperlink', 'Hyperlink' FROM #__f2c_fieldtype WHERE name = 'Hyperlink' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Multi select list (checkboxes)', 'Multiselectlist' FROM #__f2c_fieldtype WHERE name = 'Multiselectlist' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
		
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Info Text', 'Infotext' FROM #__f2c_fieldtype WHERE name = 'Infotext' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
		
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Date Picker', 'Datepicker' FROM #__f2c_fieldtype WHERE name = 'Datepicker' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
		
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Display List', 'Displaylist' FROM #__f2c_fieldtype WHERE name = 'Displaylist' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
		
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'File Upload', 'Fileupload' FROM #__f2c_fieldtype WHERE name = 'Fileupload' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Database Lookup', 'Databaselookup' FROM #__f2c_fieldtype WHERE name = 'Databaselookup' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
		
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Geo Coder', 'Geocoder' FROM #__f2c_fieldtype WHERE name = 'Geocoder' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();		
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Database Lookup (Multi select)', 'Databaselookupmulti' FROM #__f2c_fieldtype WHERE name = 'Databaselookupmulti' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();

			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Image Gallery', 'Imagegallery' FROM #__f2c_fieldtype WHERE name='Imagegallery' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`) SELECT 'Color Picker', 'Colorpicker' FROM #__f2c_fieldtype WHERE name='Colorpicker' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			// Add the modified_by column to the #__f2c_form table (release 6.13.0)
			$db->setQuery('SHOW COLUMNS FROM #__f2c_form LIKE \'modified_by\'');
			
			if(!$db->loadResult())
			{
				$db->setQuery('ALTER TABLE #__f2c_form ADD COLUMN `modified_by` int(10) unsigned NOT NULL DEFAULT \'0\' AFTER `modified`');
				$db->execute();
			}
			
			// Increase attribute column size from 10 to to 32
			$db->setQuery('ALTER TABLE #__f2c_fieldcontent MODIFY COLUMN attribute VARCHAR(32)');
			$db->execute();

			$adminFieldPath = JPATH_ADMINISTRATOR.'/components/com_form2content/models/fields/';
			
			// remove files that were present from an earlier version
			if(JFile::exists($adminFieldPath.'f2ccategory.php'))
			{
				JFile::delete($adminFieldPath.'f2ccategory.php');
			}
			
			if(JFile::exists($adminFieldPath.'f2ctemplate.php'))
			{
				JFile::delete($adminFieldPath.'f2ctemplate.php');
			}
        }
 
    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent) 
    {
    	if($type == 'install' || $type == 'update')
    	{
    		$this->__setImportExportDefaults();
    	}
    }
	
    function __createPath($path)
    {
		if(!JFolder::exists($path))
		{
			JFolder::create($path, 0775);
		}
    }
    
    function __setImportExportDefaults()
    {
		$db = JFactory::getDBO();		
		$db->setQuery('SELECT extension_id FROM #__extensions WHERE name=\'com_form2content\'');
		
		$extensionId = $db->loadResult();

    	$configTable =  JTable::getInstance('extension');
		$configTable->load($extensionId);
		
		$params = new JRegistry($configTable->params);

    	if($params->get('import_dir') == '' && $params->get('export_dir') == '' && 
    	   $params->get('import_archive_dir') == '' && $params->get('import_error_dir') == '')
  		{
  			$params->set('import_dir', JPATH_SITE . '/media/com_form2content/import');
  			$params->set('export_dir', JPATH_SITE . '/media/com_form2content/export');
  			$params->set('import_archive_dir', JPATH_SITE . '/media/com_form2content/import/archive');
  			$params->set('import_error_dir', JPATH_SITE . '/media/com_form2content/import/error');
  		}

  		$configTable->params = $params->toString();
		$configTable->store();  		
    }
    
    private function checkJoomlaVersion($versionNumber)
    {
    	$version = new JVersion();
    	return $version->isCompatible($versionNumber);
    }
}
?>