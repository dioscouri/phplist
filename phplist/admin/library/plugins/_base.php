<?php
/**
 * @version 1.5
 * @package Phplist
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

// TODO Make all Phplist plugins extend this _base file, to reduce code redundancy

/** Import library dependencies */
jimport('joomla.plugin.plugin');
jimport('joomla.utilities.string');

class PhplistPluginBase extends JPlugin
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename, 
     *                         forcing it to be unique 
     */
    var $_element    = '';
    
    /**
     * Checks to make sure that this plugin is the one being triggered by the extension
     *
     * @access public
     * @return mixed Parameter value
     * @since 1.5
     */
    function _isMe( $row ) 
    {
        $element = $this->_element;
        
        $success = false;
        if (is_object($row) && !empty($row->element) && $row->element == $element )
        {
            $success = true;
        }
        
        if (is_string($row) && $row == $element ) {
            $success = true;
        }
        
        return $success;
    }
    
    /**
     * Prepares variables for the form
     * 
     * @return string   HTML to display
     */
    function _renderForm()
    {
        $vars = new JObject();
        $html = $this->_getLayout('form', $vars);
        return $html;
    }
    
    /**
     * Prepares the 'view' tmpl layout
     * 
     * @param array
     * @return string   HTML to display
     */
    function _renderView( $options='' )
    {
        $vars = new JObject();
        $html = $this->_getLayout('view', $vars);
        return $html;
    }
    
    /**
     * Wraps the given text in the HTML
     *
     * @param string $text
     * @return string
     * @access protected
     */
    function _renderMessage($message = '')
    {
        $vars = new JObject();
        $vars->message = $message;
        $html = $this->_getLayout('message', $vars);
        return $html;
    }
    
    /**
     * Gets the parsed layout file
     * 
     * @param string $layout The name of  the layout file
     * @param object $vars Variables to assign to
     * @param string $plugin The name of the plugin
     * @param string $group The plugin's group
     * @return string
     * @access protected
     */
    function _getLayout($layout, $vars = false, $plugin = '', $group = 'phplist' )
    {
        if (empty($plugin)) 
        {
            $plugin = $this->_element;
        }
        
        ob_start();
        $layout = $this->_getLayoutPath( $plugin, $group, $layout ); 
        include($layout);
        $html = ob_get_contents(); 
        ob_end_clean();
        
        return $html;
    }
    
    
    /**
     * Get the path to a layout file
     *
     * @param   string  $plugin The name of the plugin file
     * @param   string  $group The plugin's group
     * @param   string  $layout The name of the plugin layout file
     * @return  string  The path to the plugin layout file
     * @access protected
     */
    function _getLayoutPath($plugin, $group, $layout = 'default')
    {
        $app = JFactory::getApplication();

        // get the template and default paths for the layout
        $templatePath = JPATH_SITE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.'plugins'.DS.$group.DS.$plugin.DS.$layout.'.php';
        $defaultPath = JPATH_SITE.DS.'plugins'.DS.$group.DS.$plugin.DS.'tmpl'.DS.$layout.'.php';

        // if the site template has a layout override, use it
        jimport('joomla.filesystem.file');
        if (JFile::exists( $templatePath )) 
        {
            return $templatePath;
        } 
        else 
        {
            return $defaultPath;
        }
    }

    /**
     * This displays the content article
     * specified in the plugin's params
     * 
     * @return unknown_type
     */
    function _displayArticle()
    {
        $html = '';
        
        $articleid = $this->params->get('articleid');
        if ($articleid)
        {
            JLoader::import( 'com_phplist.library.article', JPATH_ADMINISTRATOR.DS.'components' );
            $html = PhplistArticle::display( $articleid );
        }
        
        return $html;
    }
    
    /**
     * Override this to avoid overwriting of other constants
     * (we have custom language file for that)
     * @see JPlugin::loadLanguage()
     */
    function loadLanguage($extension = '', $basePath = JPATH_BASE, $overwrite = false) {
    
    	if (version_compare(JVERSION, '1.6.0', 'ge')) {
    
    		if (empty($extension)) {
    			$extension = 'plg_' . $this -> _type . '_' . $this -> _name;
    		}
    
    		$language = JFactory::getLanguage();
    		$lang = $language -> getTag();
    
    		$path = JLanguage::getLanguagePath($basePath, $lang);
    
    		if (!strlen($extension)) {
    			$extension = 'joomla';
    		}
    		$filename = ($extension == 'joomla') ? $lang : $lang . '.' . $extension;
    		$filename = $path . DS . $filename;
    
    		$result = false;
    		if (isset($language -> _paths[$extension][$filename])) {
    			// Strings for this file have already been loaded
    			$result = true;
    		} else {
    			// Load the language file
    			$result = $language -> load($extension, $basePath, null, $overwrite);
    
    			// Check if there was a problem with loading the file
    			if ($result === false) {
    				// No strings, which probably means that the language file does not exist
    				$path = JLanguage::getLanguagePath($basePath, $language -> getDefault());
    				$filename = ($extension == 'joomla') ? $language -> getDefault() : $language -> getDefault() . '.' . $extension;
    				$filename = $path . DS . $filename . '.ini';
    
    				//				$result = $language->load( $filename, $extension, $overwrite );
    			}
    
    		}
    
    	} else {
    
    		if (empty($extension)) {
    			$extension = 'plg_' . $this -> _type . '_' . $this -> _name;
    		}
    
    		$language = JFactory::getLanguage();
    		$lang = $language -> _lang;
    
    		$path = JLanguage::getLanguagePath($basePath, $lang);
    
    		if (!strlen($extension)) {
    			$extension = 'joomla';
    		}
    
    		$filename = ($extension == 'joomla') ? $lang : $lang . '.' . $extension;
    		$filename = $path . DS . $filename . '.ini';
    
    		$result = false;
    		if (isset($language -> _paths[$extension][$filename])) {
    			// Strings for this file have already been loaded
    			$result = true;
    		} else {
    			// Load the language file
    			$result = $language -> _load($filename, $extension, $overwrite);
    
    			// Check if there was a problem with loading the file
    			if ($result === false) {
    				// No strings, which probably means that the language file does not exist
    				$path = JLanguage::getLanguagePath($basePath, $language -> _default);
    				$filename = ($extension == 'joomla') ? $language -> _default : $language -> _default . '.' . $extension;
    				$filename = $path . DS . $filename . '.ini';
    
    				$result = $language -> _load($filename, $extension, $overwrite);
    			}
    
    		}
    
    	}
    
    	return $result;
    
    }
    
}