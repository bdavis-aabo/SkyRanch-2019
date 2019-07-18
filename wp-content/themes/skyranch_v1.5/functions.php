<?php
  // Load Parent Styles
  function skyranch_enqueue_styles(){
    wp_enqueue_style('parent-style', get_template_directory_uri().'/assets/css/main.min.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri().'/assets/css/main.min.css', array('parent-style'));
  }
  add_action('wp_enqueue_scripts','skyranch_enqueue_styles', PHP_INT_MAX);


  function skyranch_enqueue_scripts() {
    wp_enqueue_script( 'jquery.extras.min', get_template_directory_uri() . '/assets/js/main.min.js', 'jquery', '', true );
  }
  add_action( 'wp_enqueue_scripts', 'skyranch_enqueue_scripts' );

?>
