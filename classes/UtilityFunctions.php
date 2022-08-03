<?php
/**
 *  Base static fucntions that are regularly used across all code.
 */

class UtilityFunctions {

	private static function getDomainPath(){
		$urlDomain = 'http://';
		if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $urlDomain = 'https://';
		$urlDomain .= $_SERVER['SERVER_NAME'];
		if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) $urlDomain .= ':'.$_SERVER['SERVER_PORT'];
		return $urlDomain;
	}

}
