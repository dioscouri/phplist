<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', Phplist::getName().'.js', 'media/com_phplist/js/'); ?>
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
								<?php echo JText::_( 'SEND ACTIVATION EMAIL' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'activation_email', 'class="inputbox"', $this->row->get('activation_email', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'SEND ACTIVATION EMAIL DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'HTML FORMAT EMAILS' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'default_html', 'class="inputbox"', $this->row->get('default_html', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'HTML FORMAT EMAILS DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'AUTO CREATE JOOMLA USERS' ); ?>
							</th>
							<td>
								<?php echo JHTML::_('select.booleanlist', 'users_autocreate', 'class="inputbox"', $this->row->get('users_autocreate', '0') ); ?>
							</td>
							<td width="35%">
								<?php echo JText::_( 'AUTO CREATE JOOMLA USERS DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DISPLAY ATTRIBUTES IN FRONT END' ); ?>
							</th>
							<td>
		                       <input name="frontend_attribs" type="text" class="text_area" size='10' value="<?php echo $this->row->get('frontend_attribs', ''); ?>" />
							</td>
							<td width="35%">
								<?php echo JText::_( 'DISPLAY ATTRIBUTES IN FRONT END DESC' ); ?>
							</td>
						</tr>
					</tbody>
					</table>
					<?php	
					echo $this->sliders->endPanel();
					
					$legend = JText::_( "FRONT END DISPLAY" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'general' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DISPLAY TOP MENU' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'display_submenu', 'class="inputbox"', $this->row->get('display_submenu', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'DISPLAY TOP MENU DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'NEWSLETTER ORDERING' ); ?>
							</th>
			                <td>
			                	<?php echo PhplistSelect::newsletters_orderby( $this->row->get('display_newsletter_order', '1'), 'display_newsletter_order', '', 'id', true );?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'NEWSLETTER ORDERING DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'NEWSLETTER ORDERING DIRECTION' ); ?>
							</th>
			                <td>
			                	<?php echo PhplistSelect::newsletters_orderdir( $this->row->get('display_newsletter_order_dir', '1'), 'display_newsletter_order_dir', '', 'id', true );?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'NEWSLETTER ORDERING DIRECTION DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'MESSAGE TEMPLATE VIEW' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'display_messagetemplate', 'class="inputbox"', $this->row->get('display_messagetemplate', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'MESSAGE TEMPLATE VIEW DESC' ); ?>
			                </td>
						</tr>	
					</tbody>
					</table>
					<?php	
					echo $this->sliders->endPanel();
					
					
					$legend = JText::_( "PHPLIST DATABASE CONNECTION" );
					echo $this->sliders->startPanel( JText::_( $legend ), 'dashboard' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE HOST' ); ?>
							</th>
							<td>
		                       <input name="phplist_host" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_host', 'localhost'); ?>" />
							</td>
							<td width="35%">
			                	<?php echo JText::_( 'DATABASE HOST DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE NAME' ); ?>
							</th>
							<td>
		                        <input name="phplist_database" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_database', 'phplist'); ?>" />				
							</td>
							<td>
								<?php echo JText::_( 'DATABASE NAME DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE USER' ); ?>
							</th>
							<td>
                            	<input name="phplist_user" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_user', 'phplist'); ?>" />			
							</td>
							<td>
								<?php echo JText::_( 'DATABASE USER DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE PASSWORD' ); ?>
							</th>
							<td>
								<input name="phplist_password" type="password" class="text_area" size='50' value="<?php echo $this->row->get('phplist_password', ''); ?>" />
							</td>
							<td>
								<?php echo JText::_( 'DATABASE PASSWORD DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE PREFIX' ); ?>
							</th>
							<td>
                            	<input name="phplist_prefix" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_prefix', 'phplist'); ?>" />
							</td>
							<td>
								<?php echo JText::_( 'DATABASE PREFIX DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE USER TABLE PREFIX' ); ?>
							</th>
							<td>
                            	<input name="phplist_user_prefix" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_user_prefix', 'phplist'); ?>" />
							</td>
							<td>
								<?php echo JText::_( 'DATABASE USER TABLE PREFIX DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE DRIVER' ); ?>
							</th>
							<td>
                            	<input name="phplist_driver" type="text" class="text_area" size='50' value="<?php echo $this->row->get('phplist_driver', 'mysql'); ?>" />
							</td>
							<td>
								<?php echo JText::_( 'DATABASE DRIVER DESC' ); ?>
							</td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'DATABASE PORT' ); ?>
							</th>
							<td>
                            	<input name="phplist_port" type="text" class="text_area" size='10' value="<?php echo $this->row->get('phplist_port', '3600'); ?>" />
							</td>
							<td>
								<?php echo JText::_( 'DATABASE PORT DESC' ); ?>
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
								<?php echo JText::_( 'SHOW PHPLIST LINK IN FOOTER' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'show_linkback_phplist', 'class="inputbox"', $this->row->get('show_linkback_phplist', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'SHOW PHPLIST LINK IN FOOTER DESC' ); ?>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'SHOW DIOSCOURI LINK IN FOOTER' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'show_linkback', 'class="inputbox"', $this->row->get('show_linkback', '1') ); ?>
			                </td>
			                <td width="35%">
			                	<?php echo JText::_( 'SHOW DIOSCOURI LINK IN FOOTER DESC' ); ?>
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