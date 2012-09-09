<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('stylesheet', 'phplist.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('stylesheet', 'menu.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_phplist/js/'); ?>
<?php JHTML::_('script', 'joomla.javascript.js', 'includes/js/'); ?>
<?php JHTML::_('behavior.mootools' );  ?>
<?php $state = @$this->state; ?>
<?php $form = @$this->form; ?>
<?php $items = @$this->items; ?>
<?php $row = @$this->row; ?>
			
<div class="componentheading">
     <span><?php echo JText::_( "Forward a Message" ); ?></span>
</div>

<?php  echo DSCMenu::getInstance('submenu')->display(); ?>

<div id='onBeforeDisplay_wrapper'>
	<?php 
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onBeforeDisplayForward', array( @$this->row, @$this->user ) );
	?>
</div>

<form action="<?php echo JRoute::_( @$form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" onsubmit="Dsc.formValidation( '<?php echo JRoute::_( @$form['action'] ) ?>', 'validationmessage', document.adminForm.task.value, document.adminForm )">
	<div id="onBeforeDisplay_wrapper">
	    <?php 
	    $dispatcher = JDispatcher::getInstance();
	    $dispatcher->trigger( 'onBeforeDisplayForward', array( @$this->row, @$this->user ) );
	    ?>
    </div>
    <div id="validationmessage"></div>
	<table width="100%" class="adminform">
		<tbody>          
			<tr>
				<td valign="top">
					<?php echo JText::_( 'Forward the message' ); ?>
					<b><?php echo $this->subject; ?></b>
					<?php echo JText::_( 'to someone' ); ?><br/><br/>
				</td>
			</tr>
			<tr>	
				<td>
					<?php echo JText::_( 'Enter the email address you\'d like to forward the message to:' ); ?><br/>
					<input type="text" name="email" value="" /><br/>
					<input type="hidden" name="mid" id="mid" value="<?php echo $this->messageid; ?>" />
					<input type="hidden" name="userid" id="userid" value="<?php echo $row->id; ?>" />
					<input type="button" onclick="Dsc.submitForm('forward')" value='<?php echo JText::_( 'Forward' ); ?>' />
				</td>
				<td valign="top">
					<div id="onDisplayRightColumn_wrapper">
						<?php 
						$dispatcher =& JDispatcher::getInstance();
						$dispatcher->trigger( 'onDisplayForwardRightColumn', array( $this->row, @$this->user ) );
						?>
					</div>
				</td>
            </tr>
        </tbody>
	</table>
	<div id="onAfterDisplay_wrapper">
		<?php 
			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger( 'onAfterDisplayForward', array( $this->row, @$this->user ) );
		?>
	</div>
	<input type="hidden" name="task" value="forward" /> 
	<input type="hidden" name="filter_order" value="<?php  echo @$state->order; ?>" /> 
	<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" /> 
	<?php echo $this->form['validate']; ?>
</form>