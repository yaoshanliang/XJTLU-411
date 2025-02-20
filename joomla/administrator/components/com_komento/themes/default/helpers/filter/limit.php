<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<select name="<?php echo $name;?>" class="o-form-control" data-kt-table-filter data-table-grid-filter>
	<?php for ($i = $step; $i <= $max; $i = $i + $step) { ?>
		<option value="<?php echo $i;?>"<?php echo $i == $selected ? ' selected="selected"' : '';?>><?php echo $i;?></option>
	<?php } ?>

	<?php if ($showAll) { ?>
	<option value="all"<?php echo $selected == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_KOMENTO_ALL'); ?></option>
	<?php } ?>
</select>
