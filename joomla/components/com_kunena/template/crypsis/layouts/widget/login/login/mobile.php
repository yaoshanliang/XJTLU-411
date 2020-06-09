<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<ul class="nav pull-right">
	<li class="dropdown mobile-user">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" id="klogin-mobile">
			<?php echo KunenaIcons::user(); ?>
			<span class="login-text"><?php echo Text::_('JLOGIN'); ?></span>
			<b class="caret"></b>
		</a>

		<div class="dropdown-menu" id="userdropdown">
			<form action="<?php echo Route::_('index.php?option=com_kunena'); ?>" method="post" class="form-inline">
				<input type="hidden" name="view" value="user"/>
				<input type="hidden" name="task" value="login"/>
				<?php echo HTMLHelper::_('form.token'); ?>

				<div id="kmobile-form-login-username" class="control-group center">
					<div class="controls">
						<div class="input-prepend input-append">
							<span class="add-on">
								<?php echo KunenaIcons::user(); ?>
								<label for="kmobile-username" class="element-invisible">
									<?php echo Text::_('JGLOBAL_USERNAME'); ?>
								</label>
							</span>
							<input id="kmobile-username" type="text" name="username" class="input-small" tabindex="1"
							       size="18" autocomplete="username" placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>"/>
						</div>
					</div>
				</div>

				<div id="kmobile-form-login-password" class="control-group center">
					<div class="controls">
						<div class="input-prepend input-append">
							<span class="add-on">
								<?php echo KunenaIcons::lock(); ?>
								<label for="kmobile-passwd" class="element-invisible">
									<?php echo Text::_('JGLOBAL_PASSWORD'); ?>
								</label>
							</span>
							<input id="kmobile-passwd" type="password" name="password" class="input-small" tabindex="2"
							       size="18" autocomplete="current-password" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>"/>
						</div>
					</div>
				</div>

				<?php $login = KunenaLogin::getInstance(); ?>
				<?php
				if ($login->getTwoFactorMethods() > 1)
					:
					?>
					<div id="kmobile-form-login-tfa" class="control-group center">
						<div class="controls">
							<div class="input-prepend input-append">
							<span class="add-on">
								<?php echo KunenaIcons::star(); ?>
								<label for="kmobile-secretkey" class="element-invisible">
									<?php echo Text::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>
								</label>
						  </span>
								<input id="kmobile-secretkey" type="text" name="secretkey" class="input-small"
								       tabindex="3"
								       size="18" placeholder="<?php echo Text::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>"/>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($this->rememberMe)
					:
					?>
					<div id="kmobile-form-login-remember" class="control-group center">
						<div class="controls">
							<div class="input-prepend input-append">
								<div class="add-on">
									<input id="kmobile-remember" type="checkbox" name="remember" class="inputbox"
									       value="yes"/>
									<label for="kmobile-remember" class="control-label">
										<?php echo Text::_('JGLOBAL_REMEMBER_ME'); ?>
									</label>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<div id="kmobile-form-login-submit" class="control-group center">
					<p>
						<button type="submit" tabindex="3" name="submit" class="btn btn-primary">
							<?php echo Text::_('JLOGIN'); ?>
						</button>
					</p>

					<p>
						<?php if ($this->resetPasswordUrl)
							:
							?>
							<a href="<?php echo $this->resetPasswordUrl; ?>">
								<?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD'); ?>
							</a>
							<br/>
						<?php endif ?>

						<?php if ($this->remindUsernameUrl)
							:
							?>
							<a href="<?php echo $this->remindUsernameUrl; ?>">
								<?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME'); ?>
							</a>
							<br/>
						<?php endif ?>

						<?php if ($this->registrationUrl)
							:
							?>
							<a href="<?php echo $this->registrationUrl; ?>">
								<?php echo Text::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT'); ?>
							</a>
						<?php endif ?>

					</p>
				</div>
			</form>
			<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_login'); ?>
		</div>
	</li>
</ul>
