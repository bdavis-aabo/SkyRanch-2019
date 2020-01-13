<?php
  $_builders = new WP_Query();
  $_builderParent = get_page_by_path('homebuilders');
  $_args = array(
    'post_type'       => 'page',
    'post_status'     => 'publish',
    'posts_per_page'  => -1,
    'post__in'        =>  array(69,71,73), //may need to update
    'order'           =>  'ASC',
    'orderby'         =>  'menu_order'
  );
  $_builders->query($_args);
?>

  <div class="row">
    <div class="col-12">
      <?php if(is_active_sidebar('footer-address')): dynamic_sidebar('footer-address'); endif; ?>
    </div>
  </div>
  <div class="row">
    <footer class="footer ltblue-bg">
      <div class="row">
      <?php while($_builders->have_posts()): $_builders->the_post(); $_reverseLogo = get_field('homebuilder_logo_reverse'); ?>
        <div class="col-x">
          <div class="builder">
            <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
              <img src="<?php echo $_reverseLogo['url'] ?>" alt="<?php the_title() ?>" class="img-fluid aligncenter" />
            </a>
          </div>
        </div>
      <?php endwhile; ?>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="copyright">
            <?php echo '&copy;'.date('Y').' Pure Cycle'; ?> All pricing, product specifications, amenities and landscaping is subject to change without prior notice.
            <img src="<?php bloginfo('template_directory') ?>/assets/images/eho-icon.png" class="eho-icon alignleft" />
          </div>
        </div>
      </div>
    </footer>
  </div>


</div> <?php //end container from header.php ?>

<?php wp_footer() ?>
</body>
</html>
