<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('stylesheet', 'phplist.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('stylesheet', 'menu.css', 'media/com_phplist/css/'); ?>
<?php JHTML::_('script', 'phplist.js', 'media/com_phplist/js/'); ?>
<?php JHTML::_('script', 'joomla.javascript.js', 'includes/js/'); ?>
<?php $state = @$this->state; ?>
<?php $form = @$this->form; ?>
<?php $items = @$this->items; ?>
<?php $row = @$this->row; ?>
<?php $attributes = @$this->attributes; ?>

<div class='componentheading'>
     <span><?php echo JText::_( "PREFERENCES" ); ?></span>
</div>

<?php echo PhplistMenu::display(); ?>

<div id='onBeforeDisplay_wrapper'>
<?php 
	$dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger( 'onBeforeDisplayPreferencesForm', array( $this->row, $this->user ) );
?>
</div>

<form action="<?php echo JRoute::_( @$form['action'] )?>" method="post"
	name="adminForm" enctype="multipart/form-data">
<table width="100%">
	<tbody>
		<tr>
			<td valign="top">
				<table class="adminlist">
					<thead>
						<tr>
							<th style="text-align: left">
								<?php echo JText::_( "YOUR EMAIL" ).":"; ?>
							</th>
							<th style="text-align: left">
								<?php //only allow email addess to be edited if not a joomla user
								if ($row->foreignkey == '') : ?>
									<input type="text" name="email" value="<?php echo $this->email; ?>" />
								<?php else : echo $this->email; ?>
									<input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
								<?php endif; ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th width="20%" align="right">
								<?php echo JText::_("WOULD YOU PREFER HTML EMAILS")."?"; ?>
							</th>
							<td width="80%">
								<?php echo JHTML::_('select.booleanlist', 'htmlemail', 'class="inputbox"', $row->htmlemail ); ?>
							</td>
						</tr>
						<?php if ($attributes) :
								for ($r=0; $r<count($attributes); $r++) :
									$attr = $attributes[$r];
									$title = $attr->name;
									$input = PhplistHelperAttribute::formInput( $attr->id, $attr->value, $attr->type, $attr->name);
									if ($input != NULL) : ?>
						<tr>
							<th style="width: 25%;">
								<?php echo JText::_($title); ?>
							</th>
							<td>
								<?php echo $input; if ($attr->required == '1') echo "*"; ?>
							</td>
						</tr>
									<?php endif; ?>
								<?php endfor; ?>
							<?php endif; ?>
					</tbody>
				</table>
			</td>
			<td valign="top">
				<div id='onDisplayRightColumn_wrapper'><?php
					$dispatcher = JDispatcher::getInstance();
					$dispatcher->trigger( 'onDisplayPreferencesFormRightColumn', array( $this->row, $this->user ) ); ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div id='onAfterDisplay_wrapper'><?php 
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger( 'onAfterDisplayPreferencesForm', array( $this->row, $this->user ) );
?></div>

<input type='button' class='button' onclick="submitform('cancel')" value='<?php echo JText::_( 'CANCEL' ); ?>' /> 
<input type='button' class='button' onclick="submitform('save')" value='<?php echo JText::_( 'SAVE PREFERENCES' ); ?>' />

<p><?php  echo "*". JText::_( "or" )  . " " . $this->required->image . " " . JText::_( 'Required Field' ); ?></p>

<input type="hidden" name="task" value="" /> 
<input type="hidden" name="boxchecked" value="" /> 
<input type="hidden" name="filter_order" value="<?php  echo @$state->order; ?>" /> 
<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" /> 
<?php echo $this->form['validate']; ?>
</form>
