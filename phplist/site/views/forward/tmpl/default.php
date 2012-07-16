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
?>
			
		<div class='componentheading'>
    		<?php echo $this->pagetitle; ?>
		</div>

		<div id='onBeforeDisplay_wrapper'>
			<?php 
				$dispatcher =& JDispatcher::getInstance();
				$dispatcher->trigger( 'onBeforeDisplayMessage', array( $this->row, $this->user ) );
			?>
		</div>

		<table class="invisible">
			<tbody>
				<tr>
			        <th colspan='2' style='border-bottom: 1px solid #e5e5e5;'>
						<?php
						if ($this->pagetitle_newsletter && $this->link_newsletter) {
							echo "<< <a href='{$this->link_newsletter}'>".JText::_( 'Return to' )." {$this->pagetitle_newsletter}</a>"; 
						}
						?>
			        </th>
			    </tr>            
                <tr>
					<td valign="top">
					<?php 
						echo $this->text;
					?>
					</td>
					<td valign="top">
						
						<div id='onDisplayRightColumn_wrapper'>
							<?php 
								$dispatcher =& JDispatcher::getInstance();
								$dispatcher->trigger( 'onDisplayMessageRightColumn', array( $this->row, $this->user ) );
							?>
						</div>

					</td>
                </tr>
            </tbody>
		</table>

		<div id='onAfterDisplay_wrapper'>
			<?php 
				$dispatcher =& JDispatcher::getInstance();
				$dispatcher->trigger( 'onAfterDisplayMessage', array( $this->row, $this->user ) );
			?>
		</div>
