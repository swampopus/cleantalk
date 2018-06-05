<?php

class CleantalkCustomConfig
{
	// Exclude urls from spam_check. List them separated by commas
	private $cleantalk_url_exclusions = '';

	//Excludes fields from filtering. List them separated by commas
	private $cleantalk_fields_exclusions = '';

	function __construct()
	{
		$this->cleantalk_url_exclusions = (!empty($this->cleantalk_url_exclusions) ? explode(',', trim($this->cleantalk_url_exclusions)) : null);	
		$this->cleantalk_fields_exclusions = (!empty($this->cleantalk_fields_exclusions) ? explode(',', trim($this->cleantalk_fields_exclusions)) : null);
	}
	public function get_url_exclusions()
	{
		return $this->cleantalk_url_exclusions;
	}
	public function get_fields_exclusions()
	{
		return $this->cleantalk_fields_exclusions;
	}
}
