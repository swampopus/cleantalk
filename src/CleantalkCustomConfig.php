<?php

class CleantalkCustomConfig
{
	// Exclude urls from spam_check. List them separated by commas
	public static $cleantalk_url_exclusions = '';

	//Excludes fields from filtering. List them separated by commas
	public static $cleantalk_fields_exclusions = '';

	//Enable spam_check only on specific URLs. List them seperated by commas. 'all' by default
	public static $cleantalk_url_checking = 'all';

	public static function get_url_exclusions()
	{
		return (!empty(self::$cleantalk_url_exclusions) ? explode(',', trim(self::$cleantalk_url_exclusions)) : null);
	}
	public static function get_fields_exclusions()
	{
		return (!empty(self::$cleantalk_fields_exclusions) ? explode(',', trim(self::$cleantalk_fields_exclusions)) : null);
	}
	public static function get_url_checking()
	{
		return (!empty(self::$cleantalk_url_checking) ? explode(',', trim(self::$cleantalk_url_checking)) : null);
	}
}