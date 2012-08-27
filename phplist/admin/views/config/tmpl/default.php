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

					<?php
					// display defaults
					$pane = '1';
					echo $this->sliders->startPane( "pane_$pane" );
					
					$legend = JText::_( "OPTIONS" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'SEND_ACTIVATION_EMAIL' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'activation_email', 'class="inputbox"', $this->row->get('activation_email', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'SEND_ACTIVATION_EMAIL_DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'HTML_FORMAT_EMAILS' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'default_html', 'class="inputbox"', $this->row->get('default_html', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'HTML_FORMAT_EMAILS_DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'AUTO_CREATE_JOOMLA_USERS' ); ?>
							</th>
							<td>
								<?php echo JHTML::_('select.booleanlist', 'users_autocreate', 'class="inputbox"', $this->row->get('users_autocreate', '0') ); ?>
							</td>
							<td width="35%">
								<?php echo JText::_( 'AUTO_CREATE_JOOMLA_USERS_DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DISPLAY_ATTRIBUTES_IN_FRONT_END' ); ?>
							</th>
							<td>
			                	<?php //echo PhplistSelect::attributes( explode(',',$this->row->get('frontend_attribs', '1')), 'frontend_attribs[]', ' multiple="multiple" size="5" ', 'id', true, JText::_('No Attributes') );?>
							</td>
							<td width="35%">
								<?php echo JText::_( 'DISPLAY_ATTRIBUTES_IN_FRONT_END_DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DEFAULT_TEMPLATE_FOR_MESSAGES' ); ?>
							</th>
							<td>
			                	<?php //echo PhplistSelect::templates( $this->row->get('default_template', '1'), 'default_template', '', 'id', true );?>
							</td>
							<td width="35%">
								<?php echo JText::_( 'DEFAULT_TEMPLATE_FOR_MESSAGES_DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DEFAULT_FROM_EMAIL_ADDRESS' ); ?>
							</th>
							<td>
			               		<input name="default_fromemail" type="text" class="text_area" size='20' value="<?php echo $this->row->get('default_fromemail', ''); ?>" />
							</td>
							<td width="35%">
								<?php echo JText::_( 'DEFAULT_FROM_EMAIL_ADDRESS_DESC' ); ?>
							</td>
						</tr>
					</tbody>
					</table>
					<?php	
					echo $this->sliders->endPanel();
					
					$legend = JText::_( "FRONT_END_DISPLAY" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DISPLAY_TOP_MENU' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'display_submenu', 'class="inputbox"', $this->row->get('display_submenu', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'DISPLAY_TOP_MENU_DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DISPLAY_SEARCH_INPUT' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'display_search', 'class="inputbox"', $this->row->get('display_search', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'DISPLAY_SEARCH_INPUT_DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'NEWSLETTER_ORDERING' ); ?>
							</th>
			                <td>
			                	<?php echo PhplistSelect::newsletters_orderby( $this->row->get('display_newsletter_order', '1'), 'display_newsletter_order', '', 'id', true );?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'NEWSLETTER_ORDERING_DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'NEWSLETTER_ORDERING_DIRECTION' ); ?>
							</th>
			                <td>
			                	<?php echo PhplistSelect::newsletters_orderdir( $this->row->get('display_newsletter_order_dir', '1'), 'display_newsletter_order_dir', '', 'id', true );?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'NEWSLETTER_ORDERING_DIRECTION_DESC' ); ?>
			                </td>
						</tr>		
					</tbody>
					</table>
					<?php	
					echo $this->sliders->endPanel();
					
					
					$legend = JText::_( "PHPLIST_DATABASE_CONNECTION" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'dashboard' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE_HOST' ); ?>
							</th>
							<td>
		                       <input name="phplist_host" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_host', 'localhost'); ?>" />
							</td>
							<td width="35%">
			                	<?php echo JText::_( 'DATABASE_HOST_DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE_NAME' ); ?>
							</th>
							<td>
		                        <input name="phplist_database" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_database', 'phplist'); ?>" />				
							</td>
							<td>
								<?php echo JText::_( 'DATABASE_NAME_DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE_USER' ); ?>
							</th>
							<td>
                            	<input name="phplist_user" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_user', 'phplist'); ?>" />			
							</td>
							<td>
								<?php echo JText::_( 'DATABASE_USER_DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE_PASSWORD' ); ?>
							</th>
							<td>
								<input name="phplist_password" type="password" class="text_area" size='50' value="<?php echo $this->row->get('phplist_password', ''); ?>" />
							</td>
							<td>
								<?php echo JText::_( 'DATABASE_PASSWORD_DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE_PREFIX' ); ?>
							</th>
							<td>
                            	<input name="phplist_prefix" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_prefix', 'phplist'); ?>" />
							</td>
							<td>
								<?php echo JText::_( 'DATABASE_PREFIX_DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE_USER_TABLE_PREFIX' ); ?>
							</th>
							<td>
                            	<input name="phplist_user_prefix" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_user_prefix', 'phplist'); ?>" />
							</td>
							<td>
								<?php echo JText::_( 'DATABASE_USER_TABLE_PREFIX_DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE_DRIVER' ); ?>
							</th>
							<td>
                            	<input name="phplist_driver" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_driver', 'mysql'); ?>" />
							</td>
							<td>
								<?php echo JText::_( 'DATABASE_DRIVER_DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE_PORT' ); ?>
							</th>
							<td>
                            	<input name="phplist_port" type="text" class="text_area" size='10' value="<?php echo $this->row->get('phplist_port', '3600'); ?>" />
							</td>
							<td>
								<?php echo JText::_( 'DATABASE_PORT_DESC' ); ?>
							</td>
						</tr>
					</tbody>
					</table>
					<?php	
					echo $this->sliders->endPanel();
									
					$legend = JText::_( "CREDITS" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'SHOW_PHPLIST_LINK_IN_FOOTER' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'show_linkback_phplist', 'class="inputbox"', $this->row->get('show_linkback_phplist', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'SHOW_PHPLIST_LINK_IN_FOOTER_DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'SHOW_DIOSCOURI_LINK_IN_FOOTER' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'show_linkback', 'class="inputbox"', $this->row->get('show_linkback', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'SHOW_DIOSCOURI_LINK_IN_FOOTER_DESC' ); ?>
			                </td>
						</tr>
					</tbody>
					</table>	
					<?php	
					echo $this->sliders->endPanel();
					
					$legend = JText::_( "Administrator ToolTips" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'defaults' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Dashboard Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_dashboard_disabled', 'class="inputbox"', $this->row->get('page_tooltip_dashboard_disabled', '0') ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Configuration Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_config_disabled', 'class="inputbox"', $this->row->get('page_tooltip_config_disabled', '0') ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Users Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_users_disabled', 'class="inputbox"', $this->row->get('page_tooltip_users_disabled', '0') ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Subscriptions Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_subscriptions_disabled', 'class="inputbox"', $this->row->get('page_tooltip_subscriptions_disabled', '0') ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Newsletters Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_newsletters_disabled', 'class="inputbox"', $this->row->get('page_tooltip_newsletters_disabled', '0') ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Messages Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_messages_disabled', 'class="inputbox"', $this->row->get('page_tooltip_messages_disabled', '0') ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Attributes Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_attributes_disabled', 'class="inputbox"', $this->row->get('page_tooltip_attributes_disabled', '0') ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Logs Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_logs_disabled', 'class="inputbox"', $this->row->get('page_tooltip_logs_disabled', '0') ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Tools Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_tools_disabled', 'class="inputbox"', $this->row->get('page_tooltip_tools_disabled', '0') ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide PHPList Config Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_phplistconfig_disabled', 'class="inputbox"', $this->row->get('page_tooltip_tools_disabled', '0') ); ?>
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