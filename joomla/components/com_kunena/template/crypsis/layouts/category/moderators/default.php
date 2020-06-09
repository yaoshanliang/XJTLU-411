<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Category
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

?>
<?php if (!empty($this->moderators)) : ?>
	<div>
		<?php
		echo Text::_('COM_KUNENA_MODERATORS') . ": ";

		$mods_lin = array();

		foreach ($this->moderators as $moderator)
		{
			$mods_lin[] = "{$moderator->getLink(null, null, '', '', null)}";
		}

		echo implode(',&nbsp;', $mods_lin);
		?>
	</div>
<?php endif;
