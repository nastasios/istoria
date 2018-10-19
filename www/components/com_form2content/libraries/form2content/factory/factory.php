<?php
// No direct access
defined('JPATH_BASE') or die;

/**
 * Factory class containing helper functions
 * 
 * This class contains factory methods for useful objects when building Form2Content applications
 * 
 * @package     Joomla.Site
 * @subpackage  com_form2content
 * @since       6.8.0
 */
abstract class F2cFactory
{
	
	/**
	 * Form2Content configuration object
	 *
	 * @var    JRegistry
	 * @since  6.8.0
	 */
	private static $config 			= null;
	
	/**
	 * Array of cached Form2Content Content Type objects
	 *
	 * @var    array
	 * @since  6.8.0
	 */
	private static $arrContentType 	= array();

	/**
	 * Array of cached Form2Content Article objects
	 *
	 * @var    array
	 * @since  6.11.0
	 */
	private static $arrArticles 	= array();
	
	/**
	 * Get a F2C configuration object
	 *
	 * Returns the global {@link JRegistry} object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return JRegistry object
	 */
	public static function getConfig()
	{
		if (!self::$config) 
		{
			self::$config = self::_createConfig();
		}

		return self::$config;
	}
	
	/**
	 * Create a F2C configuration object
	 *
	 * Load the F2C configuration and return the object
	 *
	 * @return JRegistry object
	 */
	private static function _createConfig()
	{
		$config 		= new JRegistry();		
		$paramvalues 	= JComponentHelper::getParams('com_form2content');
		
		$config->loadString($paramvalues);

		// Some hard-coded read-only settings
		$config->set('f2c_pro', true);
		$config->set('template_path', JPATH_SITE.'/media/com_form2content/templates/');
		
		// Set some defaults
		if($config->get('generate_sample_template') == '')
		{
			$config->set('generate_sample_template', '1');
		}

		if($config->get('default_thumbnail_width') == '')
		{
			$config->set('default_thumbnail_width', '100');
		}

		if($config->get('default_thumbnail_height') == '')
		{
			$config->set('default_thumbnail_height', '100');
		}

		if($config->get('jpeg_quality') == '')
		{
			$config->set('jpeg_quality', '75');
		}
		
		if($config->get('date_format') == '')
		{
			$config->set('date_format', '%d-%m-%Y');
		}
		
		if($config->get('autosync_article_order') == '')
		{
			$config->set('autosync_article_order', '1');
		}

		if($config->get('edit_items_level') == '')
		{
			$config->set('edit_items_level', '0');
		}
		
		if($config->get('front_end_publish') == '')
		{
			$config->set('front_end_publish', '1');
		}
		
		if($config->get('images_path') == '')
		{
			$config->set('images_path', 'images/stories/com_form2content');
		}
		
		if($config->get('files_path') == '')
		{
			$config->set('files_path', 'media/com_form2content/documents');
		}
		
		return $config;
	}
	
	/**
	 * Retrieve a F2C Content Type and return it
	 *
	 * @param	int			$contentTypeId			Id of the Content Type
	 * @param	boolean		$addToCache				True when the Content Type should be added to the cache
	 *
	 * @return object
	 */
	public static function getContentType($contentTypeId, $addToCache = true)
	{
		// Check if the Content Type is alreay present in the cache
		if(array_key_exists($contentTypeId, self::$arrContentType))
		{
			return self::$arrContentType[$contentTypeId];
		}

		// Load the Content Type and add it to the array
		if(!class_exists('Form2ContentModelProject'))
		{
			require_once(JPATH_SITE.'/components/com_form2content/models/project.php');
			require_once(JPATH_ADMINISTRATOR.'/components/com_form2content/tables/project.php');
		}
		
		$model = new Form2ContentModelProject();
		$contentType = $model->getItem($contentTypeId);
		
		if($addToCache)
		{
			// Add the Content Type to the cache
			self::$arrContentType[$contentTypeId] = $contentType;
		}
		
		return $contentType;
	}
	
	/**
	 * Retrieve a F2C Article and return it
	 *
	 * @param	int			$id					Id of the F2C Article
	 * @param	boolean		$addToCache			True when the F2C Article should be added to the cache
	 *
	 * @return object
	 */
	public static function getArticle($id, $addToCache = false)
	{
		// Check if the Content Type is alreay present in the cache
		if(array_key_exists($id, self::$arrArticles))
		{
			return self::$arrArticles[$id];
		}

		// Load the Content Type and add it to the array
		if(!class_exists('Form2ContentModelForm'))
		{
			require_once(JPATH_SITE.'/components/com_form2content/models/form.php');
			require_once(JPATH_ADMINISTRATOR.'/components/com_form2content/tables/form.php');
		}
		
		$model = new Form2ContentModelForm();
		$article = $model->getItem($id);
		
		if($addToCache)
		{
			// Add the Article to the cache
			self::$arrArticles[$id] = $article;
		}
		
		return $article;
	}
}
?>