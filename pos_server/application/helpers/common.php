<?php
/**
 * PHP Plurk API Common Lib.
 *
 * @package   php-plurk-api
 * @category  API
 * @version   php-plurk-api 1.3
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link      http://code.google.com/p/php-plurk-api
 *
 */
Class common {


	function __consturct() {}

	function __deconstruct() {}


	/**
	 * function get_permalink
	 * transfer plurk_id to permalink
	 *
	 * @param $plurk_id
	 * @return string.
	 */
	function get_permalink($plurk_id)
	{
		return "http://www.plurk.com/p/" . base_convert($plurk_id, 10, 36);
	}

	/**
	 * function get_plurk_id
	 * transfer permalink to plurk_id
	 *
	 * @param $permalink
	 * @return int.
	 */
	function get_plurk_id($permalink)
	{
		return base_convert(str_replace('http://www.plurk.com/p/', '', $permalink), 36, 10);
	}

	/**
	 * funciton log
	 * message log
	 *
	 * @param $message
	 */
	function log($message = '')
	{
		if(!file_exists(PLURK_LOG_PATH)) 
		{
			touch(PLURK_LOG_PATH);
		}	
		$source = file_get_contents(PLURK_LOG_PATH);
		$source .= date("Y-m-d H:i:s - ") . $message . "\n";
		file_put_contents(PLURK_LOG_PATH, $source);
	}
}
