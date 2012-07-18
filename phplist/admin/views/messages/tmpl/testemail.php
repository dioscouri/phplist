<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>
<?php JHTML::_('script', 'common.js', 'media/com_phplist/js/'); ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'close' || pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		if (form.email.value == ""){
			alert( "<?php echo JText::_( 'Enter an email address to send the test message to', true ); ?>" );
		} else {
			submitform( pressbutton );
		}
	}
</script>
<form action="<?php echo JRoute::_( @$form['action'] ) ?>" method="post" class="adminform" name="adminForm" >

	<fieldset>
		<legend><?php echo JText::_('SEND TEST EMAIL'); ?></legend>
		<table style="width: 100%;">
		<tbody>
			<tr>
				<td style="vertical-align: top; min-width: 70%;">

					<table class="admintable">
						<tr>
							
							<td width="100" align="right" class="key">
								<?php echo JText::_( 'Message' ).':'; ?>
							</td>
							<td>
								<?php echo JText::_( $row->subject ); ?>
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="email">
								<?php echo JText::_( 'EMAIL ADDRESS' ); ?>:
								</label>
							</td>
							<td>
								<input type="hidden" name="mid" id="mid" size="20" value="<?php echo $row->id; ?>" />
								<input type="text" name="email" id="email" size="20" value="" />
							</td>
						</tr>
					</table>	
				</td>
			</tr>
		</tbody>
		</table>
		
		<input type="hidden" name="id" value="<?php echo @$row->id?>" />
		<input type="hidden" name="task" value="" />
	</fieldset>
</form>