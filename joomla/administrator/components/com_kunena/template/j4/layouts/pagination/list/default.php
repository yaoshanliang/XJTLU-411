<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Layouts.Pagination
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

$data = $this->pagination->getData();

if ($data->pages && count($data->pages) <= 1)
{
	return;
}

$template = new KunenaAdminTemplate4;
?>
<ul class="pagination pagination-sm justify-content-center">
	<?php echo $template->paginationItem($data->start); ?>
	<?php echo $template->paginationItem($data->previous); ?>

	<?php foreach ($data->pages as $k => $page)
	{
		echo $template->paginationItem($page);
	} ?>

	<?php echo $template->paginationItem($data->next); ?>
	<?php echo $template->paginationItem($data->end); ?>
</ul>
