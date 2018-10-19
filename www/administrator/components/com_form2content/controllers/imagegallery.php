<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.controllerform');

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'utils.form2content.php');

class Form2ContentControllerImagegallery extends JControllerForm
{
	public function getModel($name = 'ImageGallery', $prefix = 'Form2ContentModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function upload()
	{
		$maxImageWidth		= 400;
		$maxImageHeight		= 400;
		$maxThumbHeight 	= 100;
		$maxThumbWidth 		= 100;
		$f2cConfig 			= F2cFactory::getConfig();
		$upload				= $this->input->get('upload', array(), 'array');
		$formId				= $this->input->getInt('formid');
		$fieldId			= $this->input->getInt('fieldid');
		$contentTypeId		= $this->input->getInt('projectid');
		$galleryDir			= JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'stories'.DIRECTORY_SEPARATOR.'com_form2content';
		$galleryThumbsDir	= '';
		
		if($formId)
		{
			$galleryDir = Path::Combine($galleryDir, 'p'.$contentTypeId.DIRECTORY_SEPARATOR.'f'.$formId.DIRECTORY_SEPARATOR.'gallery'.$fieldId);
		}
		else 
		{
			$session 	= JFactory::getSession();
			$galleryDir = Path::Combine($galleryDir, 'gallerytmp'.DIRECTORY_SEPARATOR.$session->getId());
		}
		
		$galleryThumbsDir = Path::Combine($galleryDir, 'thumbs');
		
		if(!JFolder::exists($galleryThumbsDir))
		{
			JFolder::create($galleryThumbsDir);
		}
		
		$fileName			= $upload['name'];
		$tmpFileLocation	= Path::Combine($galleryDir, '~'.$fileName);
		
		if(JFile::upload($upload['tmp_name'], $tmpFileLocation))
		{
			// Resize image
			if(!ImageHelper::ResizeImage($tmpFileLocation, Path::Combine($galleryDir ,$fileName), $maxImageWidth, $maxImageHeight, $f2cConfig->get('jpeg_quality', 75)))
			{
				throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_IMAGE_RESIZE_FAILED'));
				return false;
			}
	
			// Resize thumbnail
			if(!ImageHelper::ResizeImage($tmpFileLocation, Path::Combine($galleryThumbsDir ,$fileName), $maxThumbWidth, $maxThumbHeight, $f2cConfig->get('jpeg_quality', 75)))
			{
				throw new Exception(JText::_('COM_FORM2CONTENT_ERROR_IMAGE_RESIZE_FAILED'));
				return false;
			}
			
			// remove tmp image
			JFile::delete($tmpFileLocation);

			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration('window.addEvent(\'domready\', function()
										{ 
											addNewRow(\'t'.$fieldId.'\', \'\','.json_encode($fileName).','.$maxImageWidth.','.$maxImageHeight.','.$maxThumbWidth.','.$maxThumbHeight.'); 
										});');
		}
		
		parent::display();
	}
	
	function delete()
	{
		$f2cConfig 			= F2cFactory::getConfig();
		$upload				= $this->input->get('upload', array(), 'array');
		$formId				= $this->input->getInt('formid');
		$contentTypeId		= $this->input->getInt('projectid');
		$fileName			= $this->input->getString('imageid');
		$galleryDir			= JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'stories'.DIRECTORY_SEPARATOR.'com_form2content';
		$galleryThumbsDir	= '';
		
		if($formId)
		{
			$galleryDir = Path::Combine($galleryDir, 'p'.$contentTypeId.DIRECTORY_SEPARATOR.'f'.$formId.DIRECTORY_SEPARATOR.'gallery'.$field->id);
		}
		else 
		{
			$session 	= JFactory::getSession();
			$galleryDir = Path::Combine($galleryDir, 'gallerytmp'.DIRECTORY_SEPARATOR.$session->getId());
		}
		
		$galleryThumbsDir = Path::Combine($galleryDir, 'thumbs');
		
		if(JFile::exists(Path::Combine($galleryDir, $fileName)))
		{
			JFile::delete(Path::Combine($galleryDir, $fileName));
		}

		if(JFile::exists(Path::Combine($galleryThumbsDir, $fileName)))
		{
			JFile::delete(Path::Combine($galleryThumbsDir, $fileName));
		}		
	}
}
?>