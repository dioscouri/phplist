<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>
<?php $editor = JFactory::getEditor(); ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'close' || pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		var message = <?php echo $editor->getContent( 'message' ); ?>
		// do field validation
		if (form.subject.value == ""){
			alert( "<?php echo JText::_( 'Message must have a Subject', true ); ?>" );
		} else if (form.fromfield.value == ""){
			alert( "<?php echo JText::_( 'Message must have a From email address', true ); ?>" );
		} else if (message == ""){
			alert( "<?php echo JText::_( 'Message must have some text.', true ); ?>" );
		} else {
			submitform( pressbutton );
		}
	}
	function phplistInsertArticle( url, articleid ) 
    {
            // execute Ajax request to server
            var a=new Ajax(url,{
                method:"get",
                data:{"articleid":articleid},
                onComplete: function(response){
                	var resp=Json.evaluate(response, false);
                	jInsertEditorText(resp, 'message');
                }
            }).request();
            
            document.getElementById('sbox-window').close();
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
								<label for="subject">
								<?php echo JText::_( 'SUBJECT' ); ?>:
								</label>
							</td>
							<td>
								<input type="text" name="subject" id="subject" size="40" value="<?php echo @$row->subject; ?>" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="fromfield">
								<?php echo JText::_( 'FROM' ); ?>:
								</label>
							</td>
							<td>
								<input type="text" name="fromfield" id="fromfield" size="30" value="<?php echo @$row->fromfield; ?>" />
							</td>
						</tr>
							<tr>
							<td width="100" align="right" class="key">
								<label for="autofill">
									<?php  echo JText::_( 'Insert Content' ); ?>:
								</label>
							</td>
							<td>
		                      <?php echo $this->elementArticleModel->_fetchElement('test'); ?>
                        		<?php echo $this->elementArticleModel->_clearElement('test'); ?>
							</td>
						</tr>
						<tr>
							<td width="100" align="right" valign="top" class="key">
								<label for="message">
								<?php echo JText::_( 'MESSAGE' ); ?>:
								</label>
							</td>
							<td>
								<?php 
								echo $editor->display( 'message',  @$row->message, '100%', '550', '75', '20' ) ;
		                        ?>
		                    </td>
						</tr>
						<tr>
							<td width="100" align="right" class="key" valign="top">
								<label for="textmessage">
								<?php echo JText::_( 'TEXT MESSAGE' ); ?>:
								</label>
							</td>
							<td>
								 <textarea name="textmessage" class="text_area" cols="60" rows="30" ><?php echo stripslashes( @$row->textmessage ); ?></textarea>
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

					echo $sliders->startPanel( JText::_( "NEWSLETTERS" ), 'newsletters' );
					?>
					<div class="note" style="margin: 5px;"><?php echo JText::_( 'SELECT_NEWSLETTERS' ); ?></div>
					<table class="admintable">
					<?php 					$newsletters = @$this->newsletters;					
					foreach (@$newsletters as $d) 
					{
						if ($d->id > 0) 
						{
							$checked = '';
							if (@$row->id > 0) 
							{
								if ($isNewsletter = PhplistHelperMessage::isNewsletter(@$row->id, $d->id)) 
								{
									$checked = "checked='checked'";	
								}
							}
							?>
							<tr>
								<td style="width: 10px; text-align: right;">
									<input type='checkbox' name='addtonewsletter[<?php echo $d->id; ?>]' value='<?php echo $d->id; ?>' <?php echo $checked; ?> />
								</td>
								<td style="text-align: left;">
									<?php echo JText::_($d->name); ?>
								</td>
							</tr>
							<?php 						}
					}
	 				?>
	 				</table>
	 				<?php 	                echo $sliders->endPanel();
					
					echo $sliders->startPanel( JText::_( "OPTIONS" ), 'options' );
					?>				
					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key">
								<label for="sendas">
								<?php echo JText::_( 'SEND AS' ); ?>:
								</label>
							</td>
							<td>
								<?php echo PhplistSelect::sendas( @$row->sendformat, 'sendformat' );  ?>
							</td>
						</tr>
						<tr>
						<td width="100" align="right" class="key">
								<label for="template">
								<?php echo JText::_( 'SELECT_TEMPLATE' ); ?>:
								</label>
							</td>	
							<td>
							<?php
							if (!$this->templates)
							{
								echo JText::_( 'SET_UP_YOUR_TEMPLATES_IN_PHPLIST' );
								echo '<input type="hidden" name="template" value="0" />';
							} else
							echo PhplistSelect::templates( @$row->template, 'template' );  ?>
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="sendstart">
								<?php echo JText::_( 'START_SENDING' ); ?>:
								</label>
							</td>
							<td>
								<?php echo JHTML::calendar( @$row->embargo, "embargo", "embargo", '%Y-%m-%d' ); ?>
							</td>
						</tr>
						<tr>
							<td width="100" align="right" valign="top" class="key">
								<label for="notify_start">
								<?php echo JText::_( 'EMAIL_TO_NOTIFY_UPON_START' ); ?>:
								</label>
							</td>
							<td>
		                        <input type="text" name="notify_start" size="50" value="<?php echo @$row->notify_start; ?>" />
		                    </td>
						</tr>
						<tr>
							<td width="100" align="right" valign="top" class="key">
								<label for="notify_end">
								<?php echo JText::_( 'EMAIL_TO_NOTIFY_UPON_COMPLETION' ); ?>:
								</label>
							</td>
							<td>
		                        <input type="text" name="notify_end" size="50" value="<?php echo @$row->notify_end; ?>" />
		                    </td>
						</tr>
					</table>
					<?php 
					echo $sliders->endPanel();
					
					echo $sliders->startPanel( JText::_( "MESSAGE_FOOTER" ), 'options' );
					?>	
					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key">
								<label for="sendas">
								<?php echo JText::_( 'FOOTER' ); ?>:
								</label>
							</td>
							<td>
								 <textarea name="footer" class="text_area" cols="40" rows="5" ><?php if (@$row->footer == '' || @$row->footer == 'NULL') echo stripslashes( @$this->footer );
								 	else echo stripslashes( @$row->footer ); ?></textarea>
							</td>
						</tr>
					</table>	
					<?php echo $sliders->endPanel();

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