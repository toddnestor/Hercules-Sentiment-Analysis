<?php

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'php-insight' . DIRECTORY_SEPARATOR . 'autoload.php' );

use PHPInsight\Sentiment;

use Hercules\Helper;

class HercHelper_Phpinsight extends Helper
{
	function __construct()
	{
		if( empty( $this->sentiment ) )
		{
			$this->sentiment = new Sentiment;
		}
	}
}