<?php

use Hercules\View;

class HercView_Settings extends View
{
	function __construct()
	{
		$this->directory  = dirname( __FILE__ );
		$this->name       = 'Hercules Sentiment';
		$this->type       = 'options_page';
		$this->class_name = __CLASS__;

		$this->IncludeBootstrap();

		parent::__construct();
	}
}