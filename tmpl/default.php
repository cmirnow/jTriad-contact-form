<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_contactform_masterpro
 *
 * @copyright   Alexey Smirnov <info@masterpro.ws>
 *
 * @license GNU GPL 2.0 (http://www.gnu.org/licenses/gpl-2.0.html)
 */
defined('_JEXEC') or die;

$session->clear('name', SESSION_NAMESPACE);
$session->clear('email', SESSION_NAMESPACE);

$oldData = $session->get('postTwergewrtgert', '', SESSION_NAMESPACE);
?>
<?=@$css?>

<div id="main-container">

<div id="form-container">
	<form id="contact-form" name="contact-form" method="post">
		<table width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td width="15%"><label for="name"><? echo $params->get('name'); ?></label></td>
				<td width="70%"><input type="text" class="validate[required,custom[onlyLetter]]" name="name" id="name" value="<?=htmlentities(@$oldData['name'], ENT_QUOTES, "UTF-8")?>" /></td>
				<td width="15%" id="errOffset">&nbsp;</td>
			</tr>
			<tr>
				<td><label for="city"><? echo $params->get('your_city'); ?></label></td>
				<td>
					<select name="city" id="city" class="validate[required]">
						<option value="" selected="selected"> - - -</option>
						<?
							foreach($mailSettings->getAllCyties() as $city){
								echo "<option value='" . htmlentities(trim($city), ENT_QUOTES, "UTF-8") . "'>$city</option>";
							}
						?>
					</select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><label for="phone"><? echo $params->get('mobile_phone'); ?></label></td>
				<td><input type="text" class="validate[required,custom[telephone]]" name="phone" id="phone" value="<?=htmlentities(@$oldData['phone'], ENT_QUOTES, "UTF-8")?>" /></td>
				<td>&nbsp;</td>
			<tr>
				<td><label for="vacancy"><? echo $params->get('job_vacancy'); ?></label></td>
				<td>
					<select name="vacancy" id="vacancy" class="validate[required]">
						<option value="" selected="selected"> - - -</option>
						<?
							foreach($mailSettings->getAllVacancies() as $vacancy){
								echo "<option value='" . htmlentities(trim($vacancy), ENT_QUOTES, "UTF-8") . "'>$vacancy</option>";
							}
						?>
					</select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td valign="top"><label for="message"><? echo $params->get('experience'); ?></label></td>
				<td><textarea name="message" id="message" class="validate[required]" cols="30" rows="7"><?=htmlentities(@$oldData['message'], ENT_QUOTES, "UTF-8")?></textarea></td>
				<td valign="top">&nbsp;</td>
			</tr>
			<tr>
				<td valign="top"><label for="message1"><? echo $params->get('additionally'); ?></label></td>
				<td><textarea name="message1" id="message1" cols="30" rows="7"><?=htmlentities(@$oldData['message1'], ENT_QUOTES, "UTF-8")?></textarea></td>
				<td valign="top">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3"><?
				if(JPluginHelper::isEnabled('captcha', 'recaptcha')) {
					$recaptcha = $dispatcher->trigger('onDisplay', array(null, 'dynamic_recaptcha_1', 'class=""'));
					echo (isset($recaptcha[0])) ? $recaptcha[0] : ''; }
					else echo '<p><img src="modules/mod_contactform_masterpro/img/recaptcha.jpg" /></p>';
				?>
				</td>
			</tr>
			<tr>
				<td valign="top">&nbsp;</td>
				<td colspan="2"><input type="submit" name="button" id="button" value="Отправить" />
					<input type="reset" name="button2" id="button2" value="Сброс" />
					<img id="loading" src="modules/mod_contactform_masterpro/img/ajax-load.gif" width="16" height="16" alt="loading" />
				</td>
			</tr>
		</table>
		<div class="formError"></div>
		<div class="error" id="errorMessage"><?=$errorMessage?></div>
	</form>
	<?=$successMessage?>
</div>
