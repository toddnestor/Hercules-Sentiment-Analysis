<?php

use Hercules\Model;

class HercModel_Settings extends Model
{
    function __construct()
    {
        $this->class_name = __CLASS__;
        $this->directory = dirname( __FILE__ );

        parent::__construct();
    }
}