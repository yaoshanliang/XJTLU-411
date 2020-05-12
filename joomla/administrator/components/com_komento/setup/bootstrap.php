<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

// Get application
$app = JFactory::getApplication();
$input = $app->input;
$input->set('tmpl', 'component');

$reinstall = $input->get('reinstall', false, 'bool') || $input->get('install', false, 'bool');
$update = $input->get('update', false, 'bool');

############################################################
#### Constants
############################################################
$path = dirname(__FILE__);

define('KT_IDENTIFIER', 'com_komento');
define('KT_PACKAGES', $path . '/packages');
define('KT_CONFIG', $path . '/config');
define('KT_THEMES', $path . '/themes');
define('KT_CONTROLLERS', $path . '/controllers');
define('KT_SERVER', 'https://stackideas.com');
define('KT_VERIFIER', 'https://stackideas.com/updater/verify');
define('KT_MANIFEST', 'https://stackideas.com/updater/manifests/komento');
define('KT_SETUP_URL', JURI::base() . 'components/' . KT_IDENTIFIER . '/setup');
define('KT_TMP', $path . '/tmp');
define('KT_BETA', false);
define('KT_KEY', '98736848dae87499e06369c9d7c7ae99');
define('KT_INSTALLER', 'full');

// Only when ED_PACKAGE is running on full package, the ED_PACKAGE should contain the zip's filename
define('KT_PACKAGE', 'com_komento_3.1.3_component_pro.zip');

// Get the current version
$contents = JFile::read(JPATH_ROOT. '/administrator/components/com_komento/komento.xml');
$parser = simplexml_load_string($contents);

$version = $parser->xpath('version');
$version = (string) $version[0];

define('KT_HASH', md5($version));

// If this is in developer mode, we need to set the session
$developer = $input->get('developer', false, 'bool');

if ($developer) {
	$session = JFactory::getSession();
	$session->set('komento.developer', true);
}

if (!function_exists('dump')) {

	function isDevelopment()
	{
		$session = JFactory::getSession();
		$developer = $session->get('komento.developer');

		return $developer;
	}

	function dump()
	{
		$args = func_get_args();

		echo '<pre>';
		
		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo '</pre>';

		exit;
	}
}

############################################################
#### Process controller
############################################################
$controller = $input->get('controller', '', 'cmd');
$task = $input->get('task', '');

if (!empty($controller)) {

	$file = strtolower($controller) . '.' . strtolower($task) . '.php';
	$file = KT_CONTROLLERS . '/' . $file;

	require_once($file);

	$className = 'KomentoController' . ucfirst($controller) . ucfirst($task);
	$controller = new $className();
	return $controller->execute();
}


############################################################
#### Initialization
############################################################
$contents = JFile::read(KT_CONFIG . '/install.json');
$steps = json_decode($contents);

############################################################
#### Workflow
############################################################
$active = $input->get('active', 0, 'default');

if ($active === 'complete') {
	$activeStep = new stdClass();

	$activeStep->title = JText::_('Installation Completed');
	$activeStep->template = 'complete';

	// Assign class names to the step items.
	if ($steps) {
		foreach ($steps as $step) {
			$step->className = ' done';
		}
	}
} else {

	if ($active == 0) {
		$active = 1;
		$stepIndex = 0;
	} else {
		$active += 1;
		$stepIndex = $active - 1;
	}

	// Get the active step object.
	$activeStep = $steps[$stepIndex];

	// Assign class names to the step items.
	foreach ($steps as $step) {
		$step->className = $step->index == $active || $step->index < $active ? ' current' : '';
		$step->className .= $step->index < $active ? ' done' : '';
	}
}

require(KT_THEMES . '/default.php');