<?php defined('_JEXEC') or die('Restricted access'); ?>

	<?php
		$defines = Phplist::getInstance();
		$img_file = "dioscouri_logo_transparent.png";
		$img_path = "../media/com_phplist/images";

		$url = "http://www.dioscouri.com/";
		if ($amigosid = $defines->get( 'amigosid', '' ))
		{
			$url .= "?amigosid=".$amigosid;
		}
	?>

	<table style="margin-bottom: 5px; width: 100%; border-top: thin solid #e5e5e5;">
	<tbody>
	<tr>
		<td style="text-align: left; width: 33%;">
			<a href="<?php echo $url; ?>" target="_blank"><?php echo JText::_( 'Dioscouri.com Support Center' ); ?></a>
			<br/>
			<a href="http://twitter.com/dioscouri" target="_blank"><?php echo JText::_( "Follow Us on Twitter" ); ?></a>
			<br/>
			<a href="http://extensions.joomla.org/extensions/owner/dioscouri" target="_blank"><?php echo JText::_( "Leave JED Feedback" ); ?></a>
		</td>
		<td style="text-align: center; width: 33%;">
			<?php echo JText::_( "PHPLIST" ); ?>: <?php echo JText::_( "COM_PHPLIST_DESC" ); ?>
			<br/>
			<?php echo JText::_( "Copyright" ); ?>: <?php echo $defines->getCopyrightYear(); ?> &copy; <a href="<?php echo $url; ?>" target="_blank">Dioscouri Design</a>
			<br/>
			<?php echo JText::_( "Version" ); ?>: <?php echo $defines->getVersion(); ?>
			<br/>
			<?php // echo sprintf( JText::_('PHPLIST_VERSION_LINE'), Phplist::getPHPListRecomendedVersion(), Phplist::getPHPListVersion());?>
			<br/>
			<?php echo sprintf( JText::_('PHP_VERSION_LINE'), $defines->getMinPhp(), $defines->getServerPhp() );?>
			<br/>
			<?php echo JText::_( 'Powered by' )." <a href='http://www.phplist.com' target='_blank'>".JText::_( 'Phplist' )."</a>"; ?>
		</td>
		<td style="text-align: right; width: 33%;">
			<a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $img_path."/".$img_file;?>"></img></a>
		</td>
	</tr>
	</tbody>
	</table>
