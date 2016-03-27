<?php namespace Hercules\Helper;

use Hercules\Helper;

class String extends Helper
{
    function __construct()
    {

    }

    /**
     * Returns an ellipses-ed excerpt of a string if the string has more words than is set in the limit.
     * @param String $text String used to create the excerpt.
     * @param Integer $limit Number of words allowed before doing an ellipses to indicate it is longer than shown.
     * @return String Returns the input text followed by an ellipses if it is longer than the limit.
     */
    function LimitText( $text, $limit )
    {
        if( str_word_count( $text, 0 ) > $limit )
        {
            $words = str_word_count( $text, 2 );
            $pos = array_keys( $words );
            $text = trim( substr( $text, 0, $pos[$limit] ) ) . '...';
        }

        return $text;
    }
}