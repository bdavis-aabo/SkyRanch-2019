<?php
  $_slides = new WP_Query();
  $_args = array(
    'post_type' => 'homepage_slides',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'order' => 'ASC',
    'orderby' => 'menu_order'
  );
  $_slides->query($_args);

?>


  <section class="heroimage homepage-slider">
    <?php //if($_slides->have_posts()): ?>
    <div id="homepage-carousel" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
        <?php $_s = 0; while($_slides->have_posts()): $_slides->the_post(); ?>
          <li data-target="#homepage-carousel" data-slide-to="<?php echo $_s ?>" <?php if($_s == 0): echo 'class="active"'; endif; ?>></li>
        <?php $_s++; endwhile; rewind_posts(); ?>
      </ol>

      <div class="carousel-inner">
        <?php $_s = 0; while($_slides->have_posts()): $_slides->the_post(); ?>
          <div class="carousel-item <?php if($_s == 0): echo 'active'; endif; ?>">
            <?php echo get_the_post_thumbnail($post->ID, 'full', array('class' => 'img-fluid aligncenter')); ?>
          </div>
        <?php $_s++; endwhile; $_s = 0;  ?>
      </div>


    </div>
    <?php //endif; ?>
  </section>
