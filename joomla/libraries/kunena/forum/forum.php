<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

/**
 * class KunenaForum
 *
 * Main class for Kunena Forum which is always present if Kunena framework has been installed.
 *
 * This class can be used to detect and initialize Kunena framework and to make sure that your extension
 * is compatible with the current version.
 * @since Kunena
 */
abstract class KunenaForum
{
	/**
	 * @since Kunena
	 */
	const PUBLISHED = 0;
	/**
	 * @since Kunena
	 */
	const UNAPPROVED = 1;
	/**
	 * @since Kunena
	 */
	const DELETED = 2;
	/**
	 * @since Kunena
	 */
	const TOPIC_DELETED = 3;
	/**
	 * @since Kunena
	 */
	const TOPIC_CREATION = 4;
	/**
	 * @since Kunena
	 */
	const MODERATOR = 1;
	/**
	 * @since Kunena
	 */
	const ADMINISTRATOR = 2;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected static $version = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected static $version_major = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected static $version_date = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected static $version_name = false;

	/**
	 * Checks if Kunena Forum is safe to be used and online.
	 *
	 * It is a good practice to check if Kunena Forum is online before displaying
	 * forum content to the user. It's even more important if you allow user to post
	 * or manipulate forum! By following this practice administrator can have single
	 * point which he can use to be sure that nobody has access to any data inside
	 * his forum.
	 *
	 * Use case: Administrator is upgrading his forum to the next major version and wants
	 * to be sure that everything works before putting forum back to online. He logs in
	 * and can see everything. For everyone else no forum related information is shown.
	 *
	 * <code>
	 * // Check if Kunena Forum has been installed, online and compatible with your code
	 *    if (class_exists('KunenaForum') && KunenaForum::enabled() && KunenaForum::isCompatible('2.0.0')) {
	 *        // Initialize the framework (new in 2.0.0)
	 *        KunenaForum::setup();
	 *        // It's now safe to display something or to save Kunena objects
	 * }
	 * </code>
	 *
	 * @see   KunenaForum::installed()
	 * @see   KunenaForum::isCompatible()
	 * @see   KunenaForum::setup()
	 *
	 * @param   boolean $checkAdmin True if administrator is considered as a special case.
	 *
	 * @return boolean True if online.
	 * @throws Exception
	 * @since Kunena
	 */
	public static function enabled($checkAdmin = true)
	{
		if (!\Joomla\CMS\Component\ComponentHelper::isEnabled('com_kunena'))
		{
			return false;
		}

		$config = KunenaFactory::getConfig();

		return !$config->board_offline
			|| ($checkAdmin && self::installed() && KunenaUserHelper::getMyself()->isAdmin());
	}

	/**
	 * Check if Kunena Forum is safe to be used.
	 *
	 * If installer is running, it's unsafe to use our framework. Files may be currently replaced with
	 * new ones and the database structure might be inconsistent. Using forum during installation will
	 * likely cause fatal errors and data corruption if you attempt to update objects in the database.
	 *
	 * Always detect Kunena in your code before you start using the framework:
	 *
	 * <code>
	 *    // Check if Kunena Forum has been installed and compatible with your code
	 *    if (class_exists('KunenaForum') && KunenaForum::installed() && KunenaForum::isCompatible('2.0.0')) {
	 *        // Initialize the framework (new in 2.0.0)
	 *        KunenaForum::setup();
	 *        // Start using the framework
	 *    }
	 * </code>
	 *
	 * @see   KunenaForum::enabled()
	 * @see   KunenaForum::isCompatible()
	 * @see   KunenaForum::setup()
	 *
	 * @return boolean True if Kunena has been fully installed.
	 * @since Kunena
	 */
	public static function installed()
	{
		return !is_file(KPATH_ADMIN . '/install.php') || self::isDev();
	}

	/**
	 * Check if Kunena Forum is running from a Git repository.
	 *
	 * Developers tend to do their work directly in the Git repositories instead of
	 * creating and installing new builds after every change. This function can be
	 * used to check the condition and make sure we do not break users repository
	 * by replacing files during upgrade.
	 *
	 * @return boolean True if Git repository is detected.
	 * @since Kunena
	 */
	public static function isDev()
	{
		if ('5.1.17' == '@' . 'kunenaversion' . '@')
		{
			return true;
		}

		return false;
	}

	/**
	 * Initialize Kunena Framework.
	 *
	 * This function initializes Kunena Framework. Main purpose of this
	 * function right now is to make sure all the translations have been loaded,
	 * but later it may contain other initialization tasks.
	 *
	 * Following code gives an example how to create backwards compatible code.
	 * Normally I wouldn't bother supporting deprecated unstable releases.
	 *
	 * <code>
	 *    // We have already checked that Kunena 2.0+ has been installed and is online
	 *
	 *    if (KunenaForum::isCompatible('2.0.0')) {
	 *        KunenaForum::setup();
	 *    } else {
	 *        KunenaFactory::loadLanguage();
	 *    }
	 * </code>
	 *
	 * @see   KunenaForum::installed()
	 *
	 * Alternatively you could use method_exists() to check that the new API is in there.
	 *
	 * @since 2.0.0-BETA2
	 * @throws Exception
	 * @return void
	 */
	public static function setup()
	{
		$config = KunenaFactory::getConfig();

		// Load language file for libraries.
		KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');

		// Setup output caching.
		$cache = Factory::getCache('com_kunena', 'output');

		if (!$config->get('cache'))
		{
			$cache->setCaching(0);
		}

		$cache->setLifeTime($config->get('cache_time', 60));

		// Setup error logging.
		jimport('joomla.error.log');
		$options    = array('logger' => 'w3c', 'text_file' => 'kunena.php');
		$categories = array('kunena');
		$levels     = JDEBUG || $config->debug ? Log::ALL :
			Log::EMERGENCY | Log::ALERT | Log::CRITICAL | Log::ERROR;
		Log::addLogger($options, $levels, $categories);
	}

	/**
	 * Check if Kunena Forum is compatible with your code.
	 *
	 * This function can be used to make sure that user has installed Kunena version
	 * that has been tested to work with your extension. All existing functions should
	 * be backwards compatible, but each release can add some new functionality, which
	 * you may want to use.
	 *
	 * <code>
	 *    if (KunenaForum::isCompatible('2.0.1')) {
	 *        // We can do it in the new way
	 *    } else {
	 *        // Use the old code instead
	 *    }
	 * </code>
	 *
	 * @see   KunenaForum::installed()
	 *
	 * @param   string $version Minimum required version.
	 *
	 * @return boolean Yes, if it is safe to use Kunena Framework.
	 * @since Kunena
	 */
	public static function isCompatible($version)
	{
		// If requested version is smaller than 4.0, it's not compatible
		if (version_compare($version, '3.0', '<'))
		{
			return false;
		}

		// Development version support.
		if ($version == '5.1')
		{
			return true;
		}

		// Check if future version is needed (remove GIT and DEVn from the current version)
		if (version_compare($version, preg_replace('/(-DEV\d*)?(-GIT)?/i', '', self::version()), '>'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Returns the exact version from Kunena Forum.
	 *
	 * @return boolean Version number.
	 * @since Kunena
	 */
	public static function version()
	{
		if (self::$version === false)
		{
			self::buildVersion();
		}

		return self::$version;
	}

	/**
	 * @return void
	 *
	 * @since version
	 */
	protected static function buildVersion()
	{
		if ('5.1.17' == '@' . 'kunenaversion' . '@')
		{
			$file = JPATH_MANIFESTS . '/packages/pkg_kunena.xml';

			if (file_exists($file))
			{
				$manifest      = simplexml_load_file($file);
				self::$version = (string) $manifest->version . '-GIT';
			}
			else
			{
				self::$version = strtoupper('5.1.17');
			}
		}
		else
		{
			self::$version = strtoupper('5.1.17');
		}

		self::$version_major = substr(self::$version, 0, 3);
		self::$version_date  = ('2020-06-09' == '@' . 'kunenaversiondate' . '@') ? Factory::getDate()->format('Y-m-d') : '2020-06-09';
		self::$version_name  = ('Quaoar' == '@' . 'kunenaversionname' . '@') ? 'Git Repository' : 'Quaoar';
	}

	/**
	 * Returns all version information together.
	 *
	 * @return object stdClass containing (version, major, date, name).
	 * @since Kunena
	 */
	public static function getVersionInfo()
	{
		$version          = new stdClass;
		$version->version = self::version();
		$version->major   = self::versionMajor();
		$version->date    = self::versionDate();
		$version->name    = self::versionName();

		return $version;
	}

	/**
	 * Returns major version number (2.0, 3.0, 3.1 and so on).
	 *
	 * @return boolean Major version in xxx.yyy format.
	 * @since Kunena
	 */
	public static function versionMajor()
	{
		if (self::$version_major === false)
		{
			self::buildVersion();
		}

		return self::$version_major;
	}

	/**
	 * Returns build date from Kunena Forum (for Git today).
	 *
	 * @return boolean Date in yyyy-mm-dd format.
	 * @since Kunena
	 */
	public static function versionDate()
	{
		if (self::$version_date === false)
		{
			self::buildVersion();
		}

		return self::$version_date;
	}

	/**
	 * Returns codename from Kunena release.
	 *
	 * @return boolean Codename.
	 * @since Kunena
	 */
	public static function versionName()
	{
		if (self::$version_name === false)
		{
			self::buildVersion();
		}

		return self::$version_name;
	}

	/**
	 * Displays Kunena Forum view/layout inside your extension.
	 *
	 * <code>
	 *
	 * </code>
	 *
	 * @param   string          $viewName Name of the view.
	 * @param   string          $layout   Name of the layout.
	 * @param   null|string     $template Name of the template file.
	 * @param   array|JRegistry $params   Extra parameters to control the model.
	 *
	 * @throws Exception
	 * @since Kunena
	 * @return void
	 */
	public static function display($viewName, $layout = 'default', $template = null, $params = array())
	{
		// Filter input
		$viewName = preg_replace('/[^A-Z0-9_]/i', '', $viewName);
		$layout   = preg_replace('/[^A-Z0-9_]/i', '', $layout);
		$template = preg_replace('/[^A-Z0-9_]/i', '', $template);
		$template = $template ? $template : null;

		$view  = "KunenaView{$viewName}";
		$model = "KunenaModel{$viewName}";

		// Load potentially needed language files
		KunenaFactory::loadLanguage();
		KunenaFactory::loadLanguage('com_kunena.model');
		KunenaFactory::loadLanguage('com_kunena.view');

		require_once KPATH_SITE . '/views/common/view.html.php';
		require_once KPATH_SITE . '/models/common.php';

		if (!class_exists($view))
		{
			$vpath = KPATH_SITE . '/views/' . $viewName . '/view.html.php';

			if (!is_file($vpath))
			{
				return;
			}

			require_once $vpath;
		}

		if ($viewName != 'common' && !class_exists($model))
		{
			$mpath = KPATH_SITE . '/models/' . $viewName . '.php';

			if (!is_file($mpath))
			{
				return;
			}

			require_once $mpath;
		}

		$view = new $view(array('base_path' => KPATH_SITE));

		// @var KunenaView $view

		if ($params instanceof \Joomla\Registry\Registry)
		{
			// Do nothing
		}
		else
		{
			$params = new \Joomla\Registry\Registry($params);
		}

		$params->set('layout', $layout);

		// Push the model into the view (as default).
		$model = new $model;

		// @var KunenaModel $model

		$model->initialize($params);
		$view->setModel($model, true);

		// Add template path
		if ($params->get('templatepath'))
		{
			$view->addTemplatePath($params->get('templatepath'));
		}

		if ($viewName != 'common')
		{
			$view->common           = new KunenaViewCommon(array('base_path' => KPATH_SITE));
			$view->common->embedded = true;
		}

		// Flag view as being embedded
		$view->embedded = true;

		// Flag view as being teaser
		$view->teaser = $params->get('teaser', 0);

		// Render the view.
		$view->displayLayout($layout, $template);
	}
}
