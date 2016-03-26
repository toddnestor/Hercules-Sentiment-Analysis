<?php namespace Hercules;

class Model extends HercAbstract
{
    function __construct()
    {
        $this->class_name = empty( $this->class_name ) ? __CLASS__ : $this->class_name;
    }

    function RegisterPostMetaSave( $post_id )
    {
        if( !empty( $_POST[ $this->class_name ] ) )
            update_post_meta( $post_id, $this->class_name, $_POST[ $this->class_name ] );
    }

    function Initialize()
    {
        if( $this->View( $this->CurrentSlug() )->type == 'metabox' && !empty( $this->View( $this->CurrentSlug() )->metabox_positions ) )
            add_action( 'save_post', array( $this, 'RegisterPostMetaSave' ) );

		$this->UpdateOptions();
    }

    function GetMeta( $post_id )
    {
        return maybe_unserialize( get_post_meta( $post_id, $this->class_name, true ) );
    }

	function GetOptions()
	{
		return get_option( $this->class_name );
	}

	function GetOption( $key )
	{
		$options = $this->GetOptions();

		if( is_array( $options ) )
			return !empty( $options[ $key ] ) ? $options[ $key ] : false;
		else
			return $options;
	}

	function UpdateOptions()
	{
		$post_data = array_merge( $_POST, $_GET );

		if( !empty( $post_data ) && !empty( $post_data[ $this->class_name ] ) )
		{
			update_option( $this->class_name, $post_data[ $this->class_name ] );
		}
	}
}