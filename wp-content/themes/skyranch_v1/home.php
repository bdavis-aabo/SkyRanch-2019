<?php /* Template Name: Page - News */ ?>

<?php get_header() ?>

  <section class="heroimage page-hero">
    <img src="<?php bloginfo('template_directory') ?>/assets/images/news-heroimage.jpg" class="img-fluid" alt="<?php the_title() ?>">
  </section>

  <?php get_template_part('page/page-breadcrumbs') ?>

  <section class="blog-articles">
    <div class="container">
    <?php while(have_posts()): the_post(); ?>
      <div class="row">
        <div class="col-12 col-md-8 offset-md-2">
          <article class="news-article" id="post-<?php $post->ID ?>">
            <?php echo get_the_post_thumbnail($post->ID, 'full', array('class' => 'img-fluid')); ?>
            <h2 class="article-title"><a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a></h2>
            <p class="post-date"><?php echo get_the_date() ?></p>
            <?php the_content() ?>
          </article>
        </div>
      </div>
    <?php endwhile; ?>
    </div>
  </section>

<?php get_footer() ?>
