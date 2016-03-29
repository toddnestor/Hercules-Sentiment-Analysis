<?php namespace Hercules;

class View extends HercAbstract
{
    function __construct()
    {
        $this->template   = !property_exists( $this, 'template' ) || empty( $this->template ) ? 'template.php' : $this->template;
        $this->name       = !property_exists( $this, 'name' ) || empty( $this->name ) ? '' : $this->name;
        $this->menu_name  = !property_exists( $this, 'menu_name' ) || empty( $this->menu_name ) ? $this->name : $this->menu_name;
        $this->class_name = !property_exists( $this, 'class_name' ) || empty( $this->class_name ) ? __CLASS__ : $this->class_name;
		$this->model      = !property_exists( $this, 'model' ) || empty( $this->model ) ? ( $this->Model(  $this->CurrentSlug() ) ? $this->CurrentSlug() : false ) : $this->model;

		$this->AddToData( array( 'current_page' => $this->GetCurrentPage() ) );

		$post_data = array_merge( $_POST, $_GET );

		if( $this->Model( $this->model ) && !empty( $post_data ) && !empty( $post_data[ $this->Model( $this->model )->class_name ] ) )
			$this->AddToData( array( 'updated' => true ) );
    }

	/**
	 * This is the core view function, it actually renders stuff to the page using the Handlebars template engine.
	 *
	 * This will also add the class name to all form inputs so that form data is an array of data based on the current class name.
	 *
	 * @param array $data data to be used when rendering.
	 * @param bool|false $return If set to true then it will return the html for rendering rather than echoing it out.
	 * @return null|string no return value if $return is set to false, otherwise the html string that is created during rendering.
	 */
    function Render( $data = array(), $return = false )
    {
        if( !is_bool( $return ) )
            $return = false;

        if( !empty( $data ) )
        {
			if( is_object( $data ) && property_exists( $data, 'post_title' ) && property_exists( $data, 'ID' ) )
			{
				$slug = property_exists( $this, 'model' ) && !empty( $this->model ) ? $this->Model( $this->model )->CurrentSlug() : $this->CurrentSlug();
				$meta_data = $this->Model( $slug )->GetMeta($data->ID);
			}
			else
			{
				$meta_data = array();
			}

            if( !array( $meta_data ) )
                $meta_data = array( $meta_data );

            if( empty( $meta_data ) )
                $meta_data = array();

            $this->data = array_merge( $this->data, $meta_data );
        }

		$this->GenerateData();

		if( !empty( $data ) && is_array( $data ) )
			$this->data = array_merge( $this->data, $data );

        if( file_exists( $this->directory . DIRECTORY_SEPARATOR . $this->template ) )
        {
            $template = file_get_contents( $this->directory . DIRECTORY_SEPARATOR . $this->template );

            $template = $this->Helper( 'handlebars' )
                ->Render( $template, ( !empty( $this->data ) ? $this->data : array() ) );

			if( !property_exists( $this, 'dynamic_names' ) || $this->dynamic_names == true )
			{
				$template = preg_replace_callback(
					'`name="([^"]*)"`',
					array($this, 'AddClassNameToPostNames'),
					$template
				);
			}

            if( !$return )
                echo $template;
            else
                return $template;
        }
    }

	/**
	 * Creates the name attribute for form inputs based on the current class.
	 * @param $matches array of matches provided by a preg_replace_callback function
	 * @return string name attribute with the classname[] thrown around the original name.
	 */
    function AddClassNameToPostNames( $matches )
    {
        if( $this->Model( $this->CurrentSlug() ) )
            return 'name="' . $this->Model( $this->CurrentSlug() )->class_name . '[' . $matches[ 1 ] . ']"';
        else
            return 'name="' . $this->class_name . '[' . $matches[ 1 ] . ']"';
    }

	function EnqueueScript( $handle = '', $style, $dependencies = array() )
	{
		wp_enqueue_script( ( empty( $handle ) ? __CLASS__ . '_' . sanitize_title( $style ) : $handle ), $this->GetUrl( $style ), $dependencies );
	}

	function RegisterScript( $style, $handle = '', $dependencies = array() )
	{
		wp_register_script( $style, $handle, $dependencies );
	}

	/**
	 * Registers all scripts for the current view.
	 */
	function RegisterAllScripts()
	{
		if( is_dir( $this->directory . DIRECTORY_SEPARATOR . 'js' ) )
			$scripts = scandir( $this->directory . DIRECTORY_SEPARATOR . 'js' );

		if( !empty( $scripts ) )
		{
			foreach( $scripts as $key=>$val )
			{
				if( $val == '.' || $val == '..' )
					continue;

				if( strpos( $val, '.js' ) !== false )
					$this->RegisterScript( 'herc-' . str_replace('.js','',$val), $this->GetUrl( str_replace( (property_exists( $this, 'plugin_directory' ) ? $this->plugin_directory : dirname( dirname( dirname( __FILE__ ) ) ) ) . DIRECTORY_SEPARATOR, '', $this->directory . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $val ) ) );
			}
		}
	}

	function RegisterAllStyles()
	{

	}

    function EnqueueStyleSheet( $style, $handle = '' )
    {
        wp_enqueue_style( ( empty( $handle ) ? __CLASS__ . '_' . sanitize_title( $style ) : $handle ), $this->GetUrl( $style ) );
    }

	function RegisterStyleSheet( $style, $handle = '' )
	{
		wp_register_style( $handle, $style, array() );
	}

	/**
	 * Enqueues bootstrap which only works inside of .herc-bootstrap sections so that we don't interfere with other styles.
	 */
    function EnqueueBootstrap()
    {
        $this->EnqueueStyleSheet( $this->GetFrameworkFolderName() . '/assets/css/bootstrap.css', sanitize_title( $this->GetFrameworkFolderName() . '_bootstrap' ) );
    }

	/**
	 * Registers the enqueue bootstrap function so it enqueues in the admin area and the public side.
	 */
    function IncludeBootstrap()
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'EnqueueBootstrap' ) );
        add_action( 'admin_print_styles', array( $this, 'EnqueueBootstrap' ) );
    }

	/**
	 * Registers metaboxes to be rendered on post edit screens.
	 */
    function RegisterMetaboxes()
    {
		$slug = property_exists( $this, 'model' ) && !empty( $this->model ) ? $this->Model( $this->model )->CurrentSlug() : $this->CurrentSlug();
		$this->data[ 'class_name' ] = $this->Model( $slug )->class_name;

        foreach( $this->metabox_positions as $key => $val )
        {
            if( !empty( $val[ 'post_type' ] ) )
            {
                if( empty( $val[ 'position' ] ) )
                    $val[ 'position' ] = 'normal';
                if( empty( $val[ 'priority' ] ) )
                    $val[ 'position' ] = 'default';

                add_meta_box( 'metabox_' . $this->class_name, $this->name, array( $this, 'Render' ), $val[ 'post_type' ], $val[ 'position' ], $val[ 'priority' ] );
            }
        }
    }

	/**
	 * Adds the view to the post content if it is a post add on.
	 * @param $content post content provided by WP.
	 * @return string the new post content with the view added on.
	 */
    function PostFilter( $content )
    {
		global $post;

		if( method_exists( $this, 'GenerateData' ) && ( !property_exists( $this, 'posts_data_generated' ) || $this->posts_data_generated != $post->ID ) )
            $this->GenerateData();

        $html = $this->Render( array(), true );

        if( $this->location == 'before' )
            return $html . $content;
        else
            return $content . $html;
    }

	/**
	 * Generates data to be used when rendering.
	 */
    function GenerateData()
    {
        if( is_object( $this->Model( $this->model ) ) )
        {
			if( $this->type == 'options_page' || $this->type == 'admin_page' )
			{
				$this->AddToData( $this->Model( $this->model )->GetOptions() );
			}
			else
			{
				global $post;

				if( !property_exists( $this, 'data' ) )
					$this->data = array();

				if( !is_array( $this->data ) )
					$this->data = array( $this->data );

				if( !empty( $post ) && is_object( $post ) && property_exists( $post, 'ID' ) )
				{
					$meta_data = $this->Model( 'post-settings' )->GetMeta( $post->ID );

					if( !is_array( $meta_data ) )
						$meta_data = array( $meta_data );

					if( empty( $meta_data ) )
						$meta_data = array();

					$this->data = array_merge( $this->data, $meta_data );
				}
			}
        }

        $this->posts_data_generated = true;
    }

	/**
	 * Registers an options page if this is an options page view.
	 */
    function AddOptionsPage()
    {
        add_options_page(
            $this->name,
            $this->menu_name,
            ( property_exists( $this, 'capability' ) && !empty( $this->capability ) ? $this->capability : 'manage_options' ),
            $this->class_name, array( $this, 'Render' )
        );
    }

	/**
	 * Adds columns to the posts lists tables if applicable for this view.
	 * @param $columns WP provided array of columns already being rendered.
	 * @return array columns array with our new columns added on.
	 */
    function AddPostColumns( $columns )
    {
        $new_columns = $this->PostsColumns();

        return array_merge( $columns, $new_columns );
    }

	/**
	 * Adds columns to the comments list table if applicable for this view.
	 * @param $columns WP provided array of columns already being rendered.
	 * @return array columns array with our new columns added on.
	 */
	function AddCommentColumns( $columns )
	{
		$new_columns = $this->CommentsColumns();

		return array_merge( $columns, $new_columns );
	}

	/**
	 * Calculates the value for the current custom column if applicable.
	 *
	 * @param $colname name of the column currently being rendered.
	 * @param $post_id id of the post that is being rendered.
	 */
    function PostColumnValues( $colname, $post_id )
    {
		$slug = property_exists( $this, 'model' ) && !empty( $this->model ) ? $this->Model( $this->model )->CurrentSlug() : $this->CurrentSlug();
		if( $this->Model( $slug ) )
        	$meta_data = $this->Model( $this->CurrentSlug() )->GetMeta( $post_id );
		else
			$meta_data = array();

        $custom_columns = $this->PostsColumns();

        if( !empty( $custom_columns[ $colname ] ) )
        {
            $method_name = $this->UpperCamelCaseIt( $colname ) . 'Filter';

            if( method_exists( $this, $method_name ) )
                if( !empty( $meta_data[ $colname ] ) )
                    echo $this->$method_name( $meta_data[ $colname ] );
                else
                    echo $this->$method_name( '' );
            else
                echo !empty( $meta_data[ $colname ] ) ? $meta_data[ $colname ] : '';
        }
    }

	/**
	 * Calculates the value for the current custom column if applicable.
	 *
	 * @param $colname name of the column currently being rendered.
	 * @param $post_id id of the comment that is being rendered.
	 */
	function CommentColumnValues( $colname, $post_id )
	{
		$slug = property_exists( $this, 'model' ) && !empty( $this->model ) ? $this->Model( $this->model )->CurrentSlug() : $this->CurrentSlug();

		if( $this->Model( $slug ) )
			$meta_data = $this->Model( $this->CurrentSlug() )->GetMeta( $post_id );
		else
			$meta_data = array();

		$custom_columns = $this->CommentsColumns();

		if( !empty( $custom_columns[ $colname ] ) )
		{
			$method_name = $this->UpperCamelCaseIt( $colname ) . 'Filter';

			if( method_exists( $this, $method_name ) )
				if( !empty( $meta_data[ $colname ] ) )
					echo $this->$method_name( $meta_data[ $colname ] );
				else
					echo $this->$method_name( '' );
			else
				echo !empty( $meta_data[ $colname ] ) ? $meta_data[ $colname ] : '';
		}
	}

	/**
	 * Adds an admin page to WP if this is an admin page view.
	 */
	function AddAdminPage()
	{
		add_menu_page(
			$this->name,
			$this->menu_name,
			( property_exists( $this, 'capability' ) && !empty( $this->capability ) ? $this->capability : 'manage_options' ),
			$this->class_name,
			array( $this, 'Render' ),
			( property_exists( $this, 'icon' ) && !empty( $this->icon ) ? $this->icon : '' ),
			( property_exists( $this, 'priority' ) ? $this->priority : false )
		);
	}

	/**
	 * Registers a short code to be used in WP.
	 * @param array $attributes provided by WP, it is the attributes added to the shortcode.
	 * @return string HTML string of the rendered view.
	 */
	public function RegisterShortcode( $attributes = array() )
	{
		return $this->Render( $attributes, true );
	}

	/**
	 * Handles all the code that needs to be handled each time WP is initialized.
	 */
    function Initialize()
    {
        if( $this->type == 'metabox' && !empty( $this->metabox_positions ) )
            add_action( 'add_meta_boxes', array( $this, 'RegisterMetaboxes' ) );
        elseif( $this->type == 'post-add-on' )
            add_filter( 'the_content', array( $this, 'PostFilter' ) );
        elseif( $this->type == 'admin_menu' )
            add_action( 'admin_menu', array( $this, 'Menu' ) );
        elseif( $this->type == 'options_page' )
            add_action( 'admin_menu', array( $this, 'AddOptionsPage' ) );
		elseif( $this->type == 'admin_page' )
			add_action( 'admin_menu', array( $this, 'AddAdminPage' ) );
		elseif( $this->type == 'shortcode' )
			add_shortcode( property_exists( $this, 'shortcode' ) ? $this->shortcode : $this->class_name, array( $this, 'RegisterShortcode' ) );

        if( method_exists( $this, 'PostsColumns' ) )
        {
            if( property_exists( $this, 'post_type' )  && !empty( $this->post_type ) )
			{
				if( is_array( $this->post_type ) )
				{
					foreach( $this->post_type as $post_type )
					{
						$this->RegisterPostColumn( $post_type );
					}
				}
				else
				{
					$this->RegisterPostColumn( $this->post_type );
				}
			}
            else
				$this->RegisterPostColumn( 'posts' );
        }

		if( method_exists( $this, 'CommentsColumns' ) )
		{
			add_filter( 'manage_edit-comments_columns', array( $this, 'AddCommentColumns' ) );
			add_filter( 'manage_edit-comments_sortable_columns', array( $this, 'AddCommentColumns' ) );
			add_action( 'manage_comments_custom_column', array( $this, 'CommentColumnValues' ), 10, 2);
		}
    }

	/**
	 * Handles adding columns and functions to process values for those columns to post list tables.
	 *
	 * @param $post_type string of the post type to process actions for.
	 */
	function RegisterPostColumn( $post_type )
	{
		switch( $post_type )
		{
			case 'all':
				$post_types = get_post_types( '', 'names' );
				$exclude_types = array( 'attachment', 'revision', 'nav_menu_item' );
				foreach( $post_types as $type )
				{
					if( !in_array( $type, $exclude_types ) )
						$this->RegisterPostColumn( $type );
				}
				break;
			case 'posts':
			case 'post':
				add_filter('manage_posts_columns', array($this, 'AddPostColumns'));
				add_action('manage_posts_custom_column', array($this, 'PostColumnValues'), 10, 2);
				break;
			default:
				add_filter('manage_' . $post_type . '_posts_columns', array($this, 'AddPostColumns'));
				add_action('manage_' . $post_type . '_posts_custom_column', array($this, 'PostColumnValues'), 10, 2);
				break;
		}
	}
}