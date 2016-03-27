<?php namespace Hercules;

abstract class HercAbstract
{
    function __construct()
    {

    }

    /**
     * Returns an instance of a helper object.
     * @param String $helper Name of the helper to instantiate.
     * @param bool|false $new Set to true to return a new instance and not use a previous instance.
     * @return mixed instance of the class that was previously created, or a new one if need be.
     */
    function Helper( $helper, $new = false )
    {
        return $this->InitiateClass( 'helper', $helper, $new );
    }

    /**
     * Returns an instance of a model object.
     * @param String $model Name of the model to instantiate.
     * @param bool|false $new Set to true to return a new instance and not use a previous instance.
     * @return mixed instance of the class that was previously created, or a new one if need be.
     */
    function Model( $model, $new = false )
    {
        return $this->InitiateClass( 'model', $model, $new );
    }

    /**
     * Returns an instance of a controller object.
     * @param String $controller Name of the controller to instantiate.
     * @param bool|false $new Set to true to return a new instance and not use a previous instance.
     * @return mixed instance of the class that was previously created, or a new one if need be.
     */
    function Controller( $controller, $new = false )
    {
        return $this->InitiateClass( 'controller', $controller, $new );
    }

    /**
     * Returns an instance of a view object.
     * @param String $view Name of the view to instantiate.
     * @param bool|false $new Set to true to return a new instance and not use a previous instance.
     * @return mixed instance of the class that was previously created, or a new one if need be.
     */
    function View( $view, $new = false )
    {
        return $this->InitiateClass( 'view', $view, $new );
    }

	/**
	 * Turns a string into Upper Camelcase.
	 * @param $string string to upper cammel case.
	 * @return string upper camel-cased version of input string.
	 */
    function UpperCamelCaseIt( $string )
    {
        return str_replace( ' ', '', ucwords( str_replace( array( '_', '-' ), ' ', $string ) ) );
    }

	/**
	 * Turns an upper camel-cased string into a slug with lowercase letters and hyphens for word splits.
	 * @param $string Upper camel-cased string to turn into a slug.
	 * @return string slugified version of the upper camel case input string.
	 */
    function SlugifyCamelCase( $string )
    {
        $chars = str_split( $string );

        foreach( $chars as $key=>$val )
        {
            if( $val !== strtolower( $val ) )
                $chars[ $key ] = '-' . strtolower( $val );
        }

        return trim( implode( '', $chars ), '-' );
    }

	/**
	 * Takes a class name from a class that uses this framework and turns it into a slug.
	 * @param $string Class name that is of the type HercView_something, HercModel_something, HercController_something, or HercHelper_something.
	 * @return bool|string slugified version of the class name if there was one, otherwise returns false.
	 */
    function SlugFromClassName( $string )
    {
        $bits = explode( '_', $string );

        if( !empty( $bits[1] ) )
            return $this->SlugifyCamelCase( $bits[1] );

        return false;
    }

	/**
	 * Returns the slug for the current class instance.
	 *
	 * @return bool|string Slugified version of the class name if it knows the class, otherwise false.
	 */
    function CurrentSlug()
    {
        return $this->SlugFromClassName( $this->class_name );
    }

    /**
     * Returns an instance of a class based on what type it is and what its slug is.
     * @param String $type This can be helper, model, controller, or view depending on what kind of object you are looking for.
     * @param String $class This is the slug, basically the part before the ".php" in the file name.
     * @param bool|false $new Set to true to return a new instance and not use a previous instance.
     * @return mixed instance of the class that was previously created, or a new one if need be.
     */
    function InitiateClass( $type, $class, $new = false )
    {
        $class = strtolower( $class );

        switch( $type )
        {
            case 'helper':
                $folder = 'helpers';
                $class_prefix = 'Helper';
                break;
            case 'model':
                $folder = 'models';
                $class_prefix = 'Model';
                break;
            case 'controller':
                $folder = 'controllers';
                $class_prefix = 'Controller';
                break;
            case 'view':
                $folder = 'views';
                $class_prefix = 'View';
                break;
        }

        if( !property_exists( $this, 'plugin_directory' ) && property_exists( $this, 'directory' ) )
            $this->plugin_directory = dirname( dirname( $this->directory ) );

        if( property_exists( $this, 'plugin_directory' ) && file_exists( $this->plugin_directory . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . $class . '.php' ) )
            require_once( $this->plugin_directory . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . $class . '.php' );
        elseif( file_exists( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . $class . '.php' ) )
		{
			require_once( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . $class . '.php' );
			$framework_object = true;
		}
        else
            return false;

		if( empty( $framework_object ) )
        	$object = 'Herc' . $class_prefix . '_' . $this->UpperCamelCaseIt( $class );
		else
			$object = 'Hercules\\' . $class_prefix . '\\' . $this->UpperCamelCaseIt( $class );

        if( $new )
            return new $object;

        if( empty( $this->$object ) )
            $this->$object = new $object;

        return $this->$object;
    }

	/**
	 * Returns the url to a specific file.
	 * @param string $file
	 * @return string url to the file supplied.
	 */
    function GetUrl( $file = '' )
    {
        return plugins_url( $this->GetPluginFolderName() . '/' . $file, $this->GetPluginDirectory() );
    }

	/**
	 * Returns the path to the main plugin folder for this plugin.
	 *
	 * @return string path to the plugin folder.
	 */
    function GetPluginDirectory()
    {
        return dirname( dirname( dirname( __FILE__ ) ) );
    }

	/**
	 * Returns the path to the Hercules Framework.
	 *
	 * @return string path to the framework parent folder.
	 */
	function GetFrameworkDirectory()
	{
		return dirname( dirname( __FILE__ ) );
	}

	/**
	 * Returns the folder name for the plugin folder for this plugin.
	 *
	 * @return string folder name for this plugin.
	 */
    function GetPluginFolderName()
    {
        $plugin_folder = $this->GetPluginDirectory();
        $folder_bits = explode( DIRECTORY_SEPARATOR, $plugin_folder );

        return array_pop( $folder_bits );
    }

	/**
	 * Returns the folder name for the framework folder.
	 *
	 * @return string folder name for this framework.
	 */
	function GetFrameworkFolderName()
	{
		$plugin_folder = $this->GetFrameworkDirectory();
		$folder_bits = explode( DIRECTORY_SEPARATOR, $plugin_folder );

		return array_pop( $folder_bits );
	}

	/**
	 * Returns the current page url based on / instead of having the domain.
	 * @return string
	 */
	function GetCurrentPage()
	{
		$current_page = $_SERVER['PHP_SELF'];

		if( !empty( $_GET ) )
		{
			$current_page .= '?';

			foreach( $_GET as $key => $val )
			{
				$current_page .= $key . '=' . urlencode( $val );
			}
		}

		return $current_page;
	}

	/**
	 * Adds data to the data array if it existed, otherwise makes a new data array from the input value.
	 *
	 * @param array $data array of data to add to the current data array.
	 */
	function AddToData( $data )
	{
		if( empty( $this->data ) )
			$this->data = array();

		if( !is_array( $this->data ) )
			$this->data = array( $this->data );

		if( is_array( $this->data ) && is_array( $data ) && !empty( $data ) )
			$this->data = array_merge( $this->data, $data );
	}
}