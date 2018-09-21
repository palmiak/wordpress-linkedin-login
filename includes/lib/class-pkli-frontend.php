<?php
/*
 * This classes handles frontend functions and shortcodes
 * @author Maciej Palmowski <m.Palmowski@freshpixels.pl>
 */

if ( !function_exists( 'is_user_logged_in' ) ) {
    require_once( ABSPATH . 'wp-includes/pluggable.php' );
}

class PKLI_Frontend {

    // This stores our plugin options
    private $user_data;
    private $user_id;

    /*
     * Class constructor
     */
    public function __construct( $id = '' ) {
        $this->user_id = $id ;
        $this->user_data = get_user_meta( $this->user_id, 'pkli_linkedin_profile' );

        if( 0 != $this->user_id && !isset( get_user_meta( $this->user_id, 'pkli_linkedin_profile' )[0] ) ) {
            $this->user_data = false;
        }
    }

    public function get_linkedln_userdata( $field_name ) {
        if ( false !== $this->user_data ) {
            // for typical field names
            if ( isset( $this->user_data[$field_name] ) ) {
                return $this->user_data[$field_name];
            } elseif ( 'location_name' === $field_name && isset( $this->user_data['location']['name'] ) ) { // location name
                return $this->user_data['location']['name'];
            } elseif ( 'location_country_code' === $field_name && isset( $this->user_data['location']['country_code'] ) ) { // location country code
                return $this->user_data['location']['country_code'];
            }
        } else {
            return '';
        }
    }

    public function has_linkedln_userdata( $field_name ) {
        if ( false !== $this->user_data ) {
            // for typical field names
            if ( isset( $this->user_data[$field_name] ) ) {
                return true;
            } elseif ( 'location_name' === $field_name && isset( $this->user_data['location']['name'] ) ) { // location name
                return true;
            } elseif ( 'location_country_code' === $field_name && isset( $this->user_data['location']['country_code'] ) ) { // location country code
                return true;
            }
        } else {
            return false;
        }
    }

    public function has_linkedln_data() {
        if ( $this->user_data === false ) {
            return false;
        } else {
            return true;
        }
    }

    public function linkedln_userdata( $field_name ) {
        if ( false !== $this->user_data ) {
            echo self::get_linkedln_userdata( $field_name );
        } else {
            return '';
        }
    }
}

//$test = new PKLI_Frontend( get_current_user_id() );
//var_dump( $test );
//var_dump( $test->get_linkedln_userdata( 'first' ) );
//$test->linkedln_userdata( 'location_name' );

/**
 * Addinig global variable on author page and singular
 */
add_action( 'wp', 'linkedin_init' );

function linkedin_init() {
    global $user_linkedin_data;

    if( is_author() ) {
        if ( get_query_var( 'author' ) ) {
            $user_linkedin_data = new PKLI_Frontend( get_query_var( 'author' ) );
        }
    } elseif( is_singular() ) {
        global $post;
        if( $post->post_author ) {
            $user_linkedin_data = new PKLI_Frontend( $post->post_author );
        }
    }
}