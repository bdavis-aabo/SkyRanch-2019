<?php /* Template Name: Page - Builder Detail */ ?>

<?php get_header() ?>

  <section class="heroimage builder-hero">
    <?php echo get_the_post_thumbnail($post->ID, 'full', array('class' => 'img-fluid aligncenter')); ?>
  </section>

  <?php get_template_part('page/page-breadcrumbs') ?>

  <section class="builder-content">
    <?php while(have_posts()): the_post(); $_builderLogo = get_field('homebuilder_logo'); ?>
    <div class="container">
      <div class="row">
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
      </div>
    </div>

    <?php if(get_field('homebuilder_gallery') != ''): $_renderings = get_field('homebuilder_gallery'); ?>
    <div class="container-fluid">
      <div class="row ">
        <?php foreach($_renderings as $_rendering): ?>
          <div class="col-12 col-md-3">
            <div class="homebuilder-model"><img src="<?php echo $_rendering['url'] ?>" class="img-fluid" alt="<?php the_title() ?>" /></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <p class="builder-link">
      <a href="<?php echo get_field('homebuilder_link') ?>" class="btn outline-btn" target="_blank">
        Learn more about <?php the_title() ?> at <?php bloginfo('name') ?>
      </a>
    </p>
    <?php endwhile; ?>
  </section>

<?php get_footer() ?>
