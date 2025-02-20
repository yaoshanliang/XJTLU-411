<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

KT::import('admin:/includes/model');

class KomentoModelSystem extends KomentoModel
{
	public $_total = null;
	public $_pagination	= null;
	public $_data = null;

	public function __construct()
	{
		parent::__construct('system');

		$mainframe = JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest( 'com_komento.subscribers.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( 'com_komento.subscribers.limitstart', 'limitstart', 0, 'int' );
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Upgrades component to the latest version
	 *
	 * @since	3.1.3
	 * @access	public
	 */
	public function update()
	{
		$config = KT::config();

		// Get the updater URL
		$uri = $this->getUpdateUrl();
		$key = $config->get('main_apikey');
		$domain = str_ireplace(array('http://', 'https://'), '', rtrim(JURI::root(), '/'));

		$uri->setVar('from', KT::getLocalVersion());
		$uri->setVar('key', $key);
		$uri->setVar('domain', $domain);
		$url = $uri->toString();
	
		// Download the package
		$file = JInstallerHelper::downloadPackage($url);

		// Error downloading the package
		if (!$file) {
			$this->setError('Error downloading zip file. Please try again. If the problem still persists, please get in touch with our support team.');
			return false;
		}
		
		$jConfig = KT::jconfig();
		$temporaryPath = $jConfig->get('tmp_path');

		// Ensure that the temporary path exists as some site owners
		// may migrate their site into a different environment
		if (!JFolder::exists($temporaryPath)) {
			$this->setError('Temporary folder set in Joomla does not exists. Please check the temporary folder path in your Joomla Global Configuration section.');
			return false;
		}

		// Unpack the downloaded zip into the temporary location
		$package = JInstallerHelper::unpack($temporaryPath . '/' . $file);

		$installer = JInstaller::getInstance();
		$state = $installer->update($package['dir']);

		if (!$state) {
			$this->setError('Error updating component when using the API from Joomla. Please try again.');
			return false;
		}

		// Clean up the installer
		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		return true;
	}

	/**
	 * Retrieves the latest installable version
	 *
	 * @since	3.1.3
	 * @access	public
	 */
	public function getUpdateUrl()
	{
		$adapter = KT::connector();
		$adapter->addUrl(KOMENTO_JUPDATE_SERVICE);
		$adapter->execute();

		$result = $adapter->getResult(KOMENTO_JUPDATE_SERVICE);

		if (!$result) {
			throw new Exception('Unable to connect to remote service to obtain package. Please contact our support team');
		}

		$parser = KT::getXML($result, false);

		$url = (string) $parser->update->downloads->downloadurl;

		$uri = new JURI($url);
		return $uri;
	}
}
