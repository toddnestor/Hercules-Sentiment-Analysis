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
	 * Processes comments based on settings.  Auto approves positive comments if that setting is set, unapproves negative comments if that setting is set.
	 */
	public function HandleComments()
	{
		$comments = get_comments( array( 'status' => 'hold', 'meta_query' => array( array( 'key' => 'sentiment-checked', 'compare' => 'NOT EXISTS' ) ) ) );

		$autoapprove = $this->Model('settings')->GetOption('auto_approve_positive_comments') == 'yes' ? true : false;
		$autounapprove = $this->Model('settings')->GetOption('auto_unapprove_negative_comments') == 'yes' ? true : false;

		foreach( $comments as $comment )
		{
			$sentiment = $this->Helper('phpinsight')->sentiment->categorise( $comment->comment_content );

			if( $sentiment == 'pos' && $autoapprove )
				wp_set_comment_status( $comment->comment_ID, 'approve' );

			if( $sentiment == 'neg' && $autounapprove )
				wp_set_comment_status( $comment->comment_ID, 'hold' );

			add_comment_meta( $comment->comment_ID, 'sentiment-checked', true, true );
		}
	}

	/**
	 * Sets functions that need to be called each time Wordpress is initiated.
	 */
	public function Initialize()
	{
		parent::Initialize();

		if( $this->Model('settings')->GetOption('auto_approve_positive_comments') == 'yes' || $this->Model('settings')->GetOption('auto_unapprove_negative_comments') == 'yes' )
			add_action( 'init', array( $this, 'HandleComments' ) );
	}
}