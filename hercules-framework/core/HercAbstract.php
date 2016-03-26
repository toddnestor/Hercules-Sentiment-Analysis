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

    function UpperCamelCaseIt( $string )
    {
        return str_replace( ' ', '', ucwords( str_replace( array( '_', '-' ), ' ', $string ) ) );
    }

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

    function SlugFromClassName( $string )
    {
        $bits = explode( '_', $string );

        if( !empty( $bits[1] ) )
            return $this->SlugifyCamelCase( $bits[1] );

        return false;
    }

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
                $file = 'helper.php';
                $class_prefix = 'Helper';
                break;
            case 'model':
                $folder = 'models';
                $file = 'model.php';
                $class_prefix = 'Model';
                break;
            case 'controller':
                $folder = 'controllers';
                $file = 'controller.php';
                $class_prefix = 'Controller';
                break;
            case 'view':
                $folder = 'views';
                $file = 'view.php';
                $class_prefix = 'View';
                break;
        }

        require_once( $file );

        if( !property_exists( $this, 'plugin_directory' ) && property_exists( $this, 'directory' ) )
            $this->plugin_directory = dirname( dirname( $this->directory ) );

        if( property_exists( $this, 'plugin_directory' ) && file_exists( $this->plugin_directory . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . $class . '.php' ) )
            require_once( $this->plugin_directory . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . $class . '.php' );
        elseif( file_exists( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . $class . '.php' ) )
            require_once( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $class . DIRECTORY_SEPARATOR . $class . '.php' );
        else
            return false;

        $object = 'Herc' . $class_prefix . '_' . $this->UpperCamelCaseIt( $class );

        if( $new )
            return new $object;

        if( empty( $this->$object ) )
            $this->$object = new $object;

        return $this->$object;
    }

    function GetUrl( $file = '' )
    {
        return plugins_url( $this->GetPluginFolderName() . '/' . $file, $this->GetPluginDirectory() );
    }

    function GetPluginDirectory()
    {
        return dirname( dirname( __FILE__ ) );
    }

    function GetPluginFolderName()
    {
        $plugin_folder = $this->GetPluginDirectory();
        $folder_bits = explode( DIRECTORY_SEPARATOR, $plugin_folder );

        return array_pop( $folder_bits );
    }

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

	function AddToData( $data )
	{
		if( empty( $this->data ) )
			$this->data = array();

		if( !is_array( $this->data ) )
			$this->data = array( $this->data );

		$this->data = array_merge( $this->data, $data );
	}
}