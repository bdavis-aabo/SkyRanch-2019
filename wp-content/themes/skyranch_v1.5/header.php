<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>
<?php
  if(function_exists('is_tag') && is_tag()) {
      echo 'Tag Archive for &quot;' . $tag . '&quot; - ';
  } elseif(is_archive()) {
      wp_title(''); echo ' Archive - ';
  } elseif(is_search()) {
      echo 'Search for &quot;' . wp_specialchars($s) . '&quot; - ';
  } elseif(!(is_404()) && (is_single()) || (is_page()) && !(is_front_page())) {
      wp_title('');
  } elseif(is_404()) {
      echo 'Page Not Found - ';
  } elseif(is_front_page()){
      bloginfo('name');
  } elseif(is_home()){
      echo 'Latest News - '; bloginfo('name');
  }
?>
</title>

<?php //seo plugin grabs page title ?>

<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<link rel="shortcut icon" href="<?php bloginfo('template_directory') ?>/assets/images/favicon.png" type="image/x-icon" />

<?php /* Load CSS in functions.php */ ?>

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<?php require_once('assets/_inc/ga-include.php') ?>

<?php wp_head() ?>
</head>
<body <?php body_class(); ?>>
<?php require_once('assets/_inc/ga-body.php') ?>

<div class="container main-wrapper">
  <div class="row">
    <header class="header">
    <div class="header-mask">
      <a href="<?php bloginfo('url') ?>" title="<?php bloginfo('name') ?>" class="hero-logo">
        <img src="<?php bloginfo('template_directory') ?>/assets/images/skyranch-logo.png" class="logo img-fluid" />
      </a>
    </div>

    <nav class="navbar navbar-light" role="navigation">
      <button class="navbar-toggle" type="button">
        <span class="nav-iconbar"></span>
        <span class="nav-iconbar"></span>
        <span class="nav-iconbar"></span>
        <span class="nav-iconbar"></span>
      </button>

      <?php wp_nav_menu(array(
        'depth'           =>  2,
        'container'       =>  'div',
        'container_class' =>  'navbar-default',
        'container_id'    =>  'main-navbar',
        'menu_class'      =>  'navbar-nav',
        'fallback_cb'     =>  'WP_Bootstrap_Navwalker::fallback',
        'walker'          =>  new WP_Bootstrap_Navwalker(),
      ));
      ?>
    </nav>

    <?php if(is_front_page()): get_template_part('home/homepage-slider'); elseif(is_home()): ?>
      <section class="heroimage builder-hero">
        <img src="<?php bloginfo('template_directory') ?>/assets/images/news-heroimage.jpg" class="img-fluid" alt="<?php the_title() ?>">
      </section>
    <?php else: ?>
      <section class="heroimage builder-hero">
        <?php echo get_the_post_thumbnail($post->ID, 'full', array('class' => 'img-fluid aligncenter')); ?>
      </section>
    <?php endif; ?>
  </header>
  </div>
