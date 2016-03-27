<?php namespace Hercules;

class Framework extends HercAbstract
{
    function __construct()
    {

    }

    /**
     * This function goes through all the objects in the framework and the main plugin it is used in and creates an instance of each one.
     *
     * This function ensures any code that needs to be run at initiation gets run.  So you can do an add_action or
     * add_filter in the constructor of any object.
     */
    function InitiateAll()
    {
        $object_types = array(
            'helper',
            'model',
            'controller',
            'view'
        );

        foreach( $object_types as $key=>$val )
        {
            $directories = array(
                dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . $val . 's',
				$this->plugin_directory . DIRECTORY_SEPARATOR . $val . 's'
            );

            foreach( $directories as $key2=>$val2 )
            {
                if( is_dir( $val2 ) )
                {
                    $files = scandir( $val2 );

                    foreach( $files as $key3=>$val3 )
                    {
                        if( strpos( $val3, '.' ) === false )
                        {
                            $object = $this->InitiateClass( $val, $val3 );

                            if( method_exists( $object, 'Initialize' ) )
                                $object->Initialize();
                        }
                    }
                }
            }
        }
    }
}