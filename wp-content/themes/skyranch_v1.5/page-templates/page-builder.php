<?php /* Template Name: Page - Builder Detail */ ?>

<?php get_header() ?>

  <?php get_template_part('page/page-breadcrumbs') ?>

  <div class="row">
    <section class="builder-content">
    <?php while(have_posts()): the_post(); $_builderLogo = get_field('homebuilder_logo'); ?>
      <div class="col-12">
        <div class="builder-contentarea">
          <img src="<?php echo $_builderLogo['url'] ?>" alt="<?php the_title() ?>" class="aligncenter img-fluid" />
          <div class="builder-details">
            <?php the_content() ?>
            <p class="home-details">
              <?php echo get_field('homebuilder_pricing') . ' | ' . get_field('homebuilder_details') ?>
            </p>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
    </section>
  </div>

  <div class="row">

    <?php if(do_shortcode('[envira-gallery slug="'.$post->post_name.'"]') != ''): ?>
    <div class="col-12">
      <div class="builder-gallery-contents" id="gallery">
        <h1><?php the_title(); echo ' Photo Gallery'; ?></h1>
        <?php echo do_shortcode('[envira-gallery slug="'.$post->post_name.'"]') ?>
      </div>
    </div>
    <?php else: echo 'empty'; endif; ?>


      <div class="col-12">
      <p class="builder-link">
        <a href="<?php echo get_field('homebuilder_link') ?>" class="btn outline-btn" target="_blank">
          Learn more about <?php the_title() ?> at <?php bloginfo('name') ?>
        </a>
      </p>
      </div>
  </div>

<?php get_footer() ?>
