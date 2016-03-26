<?php
/**
 * Created by PhpStorm.
 * User: Todd
 * Date: 7/21/2015
 * Time: 6:00 PM
 */

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Handlebars' . DIRECTORY_SEPARATOR . 'Autoloader.php' );

Handlebars\Autoloader::register();

use Handlebars\Handlebars;

use Hercules\Helper;

class HercHelper_Handlebars extends Helper
{
    function __construct()
    {

    }

    /**
     * Returns an instance of the Handlebars class.
     *
     * Initiates an instance of the Handlebars class if need be and returns it, or returns a previously
     * instantiated Handlebars object.
     *
     * @return Handlebars Instance of the Handlebars class
     */
    function Initialize()
    {
        if( empty( $this->handlebars_object ) )
        {
            $this->handlebars_object = new Handlebars;
        }

        return $this->handlebars_object;
    }

    /**
     * Returns a compiled template using the Handlebars class.
     * @param String $template A Handlebars template.
     * @param Array $data Array of data to be used by Handlebars to render the template.
     * @return String Compiled Handlebars template.
     */
    function Render( $template, $data=array() )
    {
        return $this->Initialize()->render(
            $template,
            $data
        );
    }
}