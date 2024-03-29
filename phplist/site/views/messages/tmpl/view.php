<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('stylesheet', 'phplist.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('stylesheet', 'menu.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_phplist/js/'); ?>
<?php $state = @$this->state; ?>
<?php $form = @$this->form; ?>
<?php $items = @$this->items; ?>
<?php $row = @$this->row; ?>

		<div class='componentheading'>
    		<?php echo JText::_( "VIEW_MESSAGE" ); ?>
		</div>

<?php echo DSCMenu::getInstance('submenu')->display();  ?>

<div id='onBeforeDisplay_wrapper'>
<?php 
	$dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger( 'onBeforeDisplayMessages', array( @$this->row, @$this->user ) );
?>
</div>
		<table class="">
		<tbody>
			<tr>
				<td valign="top">
					<h1>
						<?php 
						echo $row->subject;
						?>
					</h1>
					<h5>
						<?php 
						echo JText::_( 'MESSAGE_SENT_ON' ); echo JHTML::_( "date", @$row->sendstart, JText::_( 'DATE_FORMAT_LC1' ));
						?>
					</h5>
					<?php echo PhplistHelperMessage::stripPlaceholders($row->message); ?>
				</td>
				<td valign="top">
					
					<div id='onDisplayRightColumn_wrapper'>
						<?php 
							$dispatcher =& JDispatcher::getInstance();
							$dispatcher->trigger( 'onDisplayMessageRightColumn', array( @$this->row, JFactory::getUser() ) );
						?>
					</div>

				</td>
			</tr>
            </tbody>
		</table>

		<div id='onAfterDisplay_wrapper'>
			<?php 
				$dispatcher =& JDispatcher::getInstance();
				$dispatcher->trigger( 'onAfterDisplayMessage', array( @$this->row, JFactory::getUser() ) );
			?>
		</div>
		<input type="hidden" name="id" value="<?php echo @$row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="uid" value="<?php echo $this->uid; ?>"/>
