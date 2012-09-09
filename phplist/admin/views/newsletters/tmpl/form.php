<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'close' || pressbutton == 'cancel') {
			Dsc.submitForm( pressbutton );
			return;
		}
		// do field validation
		if (form.name.value == ""){
			alert( "<?php echo JText::_( 'NEWSLETTER_NAME_VALIDATION', true ); ?>" );
		} else {
			Dsc.submitForm( pressbutton );
		}
	}
</script>
<form action="<?php echo JRoute::_( @$form['action'] ) ?>" method="post" class="adminform" name="adminForm" >

	<fieldset>
		<legend><?php echo JText::_('FORM'); ?></legend>
		<table style="width: 100%;">
		<tbody>
			<tr>
				<td style="vertical-align: top; min-width: 70%;">

					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key">
								<label for="name">
								<?php echo JText::_( 'NAME' ); ?>: *
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" size="50" value="<?php echo @$row->name; ?>" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key" valign="top">
								<label for="description">
								<?php echo JText::_( 'Description' ); ?>:
								</label>
							</td>
							<td>
								<?php $editor = JFactory::getEditor(); 
								echo $editor->display( 'description',  @$row->description, '100%', '250', '75', '20' ) ;
		                        ?>
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="sendas">
								<?php echo JText::_( 'PUBLISHED' ); ?>:
								</label>
							</td>
							<td>
								<?php echo JHTML::_('select.booleanlist', 'active', '', @$row->active ) ?>
							</td>
						</tr>
					</table>
				</td>
				<td style="vertical-align: top; min-width: 30%;">
					<?php
					jimport('joomla.html.pane');
					$sliders = JPane::getInstance( 'sliders' );
					
					// display defaults
					$pane = '1';
					echo $sliders->startPane( "pane_$pane" );
					
	                // if there are 3pd plugins, display them accordingly
					for ($i=0, $count=count(@$this->items_sliders); $i < $count; $i++) 
					{
						$item = $this->items_sliders[$i];
						echo $this->sliders->startPanel( JText::_( $item->element ), $item->element );
						
						// load the plugin
							$import = JPluginHelper::importPlugin( strtolower( 'Phplist' ), $item->element );
						// fire plugin
							$dispatcher = JDispatcher::getInstance();
							$dispatcher->trigger( 'onDisplayFormSlidersMessages', array( $item, $this->row ) );
							
						echo $this->sliders->endPanel();
					}
	                
	                // close the slider pane 
	                echo $sliders->endPane();
					?>
				</td>
			</tr>
		</tbody>
		</table>
		
		<input type="hidden" name="id" value="<?php echo @$row->id?>" />
		<input type="hidden" name="task" value="" />
	</fieldset>
</form>