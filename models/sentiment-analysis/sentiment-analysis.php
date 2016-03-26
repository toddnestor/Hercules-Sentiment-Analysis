<?php

use Hercules\Model;

class HercModel_SentimentAnalysis extends Model
{
	function __construct()
	{
		$this->class_name = __CLASS__;
		$this->directory = dirname( __FILE__ );

		parent::__construct();
	}

	/**
	 * Approves all positive comments, and marks comments as having been processed so we don't process the same comments multiple times.
	 */
	public function ApprovePositiveComments()
	{
		$comments = get_comments( array( 'status' => 'hold', 'meta_query' => array( array( 'key' => 'sentiment-checked', 'compare' => 'NOT EXISTS' ) ) ) );

		foreach( $comments as $comment )
		{
			$sentiment = $this->Helper('phpinsight')->sentiment->categorise( $comment->comment_content );

			if( $sentiment == 'pos' )
				wp_set_comment_status( $comment->comment_ID, 'approve' );

			add_comment_meta( $comment->comment_ID, 'sentiment-checked', true, true );
		}
	}

	/**
	 * Sets functions that need to be called each time Wordpress is initiated.
	 */
	public function Initialize()
	{
		parent::Initialize();

		if( $this->Model('settings')->GetOption('auto_approve_positive_comments') == 'yes' )
			add_action( 'init', array( $this, 'ApprovePositiveComments' ) );
	}
}