<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Prune
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>

<div class="alert alert-error">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_ALERTBOX_WARNING') ?></h4>
	<?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_ALERTBOX_DESC') ?>
</div>

<
<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<div class="card card-block bg-faded p-2">
				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
				      method="post" id="adminForm"
				      name="adminForm">
					<input type="hidden" name="task" value="uninstall"/>
					<?php echo HTMLHelper::_('form.token'); ?>

					<fieldset>
						<legend><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_TITLE'); ?></legend>
						<table class="table table-bordered table-striped">
							<tr>
								<td colspan="2"><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_DESC') ?></td>
							</tr>
							<tr>
								<td width="20%"><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_LOGIN') ?></td>
								<td>
									<div>
										<input class="col-md-3" type="text" name="username" value=""/>
									</div>
								</td>
							</tr>
							<tr>
								<td width="20%"><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_PASSWORD') ?></td>
								<td>
									<div>
										<input class="col-md-3" type="password" name="password" value=""/>
									</div>
								</td>
							</tr>
							<?php if ($this->isTFAEnabled)
								:
								?>
								<tr>
									<td width="20%"><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_SECRETKEY') ?></td>
									<td>
										<div>
											<input class="col-md-3" type="text" name="secretkey" value=""/>
										</div>
									</td>
								</tr>
							<?php endif; ?>
							<tr>
								<td></td>
								<td>
									<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog"
									     aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
												×
											</button>
											<h3 id="myModalLabel"><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_TITLE'); ?></h3>
										</div>
										<div class="modal-body">
											<p><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_DESC') ?></p>
										</div>
										<div class="modal-footer">
											<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
												Close
											</button>
											<button type="submit"
											        class="btn btn-danger"><?php echo Text::_('COM_KUNENA_TOOLS_BUTTON_UNINSTALL_PROCESS') ?></button>
										</div>
									</div>

									<button type="button" class="btn btn-danger" data-toggle="modal"
									        data-target="#myModal"><?php echo Text::_('COM_KUNENA_TOOLS_BUTTON_UNINSTALL_PROCESS') ?></button>
								</td>
							</tr>
						</table>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
