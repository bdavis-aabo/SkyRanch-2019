<?php /* Template Name: Page - Homepage */ ?>
<?php
  $_builders = new WP_Query();
  $_builderParent = get_page_by_path('homebuilders');
  $_args = array(
    'post_type'       => 'page',
    'post_status'     => 'publish',
    'posts_per_page'  => -1,
    'post_parent'     =>  $_builderParent->ID,
    'order'           =>  'ASC',
    'orderby'         =>  'menu_order'
  );
  $_builders->query($_args);
?>

<?php get_header() ?>

<?php while(have_posts()): the_post() ?>
  <div class="row">
    <section class="homepage-content">
      <div class="col-12 col-md-10 offset-md-1">
        <div class="homepage-contentarea">
          <h1 class="peach-txt script-txt"><?php the_title() ?></h1>
          <?php the_content() ?>
        </div>
      </div>
    </section>
  </div>

  <?php if($_builders->have_posts()): ?>
  <section class="homepage-builders">
    <div class="container">
      <div class="row">
      <?php while($_builders->have_posts()): $_builders->the_post(); $_builderLogo = get_field('homebuilder_logo') ?>
        <div class="col-12 col-sm-6 col-md-4">
          <div class="builder">
            <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
              <img src="<?php echo $_builderLogo['url'] ?>" class="img-fluid alignnone hombuilder-logo" alt="<?php the_title() ?>" />
              <strong><?php echo get_field('homebuilder_pricing') ?></strong>
            </a>
          </div>
        </div>
      <?php endwhile; wp_reset_query(); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if(have_rows('homepage_hero_images')): $_i = 0; ?>
  <div class="row">
    <section class="homepage-heroimages">
      <?php while(have_rows('homepage_hero_images')): the_row(); $_heroImage = get_sub_field('hero_image'); $_mobileImage = get_sub_field('hero_mobile_image'); ?>
      <div class="homepage-mobile-hero">
        <img src="<?php echo $_mobileImage['url'] ?>" class="alignnone img-fluid" />
      </div>
      <div class="homepage-heroimage">
        <img src="<?php echo $_heroImage['url'] ?>" alt="<?php the_title() ?>" class="alignnone img-fluid" />
        <div class="homepage-heroimage-contents <?php echo get_sub_field('hero_alignment').'-align image-'.$_i ?>"><?php echo get_sub_field('hero_content') ?></div>
      </div>
      <?php $_i++; endwhile; ?>
    </section>
  </div>
  <?php endif; // end homepage_hero_images (features 'within reach') ?>
<?php endwhile; // endwhile page_query ?>

<?php if(is_front_page() || is_page('life-within-reach')): ?>
  <div class="row">
    <section class="homepage-contactform" id="contact">
      <div class="col-12">
        <div class="form-container">
          <h1 class="peach-txt script-txt">stay connected</h1>
          <p>Be the first to know the latest news and happenings at Sky Ranch.</p>
        </div>
        <div class="contact-form-container">
          <?php echo do_shortcode('[contact-form-7 id="142" title="Builder Contact Form"]'); ?>
        </div>
      </div>
    </section>
  </div>
<?php endif; ?>







<?php get_footer() ?>
