<?php
/*
Plugin Name: Lazyest Stack
Plugin URI: http://brimosoft.nl/lazyest/stack
Description: Beautiful Photo Stack Gallery with jQuery and CSS3. Requires Lazyest Gallery 1.0.0 or higher
Date: 2012, December
Author: Brimosoft
Author URI: http://brimosoft.nl/
Version: 1.1.2
Text Domain: lazyest-stack
License: GNU GPL 
*/

/*
JQuery Plugin and CSS: Copyright (C) Codrops  http://tympanus.net/codrops/2010/06/27/beautiful-photo-stack-gallery-with-jquery-and-css3/ )
Copyright (C) 2008-2010 Marcel Brinkkemper
(For questions join discussion on http://brimosoft.nl/lazyest/stack )
*/

/**
 * LazyestStack
 * The Functionality to incorporate the Photo Stack Gallery into Lazyest Gallery
 * 
 * @package Lazyest Gallery 
 * @version 1.1
 * @author Marcel Brinkkemper (lazyest@brimosoft.nl)
 * @copyright 2011 Marcel Brinkkemper 
 * @license GNU GPL
 * @link http://brimosoft.nl/lazyest/stack/
 */
class LazyestStack {
  
  /**
   * LazyestStack::__construct()
   * 
   * @uses add_action()
   * @uses add_shortcode()
   * @return void
   */
  function __construct() {    
    $this->plugin_url = WP_PLUGIN_URL . '/' . plugin_basename( dirname( __file__ ) );
    $this->plugin_dir = WP_PLUGIN_DIR . '/' . plugin_basename( dirname( __file__ ) ); 
           
    add_action('wp_head', array( &$this, 'styles' ), 7 );
    add_action('wp_head', array( &$this, 'scripts' ), 5 );
    
    add_shortcode( 'lg_stack', array( &$this, 'stack_code') );
    
    add_action( 'wp_ajax_lg_stack_album', array( &$this, 'stack_album' ) );
    add_action( 'wp_ajax_nopriv_lg_stack_album', array( &$this, 'stack_album' ) );
  }
  
  /**
   * LazyestStack::styles()
   * Registers and enqueue style sheets and calculate and output height of gallery element
   * 
   * @since 1.0
   * @uses wp_register_style()
   * @uses wp_enqueue_style()
   * @return void
   */
  function styles() {
    global $lg_gallery;
    wp_register_style( 'lazyest-stack-style', $this->plugin_url . '/css/lazyest-stack.css' );
    wp_enqueue_style( 'lazyest-stack-style' );
    echo sprintf( '<style type="text/css" media="screen"> .lg_stack{ height: %dpx; } </style>',
       350 + intval( $lg_gallery->get_option( 'pictheight' ) ) 
    );
  }
  
  /**
   * LazyestStack::scripts()
   * Registers and enqueues the javascript
   * 
   * @since 1.0
   * @uses wp_register_script()
   * @uses wp_enqueue_script()
   * @uses wp_localize_script()
   * @return void
   */
  function scripts() {
    wp_register_script( 'lg_stack', $this->plugin_url . '/js/lazyest-stack.js', array( 'jquery' ), '1.1', true );
    wp_enqueue_script( 'lg_stack' );
    wp_localize_script( 'lg_stack', 'lg_stack', $this->localize_script() );
  }
  
  /**
   * LazyestStack::localize_script()
   * Output strings for the javascript
   * 
   * @since 1.0
   * @return array of strings
   */
  function localize_script() {
    return array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
  }
  
  /**
   * LazyestStack::stack_code()
   * Handle the shortcode
   * 
   * @since 1.0
   * @param array $atts shortcode attributes
   * @uses LazyestFrontend::_set_root()
   * @uses shortcode_atts()
   * @uses current_user_can()
   * @uses get_option()
   * @return string of html
   * @example [lg_stack root="myalbum/"] show a stacked photo album starting from folder "myalbum/" 
   */
  function stack_code( $atts ) {
    global $lg_gallery;
    extract( shortcode_atts ( array( 'lg_stack' => '', 'root' => ''), $atts) );
    ob_start();
    $show = ( isset( $lg_gallery ) );       
    if ( $show === false ) {
      ?>
      <div class="error">
        <p><strong><?php echo  __( 'Something went wrong initializing Lazyest Gallery.', 'lazyest-stack' ); ?></strong></p>
        <p><?php echo __( 'Maybe the folder you are looking for does not exist', 'lazyest-stack' ); ?></p>
        <p>
      <?php 
        _e( 'Please check your Lazyest Gallery settings, ', 'lazyest-stack' ); 
        if ( current_user_can( 'manage_options' ) ) {
          ?>
          <a href="<?php echo get_option( 'siteurl' ); ?>/wp-admin/admin.php?page=lazyest-gallery"><?php _e(' here,', 'lazyest-stack'); ?></a>
          <?php
        }
        _e( ' or contact the author of this page ', 'lazyest-stack' );
      ?>      
        </p>
      </div>
      <?php 
    } else {   
      if ( '' != $root ) {
        $lg_gallery->_set_root( utf8_decode( $root ) ); 
      }
      $this->show();                  
    } 
    $new_content = ob_get_contents();
    ob_end_clean();
    return $new_content;    
  }
  
  /**
   * LazyestStack::show()
   * Output the Stacked Photo Gallery
   * Sets and restores the folder icon to random image and back 
   * 
   * @since 1.0
   * @uses LazyestGallery::get_option()
   * @uses LazyestGallery::change_option()
   * @uses LazyestGallery::update_option()
   * @return void
   */
  function show() {
    global $lg_gallery;
    $oldsetting = $lg_gallery->get_option( 'folder_image' );
    $lg_gallery->change_option( 'folder_image', 'random_image' );
    $this->show_dirs();
    $this->overlay();    
    $lg_gallery->update_option( 'folder_image', $oldsetting );
  }    
  
  /**
   * LazyestStack::show_dirs()
   * Output all non-empty folders in the gallery
   * 
   * @since 1.0
   * @uses LazyestGallery::folders()
   * @uses LazyestFolder::count()
   * @uses LazyestFolder::open()
   * @uses LazyestFolder::icon()
   * @return void
   */
  function show_dirs() {
    global $lg_gallery;    
    $folders = $lg_gallery->folders( 'subfolders', 'visible' );
    ?>
    <div class="lg_stack">
      <div id="ps_slider" class="ps_slider">
  			<a class="prev disabled"></a>
  			<a class="next disabled"></a>
  			<div id="ps_albums">
        <?php
        if ( 0 != count( $folders ) ) {
          foreach ( $folders as $folder ) {
            if ( 0 == $folder->count() ) 
              continue;       
            $folder->open();
            $icon = $folder->icon();
            $div = sprintf( '<div id="%s" class="ps_album" style="opacity:0;"><img src="%s" alt=""/><div class="ps_desc"><h2>%s</h2><span>%s</span></div></div>',
              rawurlencode( $folder->curdir ),
              $icon['icon'],
              $folder->caption(),
              $folder->description() 
            );
            echo "$div\n";
          }
        }
        ?>
        </div>
     </div>
   </div>
   <?php
  }
  
  /**
   * LazyestStack::overlay()
   * Ouput the overlay element
   * 
   * @since 1.0
   * @return void
   */
  function overlay() {
    ?>
    <div id="ps_overlay" class="ps_overlay" style="display:none;"></div>
		<a id="ps_close" class="ps_close" style="display:none;"></a>
		<div id="ps_container" class="ps_container" style="display:none;">
			<a id="ps_next_photo" class="ps_next_photo" style="display:none;"></a>
		</div>
    <?php
  }
  
  /**
   * LazyestStack::stack_album()
   * Response to ajax call for the contents of a folder in the form of image urls. 
   * 
   * @since 1.0
   * @uses LazyestFolder->load()
   * @uses LazyestSlide->src()
   * @return json encoded array of strings 
   */
  function stack_album() {
    global $lg_gallery; 
    $folder = new LazyestFolder( urldecode( $_REQUEST['album_name'] ) );
    $folder->load( 'slides');
    $files = array();
    foreach( $folder->list as $image ) {
      $files[] = $image->src();  
    }   
    $encoded = json_encode( $files );
    echo $encoded;
    die();
  }
  
}// LazyestStack

/**
 * @global $lg_stack 
 * the Lazyest Stack object
 */
global $lg_stack;

/**
 * lazyest_stack()
 * Do not start Lazyest Stack if the Lazyest Gallery plugin is not active
 * @return void
 */ 
function lazyest_stack() {
	global $lg_stack;
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
	if ( is_plugin_active( 'lazyest-gallery/lazyest-gallery.php' ) )
		$lg_stack = new LazyestStack;
}
add_action( 'init', 'lazyest_stack' );
?>