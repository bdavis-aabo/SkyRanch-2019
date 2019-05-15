<?php /* Template Name: Page - Community */ ?>

<?php get_header() ?>

  <section class="heroimage community-hero">
    <?php echo get_the_post_thumbnail($post->ID, 'full', array('class' => 'img-fluid aligncenter')); ?>
  </section>

  <?php get_template_part('page/page-breadcrumbs') ?>

  <?php while(have_posts()): the_post() ?>
  <section class="community-content">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <?php the_content() ?>
        </div>
      </div>
    </div>
  </section>

  <section class="community-slider">
    <div id="community-slides" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">
      <?php $_s = 0; while(have_rows('community_slider')): the_row(); $_slideImage = get_sub_field('slide_image'); ?>
        <div class="carousel-item <?php if($_s == 0): echo 'active'; endif; ?>">
          <img src="<?php echo $_slideImage['url'] ?>" class="img-fluid" />
        </div>
      <?php $_s++; endwhile; ?>
      </div>

      <ol class="carousel-indicators">
      <?php $_s = 0; while(have_rows('community_slider')): the_row(); $_slideImage = get_sub_field('slide_image'); ?>
        <li data-target="#community-slides" data-slide-to="<?php echo $_s ?>" <?php if($_s == 0): ?>class="active"<?php endif; ?>></li>
      <?php $_s++; endwhile; ?>
      </ol>
    </div>
  </section>

  <section class="community-education">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <?php echo get_field('community_education') ?>
        </div>
      </div>
    </div>
  </section>

  <?php endwhile; ?>

<?php get_footer() ?>
