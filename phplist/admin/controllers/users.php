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
defined( '_JEXEC' ) or die( 'Restricted access' );

class PhplistControllerUsers extends PhplistController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'users');

		// Register Extra tasks
		$this->registerTask( 'enroll_flex', 'enroll_flex' );
		$this->registerTask( 'withdraw_flex', 'withdraw_flex' );
		$this->registerTask( 'withdraw_all', 'withdraw_all' );
		$this->registerTask( 'confirmed.enable', 'boolean' );
		$this->registerTask( 'confirmed.disable', 'boolean' );
		$this->registerTask( 'sync', 'synchronizeUsers' );
		$this->registerTask( 'delete', 'delete' );
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelState()
	{
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.id', 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
		$state['filter_username']   = $app->getUserStateFromRequest($ns.'username', 'filter_username', '', '');
		$state['filter_name']   = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
		$state['filter_email']   = $app->getUserStateFromRequest($ns.'email', 'filter_email', '', '');
		$state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
		$state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
		$state['filter_foreignkey_from']    = $app->getUserStateFromRequest($ns.'foreignkey_from', 'filter_foreignkey_from', '', '');
		$state['filter_foreignkey_to']      = $app->getUserStateFromRequest($ns.'foreignkey_to', 'filter_foreignkey_to', '', '');
		$state['filter_html']      = $app->getUserStateFromRequest($ns.'html', 'filter_html', '', '');
		$state['filter_confirmed']      = $app->getUserStateFromRequest($ns.'confirmed', 'filter_confirmed', '', '');
		$state['flex_list']      = JRequest::getVar('flex_list', '0', 'get', 'int');

		foreach (@$state as $key=>$value)
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	/**
	 * change value
	 * @return void
	 */
	function enroll_flex()
	{
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );

		$details = new JObject();
		$details->listid = JRequest::getVar('flex_list', '0', 'post', 'int');

		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$redirect = 'index.php?option=com_phplist&view='.$this->get('suffix').'&flex_list='.$details->listid;
		$redirect = JRoute::_( $redirect, false );

		$model = $this->getModel($this->get('suffix'));
		$row = $model->getTable();

		$cids = JRequest::getVar('cid', array (0), 'post', 'array');
		$num_already = 0;
		$num_added = 0;
		foreach (@$cids as $cid)
		{
			$details->userid = $cid;
				
			if (!$action = PhplistHelperSubscription::storeUserTo( $details ))
			{
				$num_already++;
			}
			else {
				$num_added++;
			}
		}

		if (empty($details->listid))
		{
			$this->message = JText::_('FLEX+ NEWSLETTER NOT SELECTED ERROR');
			//TODO Keep selected user tickboxes checked after this error message
		}
		else
		{
			if (!empty($num_already))
			{
				$this->message .= $num_already. ' ' .JText::_('ALREADY SUBSCRIBED'). '</li><li>';
			}
			if (!empty($num_added))
			{
				$this->message .= $num_added. ' ' .JText::_('USERS SUCCESSFULLY SUBSCRIBED');
			}
		}

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * change value
	 * @return void
	 */
	function withdraw_flex()
	{
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );

		$details = new JObject();
		$details->listid = JRequest::getVar('flex_list', '0', 'post', 'int');

		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$redirect = 'index.php?option=com_phplist&view='.$this->get('suffix').'&flex_list='.$details->listid;
		$redirect = JRoute::_( $redirect, false );

		$model = $this->getModel($this->get('suffix'));
		$row = $model->getTable();
		
		$cids = JRequest::getVar('cid', array (0), 'post', 'array');
		$num_removed = 0;
		$num_already = 0;
		foreach (@$cids as $cid)
		{
			$details->userid = $cid;
				
		if (!$action = PhplistHelperSubscription::removeUserFrom( $details ))
			{
				$num_already++;
			}
			else {
				$num_removed++;
			}
		}

		if (empty($details->listid))
		{
			$this->message = JText::_('FLEX- NEWSLETTER NOT SELECTED ERROR');
			// TODO keep selected checkboxes after this error
		}
		else
		{
			if (!empty($num_already))
			{
				$this->message .= $num_already. ' ' .JText::_('ALREADY UNSUBSCRIBED'). '</li><li>';
			}
			if (!empty($num_removed))
			{
				$this->message .= $num_removed. ' ' .JText::_('USERS SUCCESSFULLY UNSUBSCRIBED');
			}
		}

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * change value
	 * @return void
	 */
	function withdraw_all()
	{
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phplist'.DS.'models' );

		$details = new JObject();
		$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
		$newsletters= $model->getList();

		$details->listid = JRequest::getVar('flex_list', '0', 'post', 'int');

		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$redirect = 'index.php?option=com_phplist&view='.$this->get('suffix');
		$redirect = JRoute::_( $redirect, false );

		$model = $this->getModel($this->get('suffix'));
		$row = $model->getTable();

		$cids = JRequest::getVar('cid', array (0), 'post', 'array');
		foreach (@$cids as $cid)
		{
			$details->userid = $cid;
				
			foreach (@$newsletters as $newsletter)
			{
				$details->listid = $newsletter->id;
				if (!$action = PhplistHelperSubscription::removeUserFrom( $details ))
				{
					$this->message .= $action->errorMsg.", ID: {$cid}";
					$this->messagetype = 'notice';
					$error = true;
				}
			}
		}

		if ($error)
		{
			$this->message = JText::_('UNSUBSCRIBE FAILED') . $this->message;
		}
		else
		{
			$this->message = JText::_('USERS SUCCESSFULLY UNSUBSCRIBED');
		}

		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * synchronizeUsers
	 * @return void
	 */
	function synchronizeUsers()
	{
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		$this->messagetype	= 'message';
		$this->message 		= '';
		$redirect = 'index.php?option=com_phplist&view='.$this->get('suffix');
		$redirect = JRoute::_( $redirect, false );

		$sync = PhplistHelperUser::syncJoomlaUsers('1');
		if ($sync == false)
		{
			$this->message 	=  JText::_('NO SYNCHRONIZTION NEEDED');
		}
		else
		{
			$this->message 	=  JText::_('USERS SUCCESSFULLY SYNCHRONIZED');
				
		}


		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
	/**
	 * Delete Users
	 * @return void
	 */
	function delete()
	{
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );

		$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
		$users= $model->getList();

		$error = false;
		$this->messagetype	= '';
		$this->message 		= '';
		$redirect = 'index.php?option=com_phplist&view='.$this->get('suffix');
		$redirect = JRoute::_( $redirect, false );

		$model = $this->getModel($this->get('suffix'));
		$row = $model->getTable();

		$cids = JRequest::getVar('cid', array (0), 'post', 'array');
		foreach (@$cids as $cid)
		{
			if (!$action = PhplistHelperUser::deleteUser( $cid, 'id' ))
				{
					$this->message .= $action->errorMsg.", ID: {$cid}";
					$this->messagetype = 'notice';
					$error = true;
				}
		}
		if (!$error)
		{
			$this->message 	=  JText::_('USERS SUCCESSFULLY DELETED');
		}
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * save a record
	 * @return void
	 */
	function save()
	{
		$error = false;
		JLoader::import( 'com_phplist.helpers.user', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.newsletter', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
		$this->messagetype	= '';
		$this->message 		= '';
		$model 	= $this->getModel( $this->get('suffix') );
		$row = $model->getTable();
		$row->load( $model->getId() );
		$row->bind( $_POST );
		$redirect = 'index.php?option=com_phplist&view='.$this->get('suffix').'&task=edit&id='.$row->id;
		$redirect = JRoute::_( $redirect, false );
		
		$config =& JFactory::getConfig();
		$date = JFactory::getDate();
		$date->setOffset($config->getValue('config.offset'));
		$email = JRequest::getVar( 'email' );
		$row->uniqid = PhplistHelperUser::getUniqid();
		$row->entered = $date->toMySQL(true);

		// if not a valid email address, fail w/message
		jimport( 'joomla.mail.helper' );
		$isValidEmail = JMailHelper::isEmailAddress( $email );
		if (!$isValidEmail)
		{
			$this->messagetype = 'notice';
			$this->message .= JText::_( "PLEASE ENTER A VALID EMAIL ADDRESS" );
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}
		
		//check if email already exists in phplist (only on new, not edit)
		if ($row->id <= 0 && PhplistHelperUser::getUserByEmail($email) == true)
		{
			$this->messagetype = 'notice';
			$this->message .= JText::_( "EMAIL ALREADY EXISTS" );
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		if ( $row->save() )
		{
			$model->setId( $row->id );
			$this->messagetype 	= 'message';
			$this->message  	= JText::_( 'USER SAVED' );
				

			// update attributes
			$attributes = PhplistHelperAttribute::getAttributes();
			if ($attributes)
			{
				foreach ($attributes as $a)
				{
					//replace spaces with _ in input names
					$name = str_replace(' ','_',$a->name);
					$name = str_replace('.','_',$name);
					$attributeValue = JRequest::getVar( $name);

					//get CSV for checkboxgroup
					$Value ="";
					if ($a->type == 'checkboxgroup' && $attributeValue != '')
					{
						$Value .= '';
						foreach ($attributeValue as $tickbox)
						{
							$Value .= $tickbox . ',';
						}
						$attributeValue = $Value;
					}

					if ($a->id > 0)
					{
						$insert = PhplistHelperAttribute::insertAttributeValue( $row->id, $a->id, $attributeValue );
					}
				}
			}
			 
			// Get the array of adduserto[] from request
			$adduserto = JRequest::getVar( 'adduserto', '', 'request', 'array' );
			$details = '';
			$details->userid = $row->id;
			 
			// update subscriptions
			$newsletters = PhplistHelperNewsletter::getNewsletters();
			if ($newsletters)
			{
				foreach ($newsletters as $d)
				{
					$details->listid = $d->id;

					if ($d->id > 0)
					{
						if (!isset($adduserto[$d->id]))
						{
							// Remove from
							$remove = PhplistHelperSubscription::removeUserFrom( $details );
							echo  "remove from ". $d->id;
						}
						elseif (isset($adduserto[$d->id]))
						{
							$is = PhplistHelperSubscription::isUser( $details->userid, $details->listid );
							if ($is != 'true')
							{
								// Add to
								$add = PhplistHelperSubscription::addUserTo( $details );
							}
						}
					}
				}
				 
			}
				
				
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger( 'onAfterSave'.$this->get('suffix'), array( $row ) );
		}
		else
		{
			$this->messagetype 	= 'notice';
			$this->message 		= JText::_( 'SAVE FAILED' )." - ".$row->getError();
		}

		$redirect = "index.php?option=com_phplist";
		$task = JRequest::getVar('task');
		switch ($task)
		{
			case "savenew":
				$redirect .= '&view='.$this->get('suffix').'&layout=form';
				break;
			case "apply":
				$redirect .= '&view='.$this->get('suffix').'&layout=form&id='.$model->getId();
				break;
			case "save":
			default:
				$redirect .= "&view=".$this->get('suffix');
				break;
		}

		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
}




?>