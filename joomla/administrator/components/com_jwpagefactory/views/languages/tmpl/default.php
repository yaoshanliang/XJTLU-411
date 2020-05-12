<?php
/**
 * @author       JoomWorker
 * @email        info@joomla.work
 * @url          http://www.joomla.work
 * @copyright    Copyright (c) 2010 - 2019 JoomWorker
 * @license      GNU General Public License version 2 or later
 * @date         2019/01/01 09:30
 */

//no direct accees
defined('_JEXEC') or die ('restricted access');
JHtml::_('jquery.framework');
$doc = JFactory::getDocument();
$doc->addScriptdeclaration('var pagefactory_base="' . JURI::root() . 'administrator/";');
$doc->addStylesheet(JURI::base(true) . '/components/com_jwpagefactory/assets/css/font-awesome-5.min.css');
$doc->addStylesheet(JURI::base(true) . '/components/com_jwpagefactory/assets/css/font-awesome-v4-shims.css');
$doc->addStylesheet(JURI::base(true) . '/components/com_jwpagefactory/assets/css/pbfont.css');
$doc->addStylesheet(JURI::base(true) . '/components/com_jwpagefactory/assets/css/jwpagefactory.css');
$doc->addScript(JURI::base(true) . '/components/com_jwpagefactory/assets/js/languages.js');
$doc->addScript(JURI::base(true) . '/components/com_jwpagefactory/assets/js/notice.js');

require_once JPATH_ADMINISTRATOR . '/components/com_jwpagefactory/helpers/languages.php';
$languages = JwpagefactoryHelperLanguages::language_list();

?>

<div class="jw-pagefactory-admin-top"></div>
<div class="jw-pagefactory-admin-notice"></div>

<div class="jw-pagefactory-admin clearfix" style="position: relative;">
	<div id="j-sidebar-container" class="span2">
		<?php echo JLayoutHelper::render('brand'); ?>
		<?php echo $this->sidebar; ?>
	</div>

	<div id="j-main-container" class="span10">
		<div class="jw-pagefactory-main-container-inner">
			<div class="jw-pagefactory-pages-toolbar clearfix"></div>
			<?php
			$app = JFactory::getApplication();
			$messages = $app->getMessageQueue();
			if (empty($languages)) {
				$messages = array(array('type' => 'warning', 'message' => JText::_('JGLOBAL_NO_MATCHING_RESULTS')));
			}
			?>

			<?php if (isset($messages) && count((array)$messages)) { ?>
				<div class="jw-pagefactory-message-container">
					<?php foreach ($messages as $key => $message) { ?>
						<div class="alert alert-<?php echo str_replace(array('message', 'error', 'notice'), array('success', 'danger', 'info'), $message['type']); ?>">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<h4 class="alert-heading"><?php echo ucfirst($message['type']); ?></h4>
							<div class="alert-message"><?php echo $message['message']; ?></div>
						</div>
						<?php
					} ?>
				</div>
			<?php } ?>

			<div class="jw-pagefactory-pages jw-pagefactory-languages">
				<table class="table table-striped jw-pagefactory-language-list" id="pageList">
					<thead>
					<tr>
						<th width="5%" class="center">
							#
						</th>
						<th width="60%">
							<?php echo JText::_('COM_JWPAGEFACTORY_FIELD_LANGUAGE'); ?>
						</th>
						<th width="12%" class="hidden-phone center">
							<?php echo JText::_('COM_JWPAGEFACTORY_FIELD_LANGUAGE_TAG'); ?>
						</th>
						<th width="8%" class="hidden-phone center">
							Image
						</th>
						<th width="10%" class="hidden-phone center">
							<?php echo JText::_('COM_JWPAGEFACTORY_FIELD_INSTALLED'); ?>
						</th>
						<th width="10%" class="hidden-phone">
							<?php echo JText::_('COM_JWPAGEFACTORY_FIELD_ACTION'); ?>
						</th>
					</tr>
					</thead>

					<tbody>
					<?php $item_no = 1;
					$newLang = (array)$languages;
					ksort($newLang);
					$languages = (object)$newLang;

					foreach ($languages as $key => $language) {
						$class = "available";
						$installed_version = 'NOT INSTALLED';
						$update_class = '';
						$update_status = '';

						if (count((array)$this->items)) {
							foreach ($this->items as $this->item) {
								if ($this->item->lang_key == $key) {
									if ($this->item->state == 0) {
										$class = "installed";
									} else if ($this->item->state == 1) {
										$class = "enabled";
									}

									$installed_version = $this->item->version;
									if ($language->version > $this->item->version) {
										$update_class = 'label-warning';
										$update_status = 'available';
									} else {
										$update_class = 'label-success';
										$update_status = 'updated';
									}
								}
							}
						} ?>
						<tr class="<?php echo $class . ' update-' . $update_status; ?>" data-language="<?php echo $key; ?>">
							<td class="center">
								<?php echo $item_no; ?>
							</td>
							<td>
								<p>
											<span class="language-title">
												<?php echo $this->escape($language->title); ?>
											</span>
								</p>
							</td>
							<td class="center">
								<?php echo $language->lang_tag; ?>
							</td>
							<td class="center">
								<img src="<?php echo JURI::root(true) . '/media/mod_languages/images/' . strtolower(str_ireplace('-', '_', $language->lang_tag)); ?>.gif" alt="<?php echo $language->lang_tag; ?>" title="<?php echo $language->lang_tag; ?>">
							</td>
							<td class="center installed-version">
								<span class="label <?php echo $update_class; ?>"><?php echo $installed_version; ?></span>
							</td>
							<td>
								<div class="jw-pagefactory-lang-btns">
									<a class="jw-pagefactory-btn jw-pagefactory-btn-success jw-pagefactory-btn-sm jw-pagefactory-btn-install" href="#" style="color: #fff; margin: 5px 0;"><i></i><?php echo JText::_('COM_JWPAGEFACTORY_INSTALL'); ?></a>
									<?php if ($update_status == 'available') { ?>
										<a class="jw-pagefactory-btn jw-pagefactory-btn-warning jw-pagefactory-btn-sm jw-pagefactory-btn-update" href="#" style="color: #fff; margin: 5px 0;"><i></i><?php echo JText::_('COM_JWPAGEFACTORY_UPDATE'); ?></a>
									<?php } ?>
									<a class="jw-pagefactory-btn jw-pagefactory-btn-info jw-pagefactory-btn-sm jw-pagefactory-btn-up-to-date" href="#" style="color: #fff; margin: 5px 0;"><i></i><?php echo JText::_('COM_JWPAGEFACTORY_INSTALLED'); ?></a>
								</div>
							</td>
						</tr>
						<?php $item_no++;
					} ?>
					</tbody>
				</table>
			</div>

		</div>
	</div>
</div>
