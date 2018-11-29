<?
/**
 * @package     Joomla.Site
 * @subpackage  mod_contactform_masterpro
 *
 * @copyright   Alexey Smirnov <info@masterpro.ws>
 * @created 23.06.2014 19:46:04
 * 
 * @license GNU GPL 2.0 (http://www.gnu.org/licenses/gpl-2.0.html)
**/

/**
* Class for handle sending parameters dependent on City and Vacancies administration settings.
* In database stored as text (one field) like:
* Москва + плотник + mail1@mail.msk
* Москва + дизайнер + mail1@mail.msk
* Питер + слесарь + mail2@mail.spb
* Москва + сантехник + mail1@mail.msk
* Питер + маляр + mail1@mail.spb
* Питер + лодырь + mail2@mail.spb
* Москва + слесарь + mail2@mail.msk
* Питер + сантехник + mail1@mail.spb
* Питер + садовник + mail2@mail.spb
**/
defined('_JEXEC') or die('Restricted access');
class ModContactformMasterproMailSettings{
const DELIMETER_LINES = "\n";
const DELIMITER_FIELDS = ' + ';

private $_text;
private $arr;

	public function __construct($text){
		$this->_text = $text;
		$this->parse();
	}

	private function parse(){
		foreach(explode(self::DELIMETER_LINES, $this->_text) as $k => $v){
			$a = explode(self::DELIMITER_FIELDS, $v);
			$this->arr[trim($a[0])][trim($a[1])] = trim($a[2]);
		}
	}

	public function getAllCyties(){
		return array_keys($this->arr);
	}

	/**
	* Returns array of all possible vacancies in all cities.
	*
	* @return One dimentional array.
	*/
	public function getAllVacancies(){
		return array_unique(
			call_user_func_array(
				'array_merge'
				,array_map(
					create_function('$x', 'return array_keys($x);')
					,array_values($this->arr)
				)
			)
		);
	}

	/**
	* Array of cities which are the arrays ov vacancies in it.
	**/
	public function getAllVacanciesByCity(){
		return array_map(
			function($a){
				return array_keys($a);
			}
			, $this->arr
		);
	}

	public function getMailByCityAndVacancy($city, $vacancy){
		return isset($this->arr[trim($city)][trim($vacancy)]) ? $this->arr[trim($city)][trim($vacancy)] : null;
	}
}
?>
