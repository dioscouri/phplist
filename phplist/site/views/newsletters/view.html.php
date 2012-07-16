<?php
/**
 * @version	1.5
 * @package	Phplist
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

JLoader::import( 'com_phplist.views._base', JPATH_SITE.DS.'components' );

class PhplistViewNewsletters extends PhplistViewBase 
{
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function display($tpl=null)
	{
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "default":
			default:
				$this->_default($tpl);
			  break;
		}	    
		parent::display($tpl);
    }
    
    /**
     * (non-PHPdoc)
     * @see phplist/site/views/PhplistViewBase#_default($tpl)
     */
    function _default($tpl=null)
    {
    	parent::_default($tpl);
    	
    	// get the phplist user
    	JLoader::import( 'com_phplist.library.grid', JPATH_ADMINISTRATOR.DS.'components' );
        JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
        JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
        
        $user = JFactory::getUser();
        $phplistUser = null;
        
        if ($user->id > '0') 
        {
            $phplistUser = PhplistHelperUser::getUser( $user->id, '1', 'foreignkey' );
            if (!isset($phplistUser->id)) 
            {
                $phplistUser = PhplistHelperUser::create( $user );  
            }
        }
            elseif (isset($this->uid))
        {
        	$phplistUser = PhplistHelperUser::getUser( $this->uid, '1', 'uid' );
        }
        
		$this->assign( 'phplistuser', $phplistUser );
		$this->assign('joomlauserID', $user->id);

		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$attributes = PhplistHelperAttribute::getAttributes($frontend = '1');
		$this->assign( 'attributes', $attributes );
		
		//TODO if user comes from UID, and is Joomla! user, log them in.
    }
}

?>