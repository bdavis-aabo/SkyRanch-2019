<?php //DEFAULT PAGE TEMPLATE ?>

<?php get_header() ?>

  <section class="heroimage page-hero">
    <?php echo get_the_post_thumbnail($post->ID, 'full', array('class' => 'img-fluid')); ?>
  </section>

  <?php get_template_part('page/page-breadcrumbs') ?>

  <?php while(have_posts()): the_post() ?>
  <section class="page-content">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <?php the_content() ?>

          <?php if(is_page('contact-us')): ?>
            <div class="contact-form-container">
              <?php echo do_shortcode('[contact-form-7 id="142" title="Builder Contact Form"]'); ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <?php if(is_page('location')): ?>
  <section class="location-links">
    <a href="<?php bloginfo('template_directory') ?>/assets/images/skyranch-area-amenity-map.pdf" target="_blank">
      <img src="<?php bloginfo('template_directory') ?>/assets/images/surrounding-area-map.jpg" class="img-fluid aligncenter" />
    </a>
    <p class="location-subcontent">
      <a href="<?php bloginfo('template_directory') ?>/assets/images/skyranch-area-amenity-map.pdf" target="_blank" class="btn outline-btn">Download Area Map</a>
    </p>

    <p class="location-subcontent">
      <a href="google directions" target="_blank"><img src="<?php bloginfo('template_directory') ?>/assets/images/driving-map.jpg" class="img-fluid aligncenter" /></a><br/>
      <a href="google directions" target="_blank" class="btn outline-btn">Driving Directions</a>
    </p>
  </section>
  <?php endif; ?>
<?php endwhile; ?>

<?php get_footer() ?>
