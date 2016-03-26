<?php

use Hercules\View;

class HercView_Settings extends View
{
	function __construct()
	{
		$this->directory  = dirname( __FILE__ );
		$this->name       = 'Herc Sentiment';
		$this->type       = 'admin_page';
		$this->class_name = __CLASS__;
		$this->icon 	  = 'dashicons-format-status';
		$this->priority   = 5;

		//Bootstrap makes things awesome!
		$this->IncludeBootstrap();

		parent::__construct();
	}
}