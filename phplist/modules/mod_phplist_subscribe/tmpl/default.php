<?php
/**
 * @version	1.5
 * @package	Phplist
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

$can_subscribe = false;
$submit_button = false;
$boxchecked = '';
$hide = false;
JHTML::_('script', 'mod_phplist_subscribe.js', 'modules/mod_phplist_subscribe/');
JHTML::_('script', 'common.js', 'media/com_phplist/js/');
JHTML::_('script', 'joomla.javascript.js', 'includes/js/');

$element = 'com_phplist';
$lang =& JFactory::getLanguage();
$lang->load( $element, JPATH_BASE );
?>
<span id="mod_phplist_subscribe-container"></span>
<div id='mod_phplist_subscribe-formWrapper<?php echo $moduleclass_sfx; ?>'>
	
<?php if ($params->get('header', 0)) : ?>
<div class="header<?php echo $moduleclass_sfx; ?>">
	<?php echo JText::_( $params->get('header')) ; ?>
</div>
<?php endif; ?>

<form name="modPhplistSubscribeForm" onsubmit="phplistFormValidation( 'index.php?option=com_phplist&task=validate&format=raw', 'mod_phplist_subscribe-container', 'subscribeModule', document.modPhplistSubscribeForm )" method="post" id="modPhplistSubscribeForm" action="index.php?option=com_phplist&task=subscribeModule" enctype="multipart/form-data">
	<input type="hidden" name="display_type" value="<?php echo $display_type; ?>" />

<?php 
	if ($display_type == '1') :
		// display checkbox list of newsletters
		$newsletters = PhplistHelperNewsletter::getNewsletters('1');
		for ($i=0; $i<count($newsletters); $i++) :
			$letter = $newsletters[$i];
			if ($phplistUser) :
				$hide[$i] = PhplistHelperSubscription::isUser( $phplistUser->id, $letter->id );
			endif;
			if ($hide[$i] == false) : ?>
				<input type="checkbox" name="cid[]" value="<?php echo $letter->id; ?>" onclick="NewsletterisChecked(this.checked);"/> <?php
		 		echo JText::_( $letter->name ); 
				$can_subscribe = true; ?>
				<br/><?php
		 	else : ?>
		 	<input type="checkbox" name="cid[]" value="<?php echo $letter->id; ?>" disabled checked="checked" /> <?php
		 		echo JText::_( $letter->name ); ?>
				<br/><?php
			endif;
	 	endfor;
	else :
		// display hidden text field with newslettersid
		for ($i=0; $i<count($newsletterid); $i++) :
		?>
		<input type="hidden" name="cid[]" value="<?php echo $newsletterid[$i]; ?>" /><?php
		endfor;
 	endif;

if ($display_html) : ?>
	<div class="<?php echo $moduleclass_sfx; ?>">
		<?php echo JText::_("HTML Emails");
		echo JHTML::_('select.booleanlist', 'htmlemail', 'class="inputbox"', '1', JText::_("Yes"), JText::_("No"), 'modhtml' ); ?>
	</div>		
<?php endif;

if (!$phplistUser) :
	$submit_button = true;
	// Display form for user not logged in ?>
	<input type="text" name="subscriber2add" value="<?php echo JText::_( 'Email Address' ); ?>" onfocus="if(this.value=='<?php echo JText::_( 'Email Address' ); ?>') this.value=''" />
	<?php if ($attributes) : ?>
	<table class="<?php echo $moduleclass_sfx; ?>">
	<?php for ($r=0; $r<count($attributes); $r++) :
			$attr = $attributes[$r];
			$title = $attr->name;
			$input = PhplistHelperAttribute::formInput( $attr->id, '', $attr->type, $attr->name);
			if ($input != NULL) : ?>
				<tr>
					<th><?php echo JText::_($title); ?></th>
					<td><?php echo $input; if ($attr->required == '1') echo "*"; ?></td>
				</tr><?php
			endif;
		endfor; ?>
	</table><?php
endif;
else :
	// Display form for logged in Joomla! user or UID
	if (!$isSubscribed || $can_subscribe) :
		//display email address to add subscription for
		echo JText::_( "Using this address" ).": ".$phplistUser->email; ?>
		<input type="hidden" name="subscriberemail" value="<?php echo $phplistUser->email; ?>" />
		<?php $submit_button = true;
	elseif ($display_type == '0' && $display_already == '1') :
			echo JText::_( "You are already subscribed to our newsletter" );
	elseif (!$can_subscribe) :
		echo JText::_( "You are already subscribed to our newsletters" );
	endif;
endif;
if ($submit_button) : ?>
<img src="<?php echo Phplist::getURL('images')."add.png"; ?>" style="max-height: 24px; vertical-align: middle;" onclick="phplistSubmitModuleForm('subscribeModule')" onmouseover="this.style.cursor='pointer'" alt="<?php echo JText::_("SUBSCRIBE"); ?>" />
<a href="javascript:void(0);" style="padding-left:3px;" onclick="phplistSubmitModuleForm('subscribeModule')">
						<?php echo JText::_("SUBSCRIBE"); ?>
					</a>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="<?php if ($display_type != '1') echo '1';?>" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
<?php endif; ?>


<?php if ($params->get('footer', 0)) : ?>
	<div class="footer<?php echo $moduleclass_sfx; ?>">
		<?php echo JText::_( $params->get('footer') ); ?>
	</div>
<?php endif; ?>

</div>
	
<?php
if ($display_url == '1') : ?>
	<div class="link<?php echo $moduleclass_sfx; ?>">
		<a href="<?php echo $newsletters_link; ?>">
			<?php echo JText::_( "NEWSLETTERS LINK TEXT" ); ?>
		</a>
	</div>
<?php endif; ?>
<?php
if ($display_url_prefs == '1' && $phplistUser) : ?>
	<div class="link<?php echo $moduleclass_sfx; ?>">
		<a href="<?php echo $prefs_link; ?>">
			<?php echo JText::_( "PREFS LINK TEXT" ); ?>
		</a>
	</div>
<?php endif; ?>

</form>