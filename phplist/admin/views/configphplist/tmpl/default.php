<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_phplist/js/'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>

<form action="<?php echo JRoute::_( @$form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

		<div id='onBeforeDisplay_wrapper'>
			<?php 
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger( 'onBeforeDisplayConfigForm', array() );
			?>
		</div>                

		<table style="width: 100%;">
			<tbody>
                <tr>
					<td style="vertical-align: top; min-width: 70%;">
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Option' ); ?>
							</th>
			                <th>
								<?php echo JText::_( 'Value' ); ?>
							</th>
							<th>
								<?php echo JText::_( 'Placeholder' ); ?>
							</th>
							<th>
							</th>
			            </tr>
			           </tbody>
			           </table> 
					<?php
					// display defaults
					$pane = '1';
					echo $this->sliders->startPane( "pane_$pane" );
					
					$legend = JText::_( "WEBSITE_AND_DOMAIN" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Website address (without http://)' ); ?>
							</th>
			                <td>
			                	<input name="website" type="text" class="text_area" size="30" value="<?php echo $this->row->get('website', ''); ?>" />
							</td>
							<td>
								<?php echo JText::_( '[WEBSITE]' ); ?>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Domain Name of your server (for email)' ); ?>
							</th>
			                <td>
			               		<input name="domain" type="text" class="text_area" size="30" value="<?php echo $this->row->get('domain', ''); ?>" />								
							</td>
							<td>
								<?php echo JText::_( '[DOMAIN]' ); ?>
							</td>
							<td>
							</td>
			            </tr>
			            	           </tbody>
			           </table> 
			            <?php
						echo $this->sliders->endPanel();
					
						$legend = JText::_( "ADMIN_EMAILS" );
						echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
						?>
					
					<table class="adminlist">
					<tbody>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Person in charge of this system (one email address)' ); ?>
							</th>
			                <td>
			                	<input name="admin_address" type="text" class="text_area" size="30" value="<?php echo $this->row->get('admin_address', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'List of people to CC in system emails (separate by commas)' ); ?>
							</th>
			                <td>
			               	 	<input name="admin_address" type="text" class="text_area" size="30" value="<?php echo $this->row->get('report_address', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Who gets the reports (email address, separate multiple emails with a comma)' ); ?>
							</th>
			                <td>
			                	<input name="report_address" type="text" class="text_area" size="30" value="<?php echo $this->row->get('admin_address', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'From email address for system messages' ); ?>
							</th>
			                <td>
			                	<input name="message_from_address" type="text" class="text_area" size="30" value="<?php echo $this->row->get('message_from_address', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'What name do system messages appear to come from' ); ?>
							</th>
			                <td>
			                	<input name="message_from_name" type="text" class="text_area" size="30" value="<?php echo $this->row->get('message_from_name', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Reply-to email address for system messages' ); ?>
							</th>
			                <td>
			                	<input name="message_replyto_address" type="text" class="text_area" size="30" value="<?php echo $this->row->get('message_replyto_address', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            	           </tbody>
			           </table> 
			            <?php
						echo $this->sliders->endPanel();
					
						$legend = JText::_( "PHPLIST_UI" );
						echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
						?>
					
					<table class="adminlist">
					<tbody>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'if there is only one visible list, should it be hidden in the page and automatically subscribe users who sign up (0/1)' ); ?>
							</th>
			                <td>
			                	<input name="hide_single_list" type="text" class="text_area" size="30" value="<?php echo $this->row->get('hide_single_list', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'width of a textline field (numerical)' ); ?>
							</th>
			                <td>
			                	<input name="textline_width" disabled="disabled" type="text" class="text_area" size="30" value="<?php echo $this->row->get('textline_width', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
								<?php echo JText::_( 'PHPList UI only' ); ?>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'dimensions of a textarea field (rows,columns)' ); ?>
							</th>
			                <td>
			                	<input name="textarea_dimensions" disabled="disabled" type="text" class="text_area" size="30" value="<?php echo $this->row->get('textarea_dimensions', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
								<?php echo JText::_( 'PHPList UI only' ); ?>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Does the admin get copies of subscribe, update and unsubscribe messages (0/1)' ); ?>
							</th>
			                <td>
			                	<input name="send_admin_copies" disabled="disabled" type="text" class="text_area" size="30" value="<?php echo $this->row->get('send_admin_copies', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
								<?php echo JText::_( 'PHPList UI only' ); ?>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'The default subscribe page when there are multiple' ); ?>
							</th>
			                <td>
			                	<input name="defaultsubscribepage" disabled="disabled" type="text" class="text_area" size="30" value="<?php echo $this->row->get('defaultsubscribepage', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
								<?php echo JText::_( 'PHPList UI only' ); ?>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'The default HTML template to use when sending a message' ); ?>
							</th>
			                <td>
			                	<input name="defaultmessagetemplate" type="text" class="text_area" size="30" value="<?php echo $this->row->get('defaultmessagetemplate', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            	           </tbody>
			           </table> 
			            <?php
					echo $this->sliders->endPanel();
					
					$legend = JText::_( "SUBSCRIPTION_URLS" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
					?>
					
					<table class="adminlist">
					<tbody>
			             <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'URL where users can subscribe' ); ?>
							</th>
			                <td>
			                	<input name="subscribeurl" type="text" class="text_area" size="30" value="<?php echo $this->row->get('subscribeurl', ''); ?>" />
							</td>
							<td>
								<?php echo JText::_( '[SUBSCRIBEURL]' ); ?>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'URL where known users can unsubscribe' ); ?>
							</th>
			                <td>
			                	<input name="unsubscribeurl" type="text" class="text_area" size="30" value="<?php echo $this->row->get('unsubscribeurl', ''); ?>" />
							</td>
							<td>
								<?php echo JText::_( '[UNSUBSCRIBEURL]' ); ?>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'URL where unknown users can unsubscribe (blacklist)' ); ?>
							</th>
			                <td>
			                	<input name="blacklisturl" type="text" disabled="disabled" class="text_area" size="30" value="<?php echo $this->row->get('blacklisturl', ''); ?>" />
							</td>
							<td>
								<?php echo JText::_( '[BLACKLISTURL]' ); ?>
							</td>
							<td>
								<?php echo JText::_( 'currently PHPList UI only' ); ?>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'URL where users have to confirm their subscription' ); ?>
							</th>
			                <td>
			                	<input name="confirmationurl" type="text" class="text_area" size="30" value="<?php echo $this->row->get('confirmationurl', ''); ?>" />
							</td>
							<td>
								<?php echo JText::_( '[CONFIRMATIONURL]' ); ?>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'URL where users can update their details' ); ?>
							</th>
			                <td>
			                	<input name="preferencesurl" type="text" class="text_area" size="30" value="<?php echo $this->row->get('preferencesurl', ''); ?>" />
							</td>
							<td>
								<?php echo JText::_( '[PREFERENCESURL]' ); ?>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'URL where messages can be forwarded' ); ?>
							</th>
			                <td>
			                	<input name="forwardurl" type="text" class="text_area" size="30" value="<?php echo $this->row->get('forwardurl', ''); ?>" />
							</td>
							<td>
								<?php echo JText::_( '[FORWARDURL]' ); ?>
							</td>
							<td>
							</td>
			            </tr>
			            	           </tbody>
			           </table> 
			            <?php
					echo $this->sliders->endPanel();
					
					$legend = JText::_( "SUBSCRIPTION_EMAILS_TEXT" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
					?>
					
					<table class="adminlist">
					<tbody>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Subject of the message users receive when they subscribe' ); ?>
							</th>
			                <td>
			                	<input name="subscribesubject" type="text" class="text_area" size="30" value="<?php echo $this->row->get('subscribesubject', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Message users receive when they subscribe' ); ?>
							</th>
			                <td>
			                	<textarea name="subscribemessage" class="text_area" cols="60" rows="20"><?php echo $this->row->get('subscribemessage', ''); ?></textarea>
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Subject of the message users receive when they unsubscribe' ); ?>
							</th>
			                <td>
			                	<input name="unsubscribesubject" type="text" class="text_area" size="30" value="<?php echo $this->row->get('unsubscribesubject', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Message users receive when they unsubscribe' ); ?>
							</th>
			                <td>
			                	<textarea name="unsubscribemessage"  class="text_area" cols="60" rows="20" ><?php echo $this->row->get('unsubscribemessage', ''); ?></textarea>
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Subject of the message users receive after confirming their email address' ); ?>
							</th>
			                <td>
			                	<input name="updatesubject" type="text" class="text_area" size="30" value="<?php echo $this->row->get('updatesubject', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Message users receive after confirming their email address' ); ?>
							</th>
			                <td>
			                	<textarea name="updatemessage" class="text_area" cols="60" rows="40" ><?php echo $this->row->get('updatemessage', ''); ?></textarea>
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Subject of the message users receive when they have changed their details' ); ?>
							</th>
			                <td>
			                	<input name="updatemessage" type="text" class="text_area" size="30" value="<?php echo $this->row->get('updatemessage', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Message that is sent when users change their information' ); ?>
							</th>
			                <td>
			                	<textarea name="emailchanged_text" class="text_area" cols="60" rows="40" ><?php echo $this->row->get('emailchanged_text', ''); ?></textarea>
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Part of the message that is sent to their new email address when users change their information, and the email address has changed' ); ?>
							</th>
			                <td>
			                	<textarea name="emailchanged_text_oldaddress" class="text_area" cols="60" rows="10"><?php echo $this->row->get('emailchanged_text_oldaddress', ''); ?></textarea>
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			             <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Subject of message to send when users request their personal location' ); ?>
							</th>
			                <td>
			                	<input name="personallocation_subject" class="text_area" type="text" size="50" value="<?php echo $this->row->get('personallocation_subject', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			             <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Message to send when they request their personal location' ); ?>
							</th>
			                <td>
			                	<textarea name="personallocation_message" class="text_area" cols="60" rows="10"><?php echo $this->row->get('personallocation_message', ''); ?></textarea>
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			             <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Default footer for sending a message' ); ?>
							</th>
			                <td>
			                	<textarea name="messagefooter" class="text_area" cols="60" rows="10"><?php echo $this->row->get('messagefooter', ''); ?></textarea>
							</td>
							<td>
								<?php echo JText::_( '[FOOTER]' ); ?>
							</td>
							<td>
							</td>
			            </tr>
			             <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Footer used when a message has been forwarded' ); ?>
							</th>
			                <td>
			                	<textarea name="forwardfooter" class="text_area" cols="60" rows="10"><?php echo $this->row->get('forwardfooter', ''); ?></textarea>
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            	           </tbody>
			           </table> 
			            <?php
					echo $this->sliders->endPanel();
					
					$legend = JText::_( "CHARACTER_SETS" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
					?>
					
					<table class="adminlist">
					<tbody>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Charset for HTML messages' ); ?>
							</th>
			                <td>
			                	<input name="html_charset" type="text" class="text_area" size="30" value="<?php echo $this->row->get('html_charset', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Charset for Text messages' ); ?>
							</th>
			                <td>
			                	<input name="text_charset" type="text" class="text_area" size="30" value="<?php echo $this->row->get('text_charset', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            	           </tbody>
			           </table> 
			             <?php
					echo $this->sliders->endPanel();
					
					$legend = JText::_( "Other" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
					?>
					
					<table class="adminlist">
					<tbody>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'CSS for HTML messages without a template' ); ?>
							</th>
			                <td>
			                	<textarea name="html_email_style"  class="text_area" cols="60" rows="10"><?php echo $this->row->get('html_email_style', ''); ?></textarea>
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			            <tr>
			            	<th style="width: 40%;">
								<?php echo JText::_( 'Domains that only accept text emails, one per line' ); ?>
							</th>
			                <td>
			                	<input name="alwayssendtextto" type="text" class="text_area" size="30" value="<?php echo $this->row->get('alwayssendtextto', ''); ?>" />
							</td>
							<td>
							</td>
							<td>
							</td>
			            </tr>
			                   
					</tbody>
					</table>
					<?php	
					echo $this->sliders->endPanel();
					
					// if there are plugins, display them accordingly
	                if ($this->items_sliders) 
	                {               	
                		$tab=1;
						$pane=2;
						for ($i=0, $count=count($this->items_sliders); $i < $count; $i++) {
							if ($pane == 1) {
								 echo $this->sliders->startPane( "pane_$pane" );
							}
							$item = $this->items_sliders[$i];
							echo $this->sliders->startPanel( JText::_( $item->element ), $item->element );
							
							// load the plugin
								$import = JPluginHelper::importPlugin( strtolower( 'Phplist' ), $item->element );
							// fire plugin
								$dispatcher = JDispatcher::getInstance();
								$dispatcher->trigger( 'onDisplayConfigFormSliders', array( $item, $this->row ) );
								
							echo $this->sliders->endPanel();
							if ($i == $count-1) {
								 echo $this->sliders->endPane();
							}
						}
					}
					
					echo $this->sliders->endPane();
					
					?>
					</td>
					<td style="vertical-align: top; max-width: 30%;">
						
						<?php echo PhplistGrid::pagetooltip( JRequest::getVar('view') ); ?>
						
						<div id='onDisplayRightColumn_wrapper'>
							<?php
								$dispatcher = JDispatcher::getInstance();
								$dispatcher->trigger( 'onDisplayConfigFormRightColumn', array() );
							?>
						</div>

					</td>
                </tr>
            </tbody>
		</table>

		<div id='onAfterDisplay_wrapper'>
			<?php 
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger( 'onAfterDisplayConfigForm', array() );
			?>
		</div>
        
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$filter['order']; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$filter['direction']; ?>" />
	
	<?php echo $this->form['validate']; ?>
</form>