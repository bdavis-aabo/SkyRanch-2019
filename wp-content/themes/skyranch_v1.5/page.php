<?php //DEFAULT PAGE TEMPLATE ?>

<?php get_header() ?>

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
      <a href="https://www.google.com/maps/place/39%C2%B044'21.3%22N+104%C2%B039'40.6%22W/@39.7392431,-104.6634787,17z/data=!3m1!4b1!4m6!3m5!1s0x0:0x0!7e2!8m2!3d39.7392388!4d-104.6612897" target="_blank"><img src="<?php bloginfo('template_directory') ?>/assets/images/driving-map.jpg" class="img-fluid aligncenter" /></a><br/>
      <a href="https://www.google.com/maps/place/39%C2%B044'21.3%22N+104%C2%B039'40.6%22W/@39.7392431,-104.6634787,17z/data=!3m1!4b1!4m6!3m5!1s0x0:0x0!7e2!8m2!3d39.7392388!4d-104.6612897" target="_blank" class="btn outline-btn">Driving Directions</a>
    </p>
  </section>
  <?php endif; ?>
<?php endwhile; ?>

<?php get_footer() ?>
