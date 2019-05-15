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

  <?php get_template_part('home/homepage-slider') ?>

<?php while(have_posts()): the_post() ?>

  <section class="homepage-content">
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-10 offset-md-1">
          <div class="homepage-contentarea">
            <h1 class="peach-txt script-txt"><?php the_title() ?></h1>
            <?php the_content() ?>
          </div>
        </div>
      </div>
    </div>
  </section>

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
  <?php endif; ?>
<?php endwhile; ?>

<?php if(is_front_page() || is_page('life-within-reach')): ?>
  <section class="homepage-contactform">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="form-container">
            <h1 class="peach-txt script-txt">stay connected</h1>
            <p>Be the first to know the latest news and happenings at Sky Ranch.</p>
            <?php echo do_shortcode('[contact-form-7 id="5" title="Contact Form"]') ?>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>








<?php get_footer() ?>