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

jimport( 'joomla.application.component.controller' );

class PhplistController extends JController 
{
	var $_models = array();
	
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'newsletters');
		
		// Register Extra tasks
		$this->registerTask( 'list', 'display' );
		$this->registerTask( 'unsubscribeAll', 'unsubscribeAll' );
		$this->registerTask( 'unsubscribeall', 'unsubscribeAll' );
		$this->registerTask( 'unsubscribe_all', 'unsubscribeAll' );
		$this->registerTask( 'unsubscribe', 'unsubscribe' );
		$this->registerTask( 'subscribeModule', 'subscribeModule' );
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
    function _setModelState()
    {
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state = array();
		
    	// limitstart isn't working for some reason when using getUserStateFromRequest -- cannot go back to page 1
		$limit  = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', '0', 'request', 'int');
		// If limit has been changed, adjust offset accordingly
		$state['limitstart'] = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $state['limit']  	= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		
        $state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.'.$model->getTable()->getKeyName(), 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'ASC', 'word');
        $state['filter']    = $app->getUserStateFromRequest($ns.'.filter', 'filter', '', 'string');
        $state['id']        = JRequest::getVar('id', 'post', JRequest::getVar('id', 'get', '', 'int'), 'int');

        // TODO santize the filter
        // $state['filter']   	= 

    	foreach (@$state as $key=>$value)
		{
			$model->setState( $key, $value );	
		}
  		return $state;
    }

    /**
     * 
     * @return unknown_type
     */
    function getNamespace()
    {
    	$app = JFactory::getApplication();
    	$model = $this->getModel( $this->get('suffix') );
		$ns = $app->getName().'::'.'com.phplist.model.'.$model->getTable()->get('_suffix');
    	return $ns;
    }
    
    /**
     * We override parent::getModel because parent::getModel was always creating a new Model instance
     *
     */
	function getModel( $name = '', $prefix = '', $config = array() )
	{
		if ( empty( $name ) ) {
			$name = $this->getName();
		}

		if ( empty( $prefix ) ) {
			$prefix = $this->getName() . 'Model';
		}
		
		$fullname = strtolower( $prefix.$name ); 
		if (empty($this->_models[$fullname]))
		{
			if ( $model = & $this->_createModel( $name, $prefix, $config ) )
			{
				// task is a reserved state
				$model->setState( 'task', $this->_task );
	
				// Lets get the application object and set menu information if its available
				$app	= &JFactory::getApplication();
				$menu	= &$app->getMenu();
				if (is_object( $menu ))
				{
					if ($item = $menu->getActive())
					{
						$params	=& $menu->getParams($item->id);
						// Set Default State Data
						$model->setState( 'parameters.menu', $params );
					}
				}
			}
				else 
			{
				$model = new JModel();
			}
			$this->_models[$fullname] = $model;
		}

		return $this->_models[$fullname];
	}
	
	/**
	* 	display the view
	*/
	function display($cachable=false)
	{
    	// if database isn't configured, display notice
    	JLoader::import( 'com_phplist.helpers.phplist', JPATH_ADMINISTRATOR.DS.'components' );
    	$database = PhplistHelperPhplist::getDBO();
		if (isset($database->error)) 
		{
			JError::raiseNotice( 'Database Not Configured', JText::_( "Database Connection Not Configured Please Contact Site Administrator" ) );
			return;
		}
		
		// this sets the default view
		JRequest::setVar( 'view', JRequest::getVar( 'view', 'newsletters' ) );
		
		$document =& JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', $this->getName() );
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );

		$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));

		// Get/Create the model
		if ($model = & $this->getModel($viewName)) 
		{
			// controller sets the model's state - this is why we override parent::display()
			$this->_setModelState();
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($viewLayout);

		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeDisplayComponentPhplist', array() );
		
		// Display the view
		if ($cachable && $viewType != 'feed') {
			global $option;
			$cache =& JFactory::getCache($option, 'view');
			$cache->get($view, 'display');
		} else {
			$view->display();
		}

		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAfterDisplayComponentPhplist', array() );
		
		$config = PhplistConfig::getInstance();
		$show_linkback = $config->get( 'show_linkback', '1' );
		$show_linkback_phplist = $config->get( 'show_linkback_phplist', '1' );
		$url = "http://www.dioscouri.com/";
		if ($amigosid = $config->get( 'amigosid', '' ))
		{
			$url .= "?amigosid=".$amigosid;	
		}
		if ($show_linkback == '1' || $show_linkback_phplist == '1') 
		{
			// show a generous linkback, TIA
			echo "<p align='center'>";
			if ($show_linkback_phplist) { 
				echo JText::_( 'Powered by' )." <a href='http://www.phplist.com' target='_blank'>".JText::_( 'Phplist' )."</a><br/>";
			}  
			if ($show_linkback) {
				echo JText::_( 'Integration by' )." <a href='{$url}' target='_blank'>Dioscouri Design</a>";	
			}
			echo "</p>";
		}		
	}
    
	/**
	 * @return void
	 */
	function view() 
	{
		// TODO Couldn't these eventually be more like elementuser & elementarticle
		JRequest::setVar( 'view', $this->get('suffix') );
		JRequest::setVar( 'layout', 'view' );
		parent::display();
	}
	
	/**
	 * 
	 * @return 
	 */
	function doTask()
	{
		$success = true;
		$msg = new stdClass();
		$msg->message = '';
		$msg->error = '';
				
		// expects $element in URL and $elementTask
		$element = JRequest::getVar( 'element', '', 'request', 'string' );
		$elementTask = JRequest::getVar( 'elementTask', '', 'request', 'string' );

		$msg->error = '1';
		// $msg->message = "element: $element, elementTask: $elementTask";
		
		// gets the plugin named $element
		$import 	= JPluginHelper::importPlugin( 'phplist', $element );
		$dispatcher	=& JDispatcher::getInstance();
		// executes the event $elementTask for the $element plugin
		// returns the html from the plugin
		// passing the element name allows the plugin to check if it's being called (protects against same-task-name issues)
		$result 	= $dispatcher->trigger( $elementTask, array( $element ) );
		// This should be a concatenated string of all the results, 
			// in case there are many plugins with this eventname 
			// that return null b/c their filename != element) 
		$msg->message = implode( '', $result );
			// $msg->message = @$result['0'];
						
		// encode and echo (need to echo to send back to browser)		
		echo $msg->message;
		$success = $msg->message;

		return $success;
	}
	
	/**
	 * 
	 * @return 
	 */
	function doTaskAjax()
	{
	    JLoader::import( 'com_phplist.library.json', JPATH_ADMINISTRATOR.DS.'components' );
		$success = true;
		$msg = new stdClass();
		$msg->message = '';
				
		// get elements $element and $elementTask in URL 
			$element = JRequest::getVar( 'element', '', 'request', 'string' );
			$elementTask = JRequest::getVar( 'elementTask', '', 'request', 'string' );
			
		// get elements from post
			// $elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
			
		// for debugging
			// $msg->message = "element: $element, elementTask: $elementTask";

		// gets the plugin named $element
			$import 	= JPluginHelper::importPlugin( 'phplist', $element );
			$dispatcher	=& JDispatcher::getInstance();
			
		// executes the event $elementTask for the $element plugin
		// returns the html from the plugin
		// passing the element name allows the plugin to check if it's being called (protects against same-task-name issues)
			$result 	= $dispatcher->trigger( $elementTask, array( $element ) );
		// This should be a concatenated string of all the results, 
			// in case there are many plugins with this eventname 
			// that return null b/c their filename != element)
			$msg->message = implode( '', $result );
			// $msg->message = @$result['0'];

		// set response array
			$response = array();
			$response['msg'] = $msg->message;
			
		// encode and echo (need to echo to send back to browser)
			echo ( json_encode( $response ) );

		return $success;
	}

	/**
	 * allows user to manage subscriptions via uid rather than logging in.
	 * Serves as target for subscribe links 
	 * which will be in the format:
	 * index.php?option=com_phplist&task=subscribe&uid=38338f464bb36127b34da1f63de666a5
	 * 
	 * @return unknown_type
	 */
	function subscribe()
	{	
		$uid = JRequest::getVar( 'uid' );
		
		$msg = new stdClass();
		$msg->type 		= "";
		$msg->message 	= "";
		$msg->link 		= "index.php?option=com_phplist&view=subscribe&uid={$uid}";
		
		if ($id = PhplistUrl::getItemid()) {
			$msg->link .= "&Itemid={$id}";
		}
		
		$msg->link 		= JRoute::_( $msg->link, false );
		$this->setRedirect( $msg->link, $msg->message, $msg->type );

	}
	
	/**
	 * allows user to unsubscribe from all newsletters via one-click using uid.
	 * Serves as target for unsubscribe links 
	 * which will be in the format:
	 * index.php?option=com_phplist&task=unsubscribe&uid=38338f464bb36127b34da1f63de666a5
	 * 
	 * @return unknown_type
	 */
	function unsubscribe()
	{		
		$uid = JRequest::getVar( 'uid' );
		
		$msg = new stdClass();
		$msg->type 		= "";
		$msg->message 	= "";
		$msg->link 		= "index.php?option=com_phplist&view=unsubscribe&uid={$uid}";
		
		if ($id = PhplistUrl::getItemid()) {
			$msg->link .= "&Itemid={$id}";
		}
		
		$msg->link 		= JRoute::_( $msg->link, false );
		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}
	
	/**
	 * allows user to forward a single message to another user.
	 * a typical incoming link looks like:
	 * index.php?option=com_phplist&task=forward&uid=38338f464bb36127b34da1f63de666a5&mid=13
	 */
	function forward()
	{
		// TODO Write this function
		return true;
					
		$uid = JRequest::getVar( 'uid' );
		$mid = JRequest::getVar( 'mid' );
		
		$msg = new stdClass();
		$msg->type 		= "";
		$msg->message 	= "";
		$msg->link 		= "index.php?option=com_phplist&view=forward&id={$mid}&uid={$uid}";
		
		if ($id = PhplistUrl::getItemid()) {
			$msg->link .= "&Itemid={$id}";
		}
		
		$msg->link 		= JRoute::_( $msg->link, false );
		$this->setRedirect( $msg->link, $msg->message, $msg->type );
	}
	
	/**
	 * 
	 * @return 
	 */
	function validate()
	{
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.json', JPATH_ADMINISTRATOR.DS.'components' );
		
		$success = true;
		$response = array();
        $response['msg'] = "";
	    $response['error'] = "";
		$msg = new stdClass();
		$msg->message = "";
		$msg->error = "";
		
		//get front end attributes info
		$attributes = PhplistHelperAttribute::getAttributes('1');
		$required_attribs = false;
		
		// get elements from post
			$elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
			// elements is an array of objects
			// $object->name
			// $object->value
			// $object->id (if present, is the element id in the form)
				
		// loop through every field in the form
		// collect the ones to be verified
			for ($i=0; $i<count($elements); $i++) 
			{
				$element = $elements[$i];
				if (trim(strtolower($element->name)) == 'subscriber2add') 
				{
					$email = $element->value;
					
				}
				if (trim(strtolower($element->name)) == 'boxchecked') 
				{
					$cid = $element->value;
				}
				
				// attributes fields
				if ($attributes)
				{
					foreach ($attributes as $a)
					{
						if ($a->required == '1')
						{
							//replace spaces with _ in input names
							$name = str_replace(' ','_',$a->name);
							$name = str_replace('.','_',$name);
								
							if ($element->name == $name)
							{
								$required_attribs[$name]->value = $element->value;
								$required_attribs[$name]->type = $a->type;
								$required_attribs[$name]->name = $a->name;
								$required_attribs[$name]->checked = $element->checked;
							}
						}
					}
				}
			}

			if (isset($email))
			{
				if (empty($email))
				{
					$msg->message .= '<li>"'.JText::_( 'Email' ).'" '.JText::_( 'is Required' ).'</li>';
					$msg->error = '1';
				}
				else
				{
					jimport('joomla.mail.helper');
					if (!$isEmailAddress = JMailHelper::isEmailAddress( $email ))
						
					{
						$msg->message .= '<li>'.JText::_( 'PLEASE ENTER A VALID EMAIL ADDRESS' ).'</li>';
						$msg->error = '1';
					}
					if ($emailExists = PhplistHelperUser::emailExists( $email, '1' ))
					{
						$msg->message .= '<li>'.JText::_( 'EMAIL IS JUSER' ).'</li>';
						$msg->error = '1';
					}
					elseif ($user = PhplistHelperUser::getUser( $email, '1', 'email' ))
					{
						$msg->message .= '<li>'.JText::_( 'EMAIL IS PHPLISTUSER' ).'</li>';
						$msg->error = '1';
					}
				}
			}
			if ($cid == '' || $cid == '0')
			{
				$msg->message .= '<li>'.JText::_( 'PLEASE SELECT A NEWSLETTER' ).'</li>';
				$msg->error = '1';
			}
			
			// validate required Attributes if they exist
			if ($attributes && $required_attribs)
			{
				foreach ($required_attribs as $a)
				{
					switch ($a->type)
					{
						case 'textline':
						case 'textarea':
						case 'date': //TODO make this check for valid date
						default:
							if ($a->value == '')
							{
								$msg->message .= '<li>'.$a->name. ' ' .JText::_( 'IS REQUIRED' ).'</li>';
								$msg->error = '1';
							}
							break;
						case 'checkbox':
						case 'radio':
							if ($a->checked != '1')
							{
								$msg->message .= '<li>'.$a->name. ' ' .JText::_( 'IS REQUIRED' ).'</li>';
								$msg->error = '1';
							}
							break;
						case 'checkboxgroup':
						case 'select':
							// TODO add validation for these
							break;
					}
				}
			}

			// set response array
			if (!empty($msg->error))
			{
				$response['msg'] = '
					<dl id="system-message">
					<dt class="notice">notice</dt>
					<dd class="notice message fade">
						<ul>'.
						$msg->message						
						.'</ul>
					</dd>
					</dl>
				';
				$response['error'] = '1';
			}
			
		// encode and echo (need to echo to send back to browser)
		echo ( json_encode( $response ) );

		return $success;	
	}

	/**
	 * This method processes the form submitted by the mod_subscribe module.
	 * It is just a wrapper for the module's helper, which sets the html
	 * so this entire process is confined to the module itself
	 * 
	 * @return unknown_type
	 */
	function subscribeModule()
	{
		// include the module's helper file
		JLoader::import( 'mod_phplist_subscribe.helper', JPATH_SITE.DS.'modules' );
		// display whatever output comes from the processForm method
		echo modPhplistSubscribeHelper::processForm();
	}
	
	/**
	 * 
	 * @param $message
	 * @return unknown_type
	 */
	function setResponseAjax( $message='', $type='notice' )
	{
		// set response array
			// <li>Warning - Invalid Entries</li>
		
		$txt = "";
		if ($message)
		{
			$title = ucfirst( $type );
			$txt = "
			<dl id='system-message'>
			<dt class='{$type}'>{$title}</dt>
			<dd class='{$type} message fade'>
				<ul>
				{$message}						
				</ul>
			</dd>
			</dl>
			";
				
		}
		
		$response = array();
        $response['msg'] = $txt;
        
		return $response;
	}
	
}
?>