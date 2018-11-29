<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_contactform_masterpro
 *
 * @copyright   Alexey Smirnov <info@masterpro.ws>
 * @created 23.06.2014 19:46:04
 *
 * @license GNU GPL 2.0 (http://www.gnu.org/licenses/gpl-2.0.html)
 **/

defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/ModContactformMasterproMailSettings.class.php';

define('SESSION_NAMESPACE', $module->module);

$document = JFactory::getDocument();
$session = JFactory::getSession();
$input = JFactory::getApplication()->input;

$mailSettings = new ModContactformMasterproMailSettings($params->get('textarea'));

if ($input->getString('message') or $input->getString('city') or $input->getString('vacancy') or $input->getString('subject')){
	ModContactformMasterpro::sendMail($input, $mailSettings, $params); // Answer also will be sent
}

///////////////////////////////////////////////

$errorMessage = '';
$sState = $session->get('state', '', SESSION_NAMESPACE);
if(isset($sState['errStr']) and $sState['errStr']) {
	$errorMessage = $sState['errStr'];
	$session->clear('state', SESSION_NAMESPACE);
}

$successMessage = '';
if(isset($sState['sent']) and $sState['sent']) {
	$successMessage = '<h1>Thank you!</h1>';
	$css = '<style type="text/css">#contact-form{display:none;}</style>';
	$session->clear('sent', SESSION_NAMESPACE);
}

JPluginHelper::importPlugin('captcha');
$dispatcher = JEventDispatcher::getInstance();
$dispatcher->trigger('onInit','dynamic_recaptcha_1');

$document->addStyleSheet(JURI::base() . 'modules/mod_contactform_masterpro/assets/jqtransformplugin/jqtransform.css');
$document->addStyleSheet(JURI::base() . 'modules/mod_contactform_masterpro/assets/formValidator/validationEngine.jquery.css');
$document->addStyleSheet(JURI::base() . 'modules/mod_contactform_masterpro/assets/css/styles.css');

$document->addScript(JURI::root() . 'modules/mod_contactform_masterpro/assets/jqtransformplugin/jquery.jqtransform.js');
$document->addScript(JURI::root() . 'modules/mod_contactform_masterpro/assets/formValidator/jquery.validationEngine.js');
$document->addScript(JURI::root() . 'modules/mod_contactform_masterpro/assets/js/script.js');

require JModuleHelper::getLayoutPath('mod_contactform_masterpro', $params->get('layout', 'default'));
