<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Site
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

/*
 * A light application to serve attachments to the users. Will only partially initialize Joomla to gain some speed.
 */

if (version_compare(PHP_VERSION, '7.2', '<'))
{
	die('Your host needs to use PHP 7.2 or higher to run this version of Joomla!');
}

/*
 * Constant that is checked in included files to prevent direct access.
 */
/**
 *
 */
define('_JEXEC', 1);

use Joomla\Application\Web\WebClient;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\Input\Input;
use Joomla\Registry\Registry;

// Set base directory. This should usually work even with symbolic linked Kunena.
/**
 *
 */
define('JPATH_BASE', dirname(dirname(dirname(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : __DIR__))));

// Define Joomla constants.
require_once JPATH_BASE . '/includes/defines.php';

// Installation check, and check on removal of the install directory.
if (!file_exists(JPATH_CONFIGURATION . '/configuration.php')
	|| (filesize(JPATH_CONFIGURATION . '/configuration.php') < 10)
)
{
	echo 'No configuration file found and no installation code available. Exiting...';

	exit;
}

// Kunena check.
if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_kunena/api.php'))
{
	echo 'Kunena Forum not installed. Exiting...';

	exit;
}

// System includes
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

require_once JPATH_BASE . '/includes/framework.php';

/**
 *  Kunena Application
 *
 * @package  Kunena
 *
 * @since    K2.0
 */
class KunenaApplication extends Joomla\CMS\Application\WebApplication
{
	/**
	 * @var string
	 * @since K2.0
	 */
	protected $_name = 'site';

	/**
	 * @var integer
	 * @since K2.0
	 */
	protected $_clientId = 0;

	/**
	 * @var array
	 * @since K2.0
	 */
	protected $userstate = array();

	/**
	 * @param   Input      $input   input
	 * @param   Registry   $config  config
	 * @param   WebClient  $client  client
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(Joomla\Input\Input $input = null, Joomla\Registry\Registry $config = null, Joomla\Application\Web\WebClient $client = null)
	{
		parent::__construct($input, $config, $client);

		// Load and set the dispatcher
		$this->loadDispatcher();

		// Register the application to FactoryF
		Factory::$application = $this;

		// Enable sessions by default.
		if (is_null($this->config->get('session')))
		{
			$this->config->set('session', true);
		}

		// Set the session default name.
		if (is_null($this->config->get('session_name')))
		{
			$this->config->set('session_name', 'site');
		}

		// Create the session if a session name is passed.
		if ($this->config->get('session') !== false)
		{
			$this->loadSession();

			// Register the session with Factory
			Factory::$session = $this->getSession();
		}
	}

	/**
	 * @param   Joomla\CMS\Session\Session  $session  session
	 *
	 * @return  $this
	 * @since   Kunena 6.0
	 */
	public function loadSession(Joomla\CMS\Session\Session $session = null)
	{
		if ($session !== null)
		{
			$this['session'] = $session;

			return $this;
		}

		// Generate a session name.
		$name = md5($this->get('secret') . $this->get('session_name', get_class($this)));

		// Calculate the session lifetime.
		$lifetime = (($this->get('lifetime')) ? $this->get('lifetime') * 60 : 900);

		// Get the session handler from the configuration.
		$handler = $this->get('session_handler', 'none');

		// Initialize the options for Session.
		$options = array(
			'name'   => $name,
			'expire' => $lifetime,
		);

		$session = Session::getInstance($handler, $options);
		$session->initialise($this->input, $this['dispatcher']);

		if ($session->getState() == 'expired')
		{
			$session->restart();
		}
		else
		{
			$session->start();
		}

		// Set the session object.
		$this['session'] = $session;

		return $this;
	}

	/**
	 * @return  void
	 * @since   Kunena
	 * @throws  null
	 */
	protected function doExecute()
	{
		// Handle SEF.
		$query    = $this->input->getString('query', 'foo');
		$segments = explode('/', $query);

		$segment = array_shift($segments);
		$this->input->set('id', (int) $segment);
		$segment = array_shift($segments);

		if ($segment == 'thumb')
		{
			$this->input->set('thumb', 1);
		}

		$this->input->set('format', 'raw');

		$controller = new ComponentKunenaControllerApplicationAttachmentDefaultDisplay;
		echo $controller->execute();
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function isSite()
	{
		return true;
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function isAdmin()
	{
		return false;
	}

	/**
	 * @param   bool  $params  params
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getTemplate($params = false)
	{
		return 'system';
	}

	/**
	 * @param   string   $name   name
	 * @param   boolean  $value  value
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function setUserState($name, $value)
	{
		$this->userstate[$name] = $value;
	}

	/**
	 * @param   string  $name     name
	 * @param   null    $default  default
	 *
	 * @return  null
	 * @since   Kunena 6.0
	 */
	public function getUserState($name, $default = null)
	{
		return isset($this->userstate[$name]) ? $this->userstate[$name] : $default;
	}
}

$app = new KunenaApplication;

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

try
{
	$app->execute();
}
catch (Exception $e)
{
	echo $e->getMessage();
}
