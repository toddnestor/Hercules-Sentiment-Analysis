<?php namespace Hercules;

class Model extends HercAbstract
{
    function __construct()
    {
        $this->class_name = empty( $this->class_name ) ? __CLASS__ : $this->class_name;
    }

	/**
	 * Saves the custom settings for the current model when a post is saved.
	 *
	 * @param $post_id WP will supply the post id for the current post being saved.
	 */
    function RegisterPostMetaSave( $post_id )
    {
        if( !empty( $_POST[ $this->class_name ] ) )
            update_post_meta( $post_id, $this->class_name, $_POST[ $this->class_name ] );
    }

	/**
	 * Sets the functions that need to be run when WP is initialized.
	 */
    function Initialize()
    {
        if( $this->View( $this->CurrentSlug() )->type == 'metabox' && !empty( $this->View( $this->CurrentSlug() )->metabox_positions ) )
            add_action( 'save_post', array( $this, 'RegisterPostMetaSave' ) );

		if( property_exists( $this, 'has_options' ) && !empty( $this->has_options ) )
			$this->UpdateOptions();
    }

	/**
	 * Gets post meta related to the current model
	 * @param $post_id Id of post to get meta data for.
	 * @return mixed all the meta data for the current model on the provided post.
	 */
    function GetMeta( $post_id )
    {
        return maybe_unserialize( get_post_meta( $post_id, $this->class_name, true ) );
    }

	/**
	 * Gets options for the current model.
	 *
	 * @return mixed all the options related to the current model.
	 */
	function GetOptions()
	{
		return get_option( $this->class_name );
	}

	/**
	 * Gets individual option from the current model.
	 * @param $key Option to get value from.
	 * @return bool|mixed false if no value found, otherwise the option value for the provided key.
	 */
	function GetOption( $key )
	{
		$options = $this->GetOptions();

		if( is_array( $options ) )
			return !empty( $options[ $key ] ) ? $options[ $key ] : false;
		else
			return false;
	}

	/**
	 * Updates all the options for the current model if there is any post or get data.
	 */
	function UpdateOptions()
	{
		$post_data = array_merge( $_POST, $_GET );

		if( !empty( $post_data ) && !empty( $post_data[ $this->class_name ] ) )
		{
			update_option( $this->class_name, $post_data[ $this->class_name ] );
		}
	}
}