<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{purgeButton}"  : "[data-purge-button]",
		"{cancelButton}"  : "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function()
		{
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_KOMENTO_MAILER_PURGE_PENDING_DIALOG_TITLE'); ?></title>
	<content>
		<p><?php echo JText::_('COM_KOMENTO_MAILER_PURGE_PENDING_CONFIRMATION'); ?></p>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-kt-default btn-sm"><?php echo JText::_('COM_KOMENTO_CANCEL_BUTTON'); ?></button>
		<button data-purge-button type="button" class="btn btn-kt-danger btn-sm"><?php echo JText::_('COM_KOMENTO_PURGE_PENDING_BUTTON'); ?></button>
	</buttons>
</dialog>
