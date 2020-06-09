<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Users
 * @subpackage      Users
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

?>
<div class="modal hide fade" id="moderateModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
				<h3><?php echo Text::_('COM_KUNENA_BATCH_USERS_OPTIONS'); ?></h3>
			</div>
			<div class="modal-body span11">
				<p><?php echo Text::_('COM_KUNENA_BATCH_USERS_TIP'); ?></p>
				<div class="control-group">
					<div class="controls">
						<?php echo $this->modcatlist; ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" type="button" onclick="document.id('catid').value='';"
						data-dismiss="modal">
					<?php echo Text::_('JCANCEL'); ?>
				</button>
				<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('batch_moderators');">
					<?php echo Text::_('JSUBMIT'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
