<?php
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

/** Import library dependencies */
jimport('joomla.plugin.plugin');

/**
 * @version	1.5
 * @package	Phplist
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
class plgPhplistBatchModify extends JPlugin {

	/**
	 * Constructor 
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgPhplistBatchModify(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage( '', JPATH_ADMINISTRATOR );
		$element = strtolower( 'com_Phplist' );
		$this->loadLanguage( $element, JPATH_BASE );
		$this->loadLanguage( $element, JPATH_ADMINISTRATOR );
	}

	/**
	 * Checks to make sure that this plugin is the one being called by the component
	 *
	 * @access public
	 * @return mixed Parameter value
	 * @since 1.5
	 */
	function _isMe( $row ) 
	{
		$element = 'batchmodify';
		
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
	 * Tells Component that this is a valid tool
	 * @return unknown_type
	 */
	function onListToolsPhplist( $row )
	{
		$success = false;
		if ($this->_isMe($row)) 
		{
			$success = true;
		}
		return $success;	
	}

	/**
	 * Allows the plugin to prevent itself from being run for whatever reason
	 * @param $row
	 * @return unknown_type
	 */
	function onBeforeDisplayToolPhplist( $row ) 
	{
		$success = null;
		if ($this->_isMe($row)) 
		{
			$success = true;
		}
		return $success;		
	}

	/**
	 * This displays the contents of the plugin
	 * @param $row
	 * @return unknown_type
	 */
	function onDisplayToolPhplist( $row ) 
	{
		$success = false;
		if (!$this->_isMe($row)) 
		{
			return $success;
		}
		
		// Display the summary
			echo $this->_onRenderSummary();

		// Display the results after submission
			echo $this->_onRenderSubmit();
			
		return $success;		
	}
	
	/**
	 * Gets a parameter value
	 *
	 * @access public
	 * @return mixed Parameter value
	 * @since 1.5
	 */
	function _getParameter( $name, $default='' ) 
	{
		$return = "";
		$return = $this->params->get( $name, $default );
		return $return;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function _onRenderSubmit() 
	{
		$mainframe 		= JFactory::getApplication();
		$success = false;
		$html = "";
		
		// Check for request forgeries
		// and that form has been submitted
			if (!JRequest::checkToken()) {
				return $success;
			}
			
			$html = $this->_executeTool();
			
		return $html;
	}
	
	/**
	 * This method returns HTML to be displayed to the user
	 * The HTML can include input options that are processed 
	 * in _onRenderSubmit()
	 *  
	 * @return unknown_type
	 */
	function _onRenderSummary() 
	{
		$success = false;
	
		$html = "";
		
		$html .= '<div class="note">';
		$html .= '<table>';
			$html .= '
                <tr>
                  <td>
					<p>
				  	'.JText::_( 'This tool enables batch modifications to your Phplist users' )
		            .'&nbsp;
		            </p>
				  </td>
                </tr>
			';
		$html .= '</table>';
		$html .= '</div>';
		
		// TODO Change/Remove this
		$html .= "<p>Here you would collect any kind of input you need from the user.</p>";
		
		return $html;
	}

	/**
	 * This performs the action for the tool
	 *  
	 * @return 
	 */	
	function _executeTool() 
	{
        // TODO Change everything below here
        // I'm leaving this section as a sample for you
        // showing that you could effectively do anything here
				
		$message = "<p>Here you would execute the tool and output some kind of success/fail message.</p>";
		return $message;
		
		$database = JFactory::getDBO();
		$date = JFactory::getDate();
		$message = "";

		// get a list of physicalnames from DB
		$db_physicalnames = array();
			// add filenames to be ignored
			$db_physicalnames[] = ".htaccess";
			
		// get list of files in DB			
		$db_files = HelperFile::getAll( '-1' );
		for ($i=0; $i<count($db_files); $i++)
		{
			// put each physical filename into an array
			$db_file = $db_files[$i];
			$db_physicalnames[] = $db_file->physicalname;			
		}

		// get list of files in folder
		jimport( "joomla.filesystem.folder" ); //joomla/filesystem/folder.php (line 28)
		jimport( "joomla.filesystem.file" ); 
		$dfile = new PhplistFile();
		$dir = $dfile->getDirectory();
		$hdd_files = JFolder::files( $dir );

		// foreach file in folder
		if (!is_array($hdd_files))
		{
			return JText::_("No Files Found");
		}
		
		foreach ($hdd_files as $hdd_file)
		{			
			if (!in_array( $hdd_file, $db_physicalnames))
			{
				// if it's name is not in the array of physical filenames
					// create DB entry, unpublished
					unset($newfile);
					$newfile = PhplistHelperFile::getInstance();
					$newfile->physicalname = $hdd_file;
					$newfile->filename = $hdd_file;
					$newfile->fileextension = JFile::getExt( $hdd_file );
					$size = filesize( $dir.DS.$hdd_file )/1024;
					$newfile->filesize = number_format( $size, 2 ).' Kb';
					$newfile->datetime = JHTML::_('date', $date->toMysql(), "%Y-%m-%d %H:%M:%S"); 
					if ($newfile->store())
					{
						// mark file as detected for output in $message
						$message .= "<li>".$hdd_file."</li>";
					}

					
			}			
		}

		if ($message) 
		{ 
			$message = JText::_( "Files found and added" ).": <ul> ". $message ."</ul>"; 
		} else 
		{
			$message = JText::_( "No New Files Found" );	
		}
		
		return $message;
	}
}
