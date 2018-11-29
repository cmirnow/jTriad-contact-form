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
defined('_JEXEC') or die;
JPluginHelper::importPlugin('captcha');

/**
 * Helper for mod_contactform_masterpro
 *
 * @package     Joomla.Site
 * @subpackage  mod_contactform_masterpro
 */
class ModContactformMasterpro{

	/**
	* Check if input correct. Exit on error with json error (on ajax request) or header redirect.
	*
	* @param $input
	**/
	public static function checkInput($input, ModContactformMasterproMailSettings $mailSettings){
		$err = array();

		if(!self::checkLen($input->getString('name')))
			$err[1] = '1) Имя пустое или слишком короткое!';
		if(!self::checkLen($input->getString('phone')))
			$err[2] = '2) Поле телефон пустое или слишком короткое!';
		if(!self::checkLen($input->getString('city')))
			$err[4] = '4) Вы не выбрали город!';
		if(!self::checkLen($input->getString('vacancy')))
			$err[5] = '5) Вы не выбрали вакансию!';
		if(!self::checkLen($input->getString('message')))
			$err[6] = '6) Сообщение должно быть заполнено!';

		$res = JDispatcher::getInstance()->trigger('onCheckAnswer', $input->get('recaptcha_response_field'));
		if(!(isset($res[0]) and $res[0])){
			$err[7] = '7) Не корректный код подтверждения!';
		}

		if (!$mailSettings->getMailByCityAndVacancy($input->getString('city'), $input->getString('vacancy'))){
			$err[8] = "8) Вероятно в городе [{$input->getString('city')}] нет вакансии [{$input->getString('vacancy')}]";
		}

		if(count($err)){
			self::processAnswer($input->get('ajax', false), 1, array('strErr' => implode('<br />', $err), 'err' => $err));
		}
	}

	/**
	* Actually sent mail.
	* Perform appropriate answer, so enshure exit.
	**/
	public static function sendMail($input, ModContactformMasterproMailSettings $mailSettings, $params){
		self::checkInput($input, $mailSettings); // Exit happened on error

		$msg = 'Name:	' . $input->getString('name') . '<br />
IP:	' . $input->server->get('REMOTE_ADDR') . '<br /><br />

Основное сообщение:<br /><br />
' . nl2br($input->getString('message')) . '

' . '
Дополнительно:<br /><br />
' . nl2br($input->getString('message1')) . '

';
		jimport('phpmailer.phpmailer');
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->IsMail();

		$mail->AddReplyTo(JFactory::getApplication()->getCfg('mailfrom'));
		$mail->AddAddress($mailSettings->getMailByCityAndVacancy($input->getString('city'), $input->getString('vacancy')));
		$mail->AddAddress($params->get('email1'));
		$mail->AddAddress($params->get('email2'));
		$mail->AddAddress($params->get('email3'));
		$mail->SetFrom($input->getString('email'), $input->getString('name'));
		$mail->Subject = 'A new ' . mb_strtolower($input->getString('vacancy')) . ' for ' . $input->getString('city') . ' | contact form feedback';

		$mail->MsgHTML($msg);

		$res = $mail->Send();

		self::processAnswer($input->get('ajax', false), 0, array('sent' => 1, 'res' => $res, 'to' => $mailSettings->getMailByCityAndVacancy($input->getString('city'), $input->getString('vacancy'))));
	}

	/**
	* Check particular field not empty
	*
	* @param $var String
	* @param $minLen Integer
	**/
	public static function checkLen($var, $minLen = 2){
		return isset($var) && mb_strlen(strip_tags($var),"utf-8") > $minLen;
	}

	/**
	* Sent appropriate AJAX json answer or just redirect if non-ajax request to $input->server->get('HTTP_REFERER')
	* In any case exit called. So this method never return!
	*
	* @param $isAjax
	* @param $status Status of operation. 0 - OK, other code of errors.
	* @param $data Will be put in session var state and returned in json Data field
	**/
	public static function processAnswer($isAjax, $status, $data){
		$recaptcha = JDispatcher::getInstance()->trigger('onDisplay', array(null, 'dynamic_recaptcha_1', 'class=""'));
		$recaptchaHTML = isset($recaptcha[0]) ? $recaptcha[0] : '';

		if($isAjax){
			exit(json_encode(array('Error' => $status, 'Data' => $data, 'recaptchaHTML' => $recaptchaHTML)));
		}
		else if($input->server->getString('HTTP_REFERER')){
			$session->set('state', $data, SESSION_NAMESPACE);
			header('Location: ' . $input->server->getString('HTTP_REFERER'));
		}

		exit();
	}
}
