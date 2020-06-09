<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Topic
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Load scripts to handle fileupload process
Text::script('COM_KUNENA_UPLOADED_LABEL_INSERT_ALL_BUTTON');
Text::script('COM_KUNENA_EDITOR_INSERT');
Text::script('COM_KUNENA_EDITOR_IN_MESSAGE');
Text::script('COM_KUNENA_GEN_REMOVE_FILE');
Text::sprintf('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUMBER_FILES', $this->config->attachment_limit, array('script' => true));
Text::script('COM_KUNENA_UPLOADED_LABEL_UPLOAD_BUTTON');
Text::script('COM_KUNENA_UPLOADED_LABEL_PROCESSING_BUTTON');
Text::script('COM_KUNENA_UPLOADED_LABEL_ABORT_BUTTON');
Text::script('COM_KUNENA_UPLOADED_LABEL_DRAG_AND_DROP_OR_BROWSE');
Text::script('COM_KUNENA_EDITOR_BOLD');
Text::script('COM_KUNENA_EDITOR_COLORS');
Text::script('COM_KUNENA_EDITOR_OLIST');
Text::script('COM_KUNENA_EDITOR_TABLE');
Text::script('COM_KUNENA_EDITOR_ITALIC');
Text::script('COM_KUNENA_EDITOR_UNDERL');
Text::script('COM_KUNENA_EDITOR_STRIKE');
Text::script('COM_KUNENA_EDITOR_SUB');
Text::script('COM_KUNENA_EDITOR_SUP');
Text::script('COM_KUNENA_EDITOR_CODE');
Text::script('COM_KUNENA_EDITOR_QUOTE');
Text::script('COM_KUNENA_EDITOR_SPOILER');
Text::script('COM_KUNENA_EDITOR_CONFIDENTIAL');
Text::script('COM_KUNENA_EDITOR_HIDE');
Text::script('COM_KUNENA_EDITOR_RIGHT');
Text::script('COM_KUNENA_EDITOR_LEFT');
Text::script('COM_KUNENA_EDITOR_CENTER');
Text::script('COM_KUNENA_EDITOR_HR');
Text::script('COM_KUNENA_EDITOR_FONTSIZE_SELECTION');
Text::script('COM_KUNENA_EDITOR_LINK');
Text::script('COM_KUNENA_EDITOR_EBAY');
Text::script('COM_KUNENA_EDITOR_MAP');
Text::script('COM_KUNENA_EDITOR_POLL_SETTINGS');
Text::script('COM_KUNENA_EDITOR_VIDEO');
Text::script('COM_KUNENA_EDITOR_IMAGELINK');
Text::script('COM_KUNENA_EDITOR_EMOTICONS');
Text::script('COM_KUNENA_EDITOR_TWEET');
Text::script('COM_KUNENA_EDITOR_INSTAGRAM');
Text::script('COM_KUNENA_EDITOR_SOUNDCLOUD');
Text::script('COM_KUNENA_EDITOR_REMOVE_INLINE');
Text::script('COM_KUNENA_EDITOR_COLOR_BLACK');
Text::script('COM_KUNENA_EDITOR_COLOR_ORANGE');
Text::script('COM_KUNENA_EDITOR_COLOR_RED');
Text::script('COM_KUNENA_EDITOR_COLOR_BLUE');
Text::script('COM_KUNENA_EDITOR_COLOR_PURPLE');
Text::script('COM_KUNENA_EDITOR_COLOR_GREEN');
Text::script('COM_KUNENA_EDITOR_COLOR_WHITE');
Text::script('COM_KUNENA_EDITOR_COLOR_GRAY');
Text::script('COM_KUNENA_EDITOR_ULIST');
Text::script('COM_KUNENA_EDITOR_LIST');
Text::script('COM_KUNENA_EDITOR_SIZE_VERY_VERY_SMALL');
Text::script('COM_KUNENA_EDITOR_SIZE_VERY_SMALL');
Text::script('COM_KUNENA_EDITOR_SIZE_SMALL');
Text::script('COM_KUNENA_EDITOR_SIZE_NORMAL');
Text::script('COM_KUNENA_EDITOR_SIZE_BIG');
Text::script('COM_KUNENA_EDITOR_SIZE_SUPER_BIGGER');

$this->addScriptOptions('com_kunena.imageheight', $this->config->imageheight);
$this->addScriptOptions('com_kunena.imagewidth', $this->config->imagewidth);

HTMLHelper::_('jquery.ui');
$this->addScript('assets/js/load-image.min.js');
$this->addScript('assets/js/canvas-to-blob.min.js');
$this->addScript('assets/js/jquery.fileupload.js');
$this->addScript('assets/js/jquery.fileupload-process.js');
$this->addScript('assets/js/jquery.iframe-transport.js');
$this->addScript('assets/js/jquery.fileupload-image.js');
$this->addScript('assets/js/jquery.fileupload-audio.js');
$this->addScript('assets/js/jquery.fileupload-video.js');
$this->addScript('assets/js/upload.main.js');
$this->addStyleSheet('assets/css/fileupload.css');

$this->k = 0;

$this->addScriptOptions('com_kunena.kunena_upload_files_rem', KunenaRoute::_('index.php?option=com_kunena&view=topic&task=removeattachments&format=json&' . Session::getFormToken() . '=1', false));
$this->addScriptOptions('com_kunena.kunena_upload_files_rem_inline', KunenaRoute::_('index.php?option=com_kunena&view=topic&task=removeinline&format=json&' . Session::getFormToken() . '=1', false));
$this->addScriptOptions('com_kunena.kunena_upload_files_preload', KunenaRoute::_('index.php?option=com_kunena&view=topic&task=loadattachments&format=json&' . Session::getFormToken() . '=1', false));
$this->addScriptOptions('com_kunena.kunena_upload_files_maxfiles', $this->config->attachment_limit);
$this->addScriptOptions('com_kunena.icons.upload', KunenaIcons::upload());
$this->addScriptOptions('com_kunena.icons.trash', KunenaIcons::delete());
$this->addScriptOptions('com_kunena.icons.attach', KunenaIcons::attach());

$suffix = Joomla\CMS\Application\CMSApplication::getInstance('site')->get('sef_suffix');
$this->addScriptOptions('com_kunena.suffixpreview', $suffix ? true : false);

$this->ktemplate = KunenaFactory::getTemplate();
$topicicontype   = $this->ktemplate->params->get('topicicontype');
$editor          = $this->ktemplate->params->get('editor');
$me              = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();

if ($editor == 0)
{
	$this->addScript('assets/js/markitup.js');
	$this->addScript('assets/js/markitup.editor.js');
	$this->addScript('assets/js/markitup.set.js');
}

// If polls are enabled, load also poll JavaScript.
$this->addScript('assets/js/pollcheck.js');

if ($this->config->pollenabled)
{
	Text::script('COM_KUNENA_POLL_OPTION_NAME');
	Text::script('COM_KUNENA_EDITOR_HELPLINE_OPTION');
	$this->addScript('assets/js/poll.js');
}

$this->addScriptOptions('com_kunena.editor', $this->ktemplate->params->get('editor'));
$this->addScriptOptions('com_kunena.kunena_topicicontype', $topicicontype);
$this->addScriptOptions('com_kunena.allow_edit_poll', $this->config->allow_edit_poll);

$this->addScript('assets/js/edit.js');

echo $this->subLayout('Widget/Lightbox');

if (KunenaFactory::getTemplate()->params->get('formRecover'))
{
	$this->addScript('assets/js/sisyphus.js');
}
?>
	<div id="modal_confirm_template_category" class="modal fade" tabindex="-1" role="dialog"
	     aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="myModalLabel"><?php echo Text::_('COM_KUNENA_MODAL_BOX_CATEGORY_TEMPLATE_TEXT_TITLE'); ?></h3>
				</div>
				<div class="modal-body">
					<p><?php echo Text::_('COM_KUNENA_MODAL_BOX_CATEGORY_TEMPLATE_TEXT_DESC'); ?></p>
				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal"
					        aria-hidden="true"><?php echo Text::_('COM_KUNENA_MODAL_BOX_CATEGORY_TEMPLATE_TEXT_CLOSE'); ?></button>
					<button class="btn btn-primary"
					        id="modal_confirm_erase"><?php echo Text::_('COM_KUNENA_MODAL_BOX_CATEGORY_TEMPLATE_TEXT_BUTTON_REPLACE'); ?></button>
					<button class="btn btn-primary"
					        id="modal_confirm_erase_keep_old"><?php echo Text::_('COM_KUNENA_MODAL_BOX_CATEGORY_TEMPLATE_TEXT_BUTTON_REPLACE_KEEP'); ?></button>
				</div>
			</div>
		</div>
	</div>

	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post"
	      class="form-horizontal form-validate" role="form"
	      id="postform" name="postform" enctype="multipart/form-data" data-page-identifier="1">
		<input type="hidden" name="view" value="topic"/>
		<input id="kurl_topicons_request" type="hidden"
		       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=topicicons&format=raw', false); ?>"/>
		<input id="kurl_category_template_text" type="hidden"
		       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=categorytemplatetext&format=raw', false); ?>"/>
		<input id="kcategory_poll" type="hidden" name="kcategory_poll" value="<?php echo $this->message->catid; ?>"/>
		<input id="kpreview_url" type="hidden" name="kpreview_url"
		       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=edit&format=raw', false) ?>"/>
		<?php if (!$this->config->allow_change_subject)
			:
			?>
			<input type="hidden" name="subject" value="<?php echo $this->escape($this->message->subject); ?>"/>
		<?php endif; ?>
		<?php
		if ($this->message->exists())
			:
			?>
			<input type="hidden" name="task" value="edit"/>
			<input id="kmessageid" type="hidden" name="mesid" value="<?php echo intval($this->message->id) ?>"/>
		<?php else

			:
			?>
			<input type="hidden" name="task" value="post"/>
			<input type="hidden" name="parentid" value="<?php echo intval($this->message->parent) ?>"/>
		<?php endif; ?>
		<?php
		if (!isset($this->selectcatlist))
			:
			?>
			<input type="hidden" name="catid" value="<?php echo intval($this->message->catid) ?>"/>
		<?php endif; ?>
		<?php
		if ($this->category->id && $this->category->id != $this->message->catid)
			:
			?>
			<input type="hidden" name="return" value="<?php echo intval($this->category->id) ?>"/>
		<?php endif; ?>
		<?php
		if ($this->message->getTopic()->first_post_id == $this->message->id && $this->message->getTopic()->getPoll()->id)
			:
			?>
			<input type="hidden" id="poll_exist_edit" name="poll_exist_edit"
			       value="<?php echo intval($this->message->getTopic()->getPoll()->id) ?>"/>
		<?php endif; ?>
		<input type="hidden" id="kunena_upload" name="kunena_upload"
		       value="<?php echo intval($this->message->catid) ?>"/>
		<input type="hidden" id="kunena_upload_files_url"
		       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&task=upload&format=json&' . Session::getFormToken() . '=1', false) ?>"/>
		<?php if ($this->me->exists())
			:
			?>
			<input type="hidden" id="kurl_users" name="kurl_users"
			       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>"/>
		<?php endif; ?>
		<?php echo HTMLHelper::_('form.token'); ?>

		<h1>
			<?php echo $this->escape($this->headerText) ?>
		</h1>

		<div class="well">
			<div class="row-fluid column-row">
				<div class="col-md-12 column-item">
					<fieldset>
						<?php if (isset($this->selectcatlist))
							:
							?>
							<div class="control-group">
								<!-- Username -->
								<label class="control-label"><?php echo Text::_('COM_KUNENA_CATEGORY') ?></label>

								<div class="controls"> <?php echo $this->selectcatlist ?> </div>
							</div>
						<?php endif; ?>
						<?php if ($this->message->userid) : ?>
							<div class="control-group" id="kanynomous-check"
							     <?php if (!$this->message->getCategory()->allow_anonymous) : ?>style="display:none;"<?php endif; ?>>
								<label class="control-label"><?php echo Text::_('COM_KUNENA_POST_AS_ANONYMOUS'); ?></label>

								<div class="controls" style="text-align: right">
									<input type="checkbox" id="kanonymous" name="anonymous"
									       value="1" <?php if ($this->post_anonymous)
									{
										echo 'checked="checked"';
									} ?> />
									<span><?php echo Text::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></span>
								</div>
							</div>
						<?php endif; ?>
						<div class="control-group" id="kanynomous-check-name"
						     <?php if ($this->me->userid && !$this->category->allow_anonymous)
						     :
						     ?>style="display:none;"<?php
						endif; ?>>
							<div class="alert alert-info"><?php echo Text::_('COM_KUNENA_GEN_INFO_GUEST_CANNOT_EDIT_DELETE_MESSAGE'); ?></div>

							<label class="control-label"><?php echo Text::_('COM_KUNENA_GEN_NAME'); ?></label>
							<div class="controls">
								<input type="text" id="kauthorname" name="authorname" size="35"
								       placeholder="<?php echo Text::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_AUTHORNAME') ?>"
								       class="input-xxlarge" maxlength="35" tabindex="4"
								       value="<?php echo $this->escape($this->message->name); ?>"/>
								<!-- Encourage guest user to login or register -->
								<?php
								$login    = '<a class="btn-link" href="' . Route::_('index.php?option=com_users&view=login&return=' . base64_encode((string) Uri::getInstance())) . '"> ' . Text::_('JLOGIN') . '</a>';
								$register = ' ' . Text::_('COM_KUNENA_LOGIN_OR') . ' <a class="btn-link" href="index.php?option=com_users&view=registration">' . Text::_('JREGISTER') . '</a>';
								echo Text::sprintf('COM_KUNENA_LOGIN_PLEASE_SKIP', $login, $register);
								?>
							</div>
						</div>
						<?php if ($this->config->askemail && !$this->me->userid)
							:
							?>
							<div class="control-group">
								<label class="control-label"><?php echo Text::_('COM_KUNENA_GEN_EMAIL'); ?></label>
								<div class="col-md-10">
									<input type="text" id="email" name="email" size="35"
									       placeholder="<?php echo Text::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_EMAIL') ?>"
									       class="form-control" maxlength="45" tabindex="5"
									       value="<?php echo !empty($this->message->email) ? $this->escape($this->message->email) : '' ?>"
									       required/>
									<br/>
									<?php echo $this->config->showemail == '0' ? Text::_('COM_KUNENA_POST_EMAIL_NEVER') : Text::_('COM_KUNENA_POST_EMAIL_REGISTERED'); ?>
								</div>
							</div>
						<?php endif; ?>

						<div class="form-group" id="kpost-subject">
							<label class="control-label col-md-4"><?php echo Text::_('COM_KUNENA_GEN_SUBJECT'); ?></label>
							<div class="col-md-10">
								<?php if (!$this->config->allow_change_subject && $this->topic->exists() && !KunenaUserHelper::getMyself()->isModerator($this->message->getCategory()))
									:
									?>
									<input class="form-control" type="text" name="subject" id="subject"
									       value="<?php echo $this->escape($this->message->subject); ?>" disabled/>
								<?php else

									:
									?>
									<input class="form-control" type="text"
									       placeholder="<?php echo Text::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_SUBJECT') ?>"
									       name="subject" id="subject"
									       maxlength="<?php echo $this->escape($this->ktemplate->params->get('SubjectLengthMessage')); ?>"
									       tabindex="6" value="<?php echo $this->escape($this->message->subject); ?>"/>
								<?php endif; ?>
							</div>
						</div>

						<?php if (!empty($this->topicIcons))
							:
							?>
							<div class="form-group" id="kpost-topicicons">
								<label class="col-md-3 control-label"><?php echo Text::_('COM_KUNENA_GEN_TOPIC_ICON'); ?></label>
								<div id="iconset_inject" class="controls controls-select">
									<div id="iconset_topic_list">
										<?php foreach ($this->topicIcons as $id => $icon)
										:
										?>
										<input type="radio" id="radio<?php echo $icon->id ?>" name="topic_emoticon"
										       value="<?php echo $icon->id ?>" <?php echo !empty($icon->checked) ? ' checked="checked" ' : '' ?> />
										<?php if ($this->config->topicicons && $topicicontype == 'B3')
										:
										?>
										<label class="radio inline" for="radio<?php echo $icon->id; ?>"><span
													class="glyphicon glyphicon-<?php echo $icon->b3; ?> glyphicon-topic"
													aria-hidden="true"></span>
											<?php elseif ($this->config->topicicons && $topicicontype == 'fa')
											:
											?>
											<label class="radio inline" for="radio<?php echo $icon->id; ?>"><i
														class="fa fa-<?php echo $icon->fa; ?> glyphicon-topic fa-2x"></i>
												<?php else

												:
												?>
												<label class="radio inline" for="radio<?php echo $icon->id; ?>"><img
															src="<?php echo $icon->relpath; ?>"
															alt="<?php echo $icon->name; ?>"
															border="0"/>
													<?php endif; ?>
												</label>
												<?php endforeach; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if ($editor == 1)
						{
							echo $this->subLayout('Widget/Editor')->setLayout('wysibb')->set('message', $this->message)->set('config', $this->config);
						}
						else
						{
							echo $this->subLayout('Widget/Editor')->setLayout('bbcode')->set('message', $this->message)->set('config', $this->config)->set('config', $this->config)->set('poll', $this->message->getTopic()->getPoll())->set('allow_polls', $this->topic->getCategory()->allow_polls);
						} ?>

						<?php if ($this->message->exists() && $this->config->editmarkup)
							:
							?>
							<div class="control-group" id="modified_reason">
								<label class="control-label"><?php echo Text::_('COM_KUNENA_EDITING_REASON') ?></label>

								<div class="controls">
									<input class="input-xxlarge form-control" name="modified_reason" size="40"
									       maxlength="200"
									       type="text"
									       value="<?php echo $this->modified_reason; ?>" title="reason"
									       placeholder="<?php echo Text::_('COM_KUNENA_EDITING_ENTER_REASON') ?>"/>
								</div>
							</div>
						<?php endif; ?>

						<?php if ($this->allowedExtensions)
							:
							?>
							<div class="control-group krow<?php echo 1 + $this->k ^= 1; ?>" id="kpost-attachments">
								<label class="control-label"></label>
								<div class="controls">
									<button class="btn btn-default" id="kshow_attach_form"
									        type="button"><?php echo KunenaIcons::attach() . ' ' . Text::_('COM_KUNENA_EDITOR_ATTACHMENTS'); ?></button>
									<div id="kattach_form" style="display: none;">
									<span class="label label-info"><?php echo Text::_('COM_KUNENA_FILE_EXTENSIONS_ALLOWED') ?>
										: <?php echo $this->escape(implode(', ', $this->allowedExtensions)) ?></span><br/><br/>
										<span class="label label-info"><?php echo Text::_('COM_KUNENA_UPLOAD_MAX_FILES_WEIGHT') ?>
											: <?php echo $this->config->filesize != 0 ? round($this->config->filesize / 1024, 1) : $this->config->filesize ?> <?php echo Text::_('COM_KUNENA_UPLOAD_ATTACHMENT_FILE_WEIGHT_MB') ?> <?php echo Text::_('COM_KUNENA_UPLOAD_MAX_IMAGES_WEIGHT') ?>
											: <?php echo $this->config->imagesize != 0 ? round($this->config->imagesize / 1024, 1) : $this->config->imagesize ?> <?php echo Text::_('COM_KUNENA_UPLOAD_ATTACHMENT_FILE_WEIGHT_MB') ?></span><br/><br/>

										<!-- The fileinput-button span is used to style the file input field as button -->
										<span class="btn btn-primary fileinput-button">
										<?php echo KunenaIcons::plus(); ?>
											<span><?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_ADD_FILES_BUTTON') ?></span>
											<!-- The file input field used as target for the file upload widget -->
										<input id="fileupload" type="file" name="file" multiple>
									</span>
										<button id="insert-all" class="btn btn-primary" type="submit"
										        style="display:none;">
											<?php echo KunenaIcons::upload(); ?>
											<span><?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_INSERT_ALL_BUTTON') ?></span>
										</button>
										<button id="remove-all" class="btn btn-danger" type="submit"
										        style="display:none;">
											<?php echo KunenaIcons::cancel(); ?>
											<span><?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_REMOVE_ALL_BUTTON') ?></span>
										</button>
										<div class="clearfix"></div>
										<br/>
										<div id="progress" class="progress progress-striped" style="display: none;">
											<div class="bar"></div>
										</div>
										<!-- The container for the uploaded files -->
										<div id="files" class="files"></div>
										<div id="dropzone">
											<div class="dropzone">
												<div class="default message">
												<span
														id="klabel_info_drop_browse"><?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_DRAG_AND_DROP_OR_BROWSE') ?></span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if ($this->canSubscribe): ?>
							<div class="control-group" id="kpost-subscribe">
								<label class="control-label"><?php echo Text::_('COM_KUNENA_POST_SUBSCRIBE'); ?></label>
								<div class="controls">
									<input style="float: left; margin-right: 10px;" type="checkbox" name="subscribeMe"
									       id="subscribeMe"
									       value="1" <?php if ($this->config->subscriptionschecked == 1 && $me->canSubscribe != 0 || $this->config->subscriptionschecked == 0 && $me->canSubscribe == 1)
									{
										echo 'checked="checked"';
									} ?> />
									<label class="string optional"
									       for="subscribeMe"><?php echo Text::_('COM_KUNENA_POST_NOTIFIED'); ?></label>
								</div>
							</div>
						<?php endif; ?>

						<?php if ($this->message->userid): ?>
							<div class="control-group" id="kanynomous-check"
							     <?php if (!$this->category->allow_anonymous): ?>style="display:none;"<?php endif; ?>>
								<label class="control-label"><?php echo Text::_('COM_KUNENA_POST_AS_ANONYMOUS'); ?></label>
								<div class="controls">
									<input type="checkbox" id="kanonymous" name="anonymous"
									       value="1" <?php if ($this->post_anonymous)
									{
										echo 'checked="checked"';
									} ?> />
									<label for="kanonymous"><?php echo Text::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></label>
								</div>
							</div>
							<div class="clearfix"></div>
						<?php endif; ?>

						<?php if (!empty($this->captchaEnabled)): ?>
							<div class="control-group">
								<div class="controls">
									<?php echo $this->captchaDisplay; ?>
								</div>
							</div>
						<?php endif; ?>

					</fieldset>
				</div>
			</div>
			<div class="center">
				<?php if ($editor == 1)
					:
					?>
					<input type="submit" class="btn btn-success form-validate" name="submit"
					       value="<?php echo Text::_('COM_KUNENA_SUBMIT'); ?>"
					       title="<?php echo Text::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'); ?>"/>
				<?php else

					:
					?>
					<button id="form_submit_button" name="submit" type="submit" class="btn btn-success form-validate" tabindex="8">
						<?php echo KunenaIcons::save(); ?>
						<?php echo ' ' . Text::_('COM_KUNENA_SUBMIT') . ' '; ?>
					</button>
				<?php endif; ?>

				<button type="reset" class="btn btn-default" onclick="window.history.back();" tabindex="10">
					<?php echo KunenaIcons::delete(); ?>
					<?php echo ' ' . Text::_('COM_KUNENA_CANCEL') . ' '; ?>
				</button>
			</div>
			<?php
			if (!$this->message->name)
			{
				echo '<script type="text/javascript">document.postform.authorname.focus();</script>';
			}
			else
			{
				if (!$this->topic->subject)
				{
					if ($this->config->allow_change_subject)
					{
						echo '<script type="text/javascript">document.postform.subject.focus();</script>';
					}
				}
				else
				{
					echo '<script type="text/javascript">document.postform.message.focus();</script>';
				}
			}
			?>
			<div id="kattach-list"></div>
		</div>
	</form>
<?php
if ($this->config->showhistory && $this->topic->exists())
{
	echo $this->subRequest('Topic/Form/History', new \Joomla\Input\Input(array('id' => $this->topic->id)));
}
