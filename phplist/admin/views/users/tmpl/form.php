<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>
<?php $isJoomlaUser = true; ?>
<?php if (@$row->foreignkey == 'NULL' || @$row->foreignkey == '' ) $isJoomlaUser = false; ?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'close' || pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		// do field validation
		if (form.email.value == ""){
			alert( "<?php echo JText::_( 'You must enter an email address', true ); ?>" );
		} else {
			submitform( pressbutton );
		}
	}
</script>
<form action="<?php echo JRoute::_( @$form['action'] ) ?>" method="post" class="adminform" name="adminForm">

<fieldset><legend><?php if ($this->new == false)
						{
							echo JText::_('EDIT USER') . " : " . @$row->email; 
						}
						else
						{
							echo JText::_('NEW USER');
						}?></legend>
<table style="width: 100%;">
	<tbody>
		<tr>
			<td style="vertical-align: top; min-width: 70%;">
			<table class="adminlist">
				<tbody>
					<tr>
						<th style="width: 25%;"><label for="id"> <?php echo JText::_( 'EMAIL' ); ?>:
						</label></th>
						<td><?php if (!$isJoomlaUser) {?>
						<input type="text" name="email" id="email" size="50" value="<?php echo @$row->email; ?>" />
						<?php }
						else
						{ ?>
						<div class="note" style="margin: 5px;"><?php echo JText::_( 'JOOMLA EMAIL' ); ?></div>
						<?php
							echo @$row->email; ?>
							<input type="hidden" name="email" id="email" value="<?php echo @$row->email; ?>" />
						<?php } ?>
						</td>
					</tr>
					
					
					<tr>
						<th style="width: 25%;"><label for="id"> <?php echo JText::_( 'HTML EMAILS' ); ?>:
						</label></th>
						<td><?php 
						if ($this->new == false)
						{
							echo JHTML::_('select.booleanlist', 'htmlemail', 'class="inputbox"', @$row->htmlemail, JText::_("YES"), JText::_("NO") );
						}
						else
						{
							echo JHTML::_('select.booleanlist', 'htmlemail', 'class="inputbox"', @$this->default_html, JText::_("YES"), JText::_("NO") );
							
						} ?>
						</td>
					</tr>
					<tr>
						<th style="width: 25%;"><label for="id"> <?php echo JText::_( 'CONFIRM USER' ); ?>:
						</label></th>
						<td>
						<?php 
						if ($this->new == false)
						{
							echo JHTML::_('select.booleanlist', 'confirmed', 'class="inputbox"', @$row->confirmed, JText::_("YES"), JText::_("NO") );
						}
						else
						{
							echo JHTML::_('select.booleanlist', 'confirmed', 'class="inputbox"', @$this->activation_email, JText::_("YES"), JText::_("NO") );
						} ?>
						</td>
					</tr>
						<?php if ($this->new == false)
						{ ?>
					<tr>
						<th style="width: 25%;"><label for="id"> <?php echo JText::_( 'PHPLIST ID' ); ?>:
						</label></th>
						<td><?php echo @$row->id; ?>
						<input type="hidden" name="id" id="id" value="<?php echo @$row->id; ?>" />
						</td>
					</tr>
					<tr>
						<th style="width: 25%;"><label for="id"> <?php echo JText::_( 'Unique ID' ); ?>:
						</label></th>
						<td><?php echo @$row->uniqid; ?>
						<input type="hidden" name="id" id="id" value="<?php echo @$row->uniqid; ?>" />
						</td>
					</tr>
					<?php } ?>
			<?php
			
		if ($this->new == false && $isJoomlaUser)
		{
			?>
					<tr>
						<th style="width: 25%;"><label for="foreignkey"> <?php echo JText::_( 'Joomla! ID' ); ?>:
						</label></th>
						<td><?php echo @$row->foreignkey; ?></td>
					</tr>
					<tr>
						<th style="width: 25%;"><label for="joomla_details"> <?php echo JText::_( 'NAME' ); ?>:
						</label></th>
						<td><?php $info_user = JFactory::getUser((int) @$row->foreignkey);
						if ($info_user->id != 0) echo $info_user->name;
						else echo "---";
						?></td>
					</tr>
					<tr>
						<th style="width: 25%;"><label for="joomla_details"> <?php echo JText::_( 'USERNAME' ); ?>:
						</label></th>
						<td><?php if ($info_user->id != 0) echo $info_user->username;
						else echo "---";
						?></td>
					</tr>
				
			<?php
		}
			?>
			</tbody>
			</table>
			</td>
			<td style="vertical-align: top; min-width: 30%;">
			
			<?php
			// display defaults
			$pane = '1';
			echo $this->sliders->startPane( "pane_$pane" );
			

			$legend = JText::_( "SUBSCRIPTIONS" );
			echo $this->sliders->startPanel( JText::_( $legend ), 'subscriptions' );
			?>
			<table class="adminlist">
				<tbody>
					<tr>
						<td colspan="3">
						<div class="note" style="margin: 5px;">
							<?php  if ($this->new == false)
							{
								echo JText::_( 'NEWSLETTERS THIS USER IS SUBSCRIBED TO' ) . ':'; 
							}
							else
							{
								echo JText::_( 'Select Newsletters to subscribe user to' ) . ':';
							}
							?></div>
						</td>
					</tr>
					<?php
					$newsletters = @$this->newsletters;
					foreach (@$newsletters as $d)
					{
						if ($d->id > 0)
						{
							$checked = '';
							if (@$row->id > 0)
							{
								if ($isNewsletter = PhplistHelperSubscription::isUser(@$row->id, $d->id))
								{
									$checked = "checked='checked'";
								}
							}
							?>
					<tr>
						<td style="width: 10px; text-align: right;"><input type='checkbox'
							name='adduserto[<?php echo $d->id; ?>]'
							value='<?php echo $d->id; ?>' <?php echo $checked; ?> />
							</td>
						<td style="width:150px; text-align: left;"><?php 
						echo JText::_($d->name); ?>
						</td>
						<td>
					<?php 	if ($d->active == 0)
						{ ?> <span style="color: red;"> [ <?php	echo JText::_( 'UNPUBLISHED' ); ?>
						] </span> <?php } ?></td>
					</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>
			<?php
			echo $this->sliders->endPanel();
			
			$legend = JText::_( "ATTRIBUTES" );
			echo $this->sliders->startPanel( JText::_( $legend ), 'dashboard' );
			?>
			<table class="adminlist">
				<tbody>
				<?php
					
				JLoader::import( 'com_phplist.helpers.attribute', JPATH_ADMINISTRATOR.DS.'components' );
				$attributes_list = "";
				$attributes = array();
					$attributes = PhplistHelperAttribute::getAttributes();
					for ($r=0; $r<count($attributes); $r++)
					{
						$attr = $attributes[$r];
						$title = strtoupper($attr->name);
						$value = PhplistHelperAttribute::getAttributeValue(@$row->id, $attr->id);
						if ($value == false)
						{
							//if no value, check for default
							$value = PhplistHelperAttribute::getAttributeDefault($attr->id);
						}
						$input = PhplistHelperAttribute::formInput( $attr->id, $value, $attr->type, $attr->name);
						if ($input == NULL) {
							$attributes_list .= '';
						}
						else
						{
							$attributes_list .= "<tr><th style='width: 25%;'>";
							$attributes_list .= "[" .$title."] : ";
							$attributes_list .= "</th><td>";
							$attributes_list .= $input;
							$attributes_list .= "</td><td>";
						if ($attr->required == 0)
						{ 
						$attributes_list .= '<span style="color: red;"> [ ' . JText::_( 'UNPUBLISHED' ) . ' ]'; ?>
						 </span> 
						<?php }
						} ?>
						</td></tr> <?php 
					} 
				
				echo $attributes_list; ?>
				</tbody>
			</table>
			<?php


			echo $this->sliders->endPanel();
			

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

			echo $this->sliders->endPane();
			?></td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="id" value="<?php echo @$row->id?>" /> <input
	type="hidden" name="task" value="" /></fieldset>
</form>
