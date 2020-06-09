<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Ranks
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('dropdown.init');
HTMLHelper::_('behavior.tabstate');

?>

<script type="text/javascript">
	Joomla.orderTable = function () {
		var table = document.getElementById("sortTable");
		var direction = document.getElementById("directionTable");
		var order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this->listOrdering; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<div class="card card-block bg-faded p-2">
				<div class="module-title nav-header">
					<i class="icon-star-2"></i>
					<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_RANKS') ?>
				</div>
				<hr class="hr-condensed">
				<div class="tabbable-panel">
					<div class="tabbable-line">
						<ul class="nav nav-tabs">
							<li class="nav-item">
								<a class="nav-link active" href="#tab1"
								   data-toggle="tab"><?php echo Text::_('COM_KUNENA_A_RANKS'); ?></a></li>
							<li><a class="nav-link" href="#tab2"
							       data-toggle="tab"><?php echo Text::_('COM_KUNENA_A_RANKS_UPLOAD'); ?></a>
							</li>
						</ul>

						<div class="tab-content">
							<div class="tab-pane active" id="tab1">
								<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=ranks') ?>"
								      method="post"
								      id="adminForm" name="adminForm">
									<input type="hidden" name="task" value=""/>
									<input type="hidden" name="boxchecked" value="0"/>
									<input type="hidden" name="filter_order"
									       value="<?php echo $this->listOrdering; ?>"/>
									<input type="hidden" name="filter_order_Dir"
									       value="<?php echo $this->listDirection; ?>"/>
									<?php echo HTMLHelper::_('form.token'); ?>

									<div id="filter-bar" class="btn-toolbar">
										<div class="filter-search btn-group pull-left">
											<label for="filter_search"
											       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
											<input type="text" name="filter_search" id="filter_search" class="filter"
											       placeholder="<?php echo Text::_('COM_KUNENA_ATTACHMENTS_FIELD_INPUT_SEARCHFILE'); ?>"
											       value="<?php echo $this->escape($this->state->get('list.search')); ?>"
											       title="<?php echo Text::_('COM_KUNENA_RANKS_FIELD_INPUT_SEARCHRANKS'); ?>"/>
										</div>
										<div class="btn-group pull-left">
											<button class="btn tip" type="submit"
											        title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>">
												<i class="icon-search"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>
											</button>
											<button class="btn tip" type="button"
											        title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>"
											        onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();">
												<i class="icon-remove"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>
											</button>
										</div>
										<div class="btn-group pull-right hidden-phone">
											<?php echo KunenaLayout::factory('pagination/limitbox')->set('pagination', $this->pagination); ?>
										</div>
										<div class="btn-group pull-right hidden-phone">
											<label for="directionTable"
											       class="element-invisible"><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></label>
											<select name="directionTable" id="directionTable" class="input-medium"
											        onchange="Joomla.orderTable()">
												<option value=""><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></option>
												<?php echo HTMLHelper::_('select.options', $this->sortDirectionFields, 'value', 'text', $this->listDirection); ?>
											</select>
										</div>
										<div class="btn-group pull-right">
											<label for="sortTable"
											       class="element-invisible"><?php echo Text::_('JGLOBAL_SORT_BY'); ?></label>
											<select name="sortTable" id="sortTable" class="input-medium"
											        onchange="Joomla.orderTable()">
												<option value=""><?php echo Text::_('JGLOBAL_SORT_BY'); ?></option>
												<?php echo HTMLHelper::_('select.options', $this->sortFields, 'value', 'text', $this->listOrdering); ?>
											</select>
										</div>
										<div class="clearfix"></div>
									</div>

									<table class="table table-striped" id="rankList">
										<thead>
										<tr>
											<th width="1%">
												<input type="checkbox" name="toggle" value=""
												       onclick="Joomla.checkAll(this)"/>
											</th>
											<th width="10%">
												<?php echo Text::_('COM_KUNENA_RANKSIMAGE'); ?>
											</th>
											<th width="58%">
												<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_RANKS_LABEL_TITLE', 'title', $this->listDirection, $this->listOrdering); ?>
											</th>
											<th width="10%" class="nowrap center">
												<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_RANKS_SPECIAL', 'special', $this->listDirection, $this->listOrdering); ?>
											</th>
											<th width="10%" class="nowrap center">
												<?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_RANKSMIN', 'min', $this->listDirection, $this->listOrdering); ?>
											</th>
											<th width="1%" class="nowrap center hidden-phone">
												<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'id', $this->listDirection, $this->listOrdering); ?>
											</th>
										</tr>
										<tr>
											<td class="hidden-phone">
											</td>
											<td class="hidden-phone">
											</td>
											<td class="nowrap">
												<label for="filter_title"
												       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
												<input class="input-block-level input-filter filter" type="text"
												       name="filter_title" id="filter_title"
												       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
												       value="<?php echo $this->filterTitle; ?>"
												       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
											</td>
											<td class="nowrap center">
												<label for="filter_special"
												       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
												<select name="filter_special" id="filter_special"
												        class="select-filter filter"
												        onchange="Joomla.orderTable()">
													<option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
													<?php echo HTMLHelper::_('select.options', $this->specialOptions(), 'value', 'text', $this->filterSpecial); ?>
												</select>
											</td>
											<td class="nowrap center">
												<label for="filter_min"
												       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_SEARCHIN') ?></label>
												<input class="input-block-level input-filter filter" type="text"
												       name="filter_min" id="filter_min"
												       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
												       value="<?php echo $this->filterMinPostCount; ?>"
												       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
											</td>
											<td class="hidden-phone">
											</td>
										</tr>
										</thead>
										<tfoot>
										<tr>
											<td colspan="6">
												<?php echo KunenaLayout::factory('pagination/footer')->set('pagination', $this->pagination); ?>
											</td>
										</tr>
										</tfoot>
										<tbody>
										<?php
										$i = 0;

										if ($this->pagination->total > 0)
											:
											foreach ($this->items as $id => $row)
												:
												?>
												<tr>
													<td>
														<input type="checkbox" id="cb<?php echo $id; ?>" name="cid[]"
														       value="<?php echo $this->escape($row->rank_id); ?>"
														       onclick="Joomla.isChecked(this.checked);"/>
													</td>
													<td>
														<a href="#edit"
														   onclick="return listItemTask('cb<?php echo $id; ?>','edit')">
															<img src="<?php echo $this->escape($this->ktemplate->getRankPath($row->rank_image, true)) ?>"
															     alt="<?php echo $this->escape($row->rank_image); ?>"/>
														</a>
													</td>
													<td class="nowrap">
														<a href="#edit"
														   onclick="return listItemTask('cb<?php echo $id; ?>','edit')">
															<?php echo $this->escape($row->rank_title); ?>
														</a>
													</td>
													<td class="nowrap center">
														<?php echo $row->rank_special == 1 ? Text::_('COM_KUNENA_YES') : Text::_('COM_KUNENA_NO'); ?>
													</td>
													<td class="nowrap center">
														<?php echo $this->escape($row->rank_min); ?>
													</td>
													<td class="nowrap center">
														<?php echo $this->escape($row->rank_id); ?>
													</td>
												</tr>
											<?php
											endforeach;
										else
											:
											?>
											<tr>
												<td colspan="10">
													<div class="card card-block bg-faded p-2 center filter-state">
															<span><?php echo Text::_('COM_KUNENA_FILTERACTIVE'); ?>
																<?php
																if ($this->filterActive)
																	:
																	?>
																	<button class="btn btn-default" type="button"
																	        onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
																<?php else
																	:
																	?>
																	<button class="btn btn-success" type="button"
																	        onclick="Joomla.submitbutton('add');"><?php echo Text::_('COM_KUNENA_NEW_RANK'); ?></button>
																<?php endif; ?>
															</span>
													</div>
												</td>
											</tr>
										<?php endif; ?>
										</tbody>
									</table>
								</form>
							</div>

							<div class="tab-pane" id="tab2">
								<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>"
								      id="uploadForm" method="post"
								      enctype="multipart/form-data">
									<input type="hidden" name="view" value="ranks"/>
									<input type="hidden" name="task" value="rankupload"/>
									<input type="hidden" name="boxchecked" value="0"/>
									<?php echo HTMLHelper::_('form.token'); ?>

									<input type="file" id="file-upload" class="btn btn-default" name="Filedata"/>
									<input type="submit" id="file-upload-submit" class="btn btn-primary"
									       value="<?php echo Text::_('COM_KUNENA_A_START_UPLOAD'); ?>"/>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>

	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
