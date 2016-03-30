<?php

use Hercules\View;

class HercView_SentimentAnalysis extends View
{
	/**
	 * Sets the main data used byt hte framework and some other variables used in just this class.
	 */
    function __construct()
    {
        $this->directory         = dirname( __FILE__ );
        $this->name              = 'Sentiment Analysis';
        $this->type              = 'post-columns';
        $this->class_name        = __CLASS__;

		//Defines what post types the sentiment columns will be added to.
		$this->post_type = array('all');

		$this->sentiments = array(
			'neu'   => 'Neutral',
			'pos'   => 'Positive',
			'neg'   => 'Negative',
		);

        $this->data = array();

        parent::__construct();
    }

	/**
	 * Defines the array of columns to add to the the post list tables.
	 *
	 * @return array columns we want to add to the post list tables.
	 */
    function PostsColumns()
    {
        return array(
            'title-sentiment' => 'Title Sentiment',
			'post-sentiment' => 'Post Sentiment'
        );
    }

	/**
	 * Defines the array of columns to add to the comment list table.
	 *
	 * @return array columns we want to add to the comment list table.
	 */
	function CommentsColumns()
	{
		return array(
			'comment-sentiment' => 'Sentiment'
		);
	}

	/**
	 * Sets the function to handle printing data to the sentiment column on the comments list table.
	 *
	 * @return String Sentiment string to display in the comment sentiment column.
	 */
	function CommentSentimentFilter()
	{
		global $comment;
		return $this->sentiments[ $this->Helper('phpinsight')->sentiment->categorise( $comment->comment_content ) ];
	}

	/**
	 * Sets the function to handle printing data to the post title sentiment column in the posts list tables.
	 *
	 * @return String Sentiment string to display in the post title sentiment column.
	 */
    function TitleSentimentFilter()
    {
		global $post;
        return $this->sentiments[ $this->Helper('phpinsight')->sentiment->categorise( $post->post_title ) ];
    }

	/**
	 * Sets the function to handle printing data to the post content sentiment column in the posts list tables.
	 *
	 * @return String Sentiment string to display in the post content sentiment column.
	 */
	function PostSentimentFilter()
	{
		global $post;
		return $this->sentiments[ $this->Helper('phpinsight')->sentiment->categorise( $post->post_content ) ];
	}
}