<?php
/*
Plugin Name: Hercules Sentiment Analysis
Description: Adds a sentiment analysis tools that show the overal sentiment (positive, negative, neutral) of comments, posts, and titles so you can better target your audience and respond to their needs.
Author: Todd D. Nestor - todd.nestor@gmail.com
Author URI: http://toddnestor.com
Version: 1.1
License: GNU General Public License v3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

    /**
     * This class contains all of the functions that build the sentiment analysis tools.
     */
    class HercSentimentAnalysis
    {
        /**
         * Adds the columns to the comments and posts tables and initials the sentiment analysis class.
         */
        function __construct()
        {
            require_once( dirname(__FILE__).'/php-insight/autoload.php' );
            $this->sentiment = new \PHPInsight\Sentiment();
            
            $this->SetSettingsVariables();
            //This will be used for a future version where we will have settings
            //add_action( 'admin_menu', array( $this, 'AddSettingsPage' ) );
            add_filter( 'manage_edit-comments_columns', array( $this, 'CommentSentimentColumns' ) );
            add_filter( 'manage_edit-comments_sortable_columns', array( $this, 'CommentSentimentColumns' ) );
            
            add_filter( 'manage_posts_columns', array( $this, 'PostTitleSentimentColumns' ) );
            //add_filter( 'manage_edit-post_sortable_columns', array( $this, 'PostTitleSentimentColumns' ) );
            
            add_filter( 'manage_posts_columns', array( $this, 'PostContentSentimentColumns' ) );
            //add_filter( 'manage_edit-post_sortable_columns', array( $this, 'PostContentSentimentColumns' ) );
            
            add_action( 'manage_comments_custom_column', array( $this, 'CommentSentimentColumn' ), 10, 2);
            add_action( 'manage_posts_custom_column', array( $this, 'PostSentimentColumn' ), 10, 2);
        }
        
        /**
         * Adds the sentiment column to the comments table
         * @param array $columns Provided by Wordpress
         */
        function CommentSentimentColumns( $columns )
        {
            $columns["sentiment"] = "Sentiment";
            return $columns;
        }
        
        /**
         * Adds the title sentiment column to the posts table
         * @param array $columns Provided by Wordpress
         */
        function PostTitleSentimentColumns( $columns )
        {
            $columns["title-sentiment"] = "Title Sentiment";
            return $columns;
        }
        
        /**
         * Adds the post sentiment column to the posts table
         * @param array $columns Provided by Wordpress
         */
        function PostContentSentimentColumns( $columns )
        {
            $columns["content-sentiment"] = "Post Sentiment";
            return $columns;
        }
        
        /**
         * Conducts the sentiment analysis on the comment and fills in the sentiment column
         * @param array $colname Provided by Wordpress, an array of the columns in the table
         * @param integer $cptid Provied by Wordpress, this is the id for the current element
         */
        function CommentSentimentColumn( $colname, $cptid )
        {
            if ( $colname == 'sentiment' )
            {
                 $comment = get_comment( $cptid );
                 
                 $sentiment = $this->sentiment->categorise( $comment->comment_content );
                 
                 $sentiments = array(
                    'neu'   => 'Neutral',
                    'pos'   => 'Positive',
                    'neg'   => 'Negative',
                 );
                 
                 echo $sentiments[ $sentiment ];
            }
        }
        
        /**
         * Conducts the sentiment analysis on the post title and content and fills in the sentiment columns
         * @param array $colname Provided by Wordpress, an array of the columns in the table
         * @param integer $cptid Provied by Wordpress, this is the id for the current element
         */
        function PostSentimentColumn( $colname, $cptid )
        {
            if ( $colname == 'title-sentiment' || $colname == 'content-sentiment' )
            {
                 $post = get_post( $cptid );
                 
                 $sentiments = array(
                    'neu'   => 'Neutral',
                    'pos'   => 'Positive',
                    'neg'   => 'Negative',
                 );
                 
                 if( $colname == 'title-sentiment' )
                 {
                    $sentiment = $this->sentiment->categorise( $post->post_title );
                    echo $sentiments[ $sentiment ];
                 }
                 elseif( $colname == 'content-sentiment' )
                 {
                    $sentiment = $this->sentiment->categorise( $post->post_content );
                    echo $sentiments[ $sentiment ];
                 }
            }
        }
        
        /**
         * Generates the HTML for the settings page which shows up as a submenu of "Settings" in wp-admin
         */
        function GenerateSettingsPage()
        {
            if( isset( $_POST["update_herc_sentiment_analysis_settings"] ) )
            {
                update_option( 'herc_sentiment_analysis_options', $_POST['herc_sentiment_analysis_options'] );
                $success_msg = "Settings Updated!";
            }
            $this->SetSettingsVariables();
            ?>
                <div class="wrap">
                    <h2>Hercules Sentiment Analysis Settings</h2>
                        <table class="form-table">
                            <?php if( !empty( $success_msg ) ): ?>
                            <tr valign="top">
                                <th colspan="2" scope="row">
                                    <span style="color:red; font-weight:bold;"><?php echo $success_msg; ?></span>
                                </th>
                            </tr>
                            <?php endif; ?>
                            <tr valign="top">
                                <th colspan="2" scope="row">
                                    Set Stuff
                                </th>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <form method="POST" action="">
                                    <input type="hidden" name="update_herc_sentiment_analysis_settings" value="Y" />
                                    <label for="herc_sentiment_analysis_options[something]">
                                        Something
                                    </label> 
                                </th>
                                <td>
                                    <input type="text" name="herc_sentiment_analysis_options[something]" size="25" value="<?php echo $this->options['something']; ?>" />
                                    <?php echo $this->sentiment->categorise( $this->options['something'] ); ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    
                                </th>
                                <td>
                                    <input type="submit" />
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    
                                </th>
                                <td>
                                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                        <input type="hidden" name="cmd" value="_s-xclick">
                                        <input type="hidden" name="hosted_button_id" value="GDBHPL4Y24ZXQ">
                                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                                    </form>
                                </td>
                            </tr>
                    </table>
                </div>
            <?php
        }
        
        /**
         * Adds the settings page as a submenu of "Settings"
         */
        function AddSettingsPage()
        {
            add_options_page( 'Hercules Sentiment Analysis', 'Hercules Sentiment Analysis', 'manage_options', 'hercules-sentiment-analysis', array( $this, 'GenerateSettingsPage' ) );
        }

        /**
         * @return array|void Recaptcha options as an array from the database.
         */
        function GetSettings()
        {
            return get_option( 'herc_sentiment_analysis_options' );
        }
        
        function SetSettingsVariables()
        {
            $this->options = $this->GetSettings();
        }
    }
    
$herc_sentiment_analysis = new HercSentimentAnalysis;

?>