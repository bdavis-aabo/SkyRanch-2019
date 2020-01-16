<?php /* Template Name: Page - Quick Move-Ins */ ?>

<?php
  $_terms = get_terms('builder', 'orderby=title&hide_empty=0');
?>


<?php get_header() ?>

  <?php get_template_part('page/page-breadcrumbs') ?>

  <section class="community-content">
    <div class="row">
      <div class="col-12">
        <div class="page-contents">
          <?php the_content() ?>
        </div>
      </div>
    </div>
  </section>

    <?php foreach($_terms as $_term): ?>
    <section class="builder-quickmoves" id="<?php echo $_term->slug . '-qmi' ?>">
      <?php $_builderPage = new WP_Query(array('post_type'=>'page','post_status'=>'publish','pagename'=>$_term->slug));
        while($_builderPage->have_posts()): $_builderPage->the_post(); $_builderLogo = get_field('homebuilder_logo');
      ?>
      <div class="builder-information-container">
        <div class="builder-logo">
          <img src="<?php echo $_builderLogo['url'] ?>" alt="<?php the_title() ?>" class="aligncenter img-fluid" />
        </div>
        <div class="builder-address">
          <h3><?php the_title() ?></h3>
          <?php echo get_field('homebuilder_address'); ?>
          <strong>Sales office is Open:</strong> <?php echo get_field('homebuilder_hours') ?><br/>
          <strong class="blue-txt"><?php echo get_field('homebuilder_phone') ?></strong>
        </div>
      </div>
    <?php endwhile; wp_reset_query(); ?>

      <?php
      $_quickmoves = new WP_Query();
      $_args = array(
        'post_type'       =>  'quickmoves',
        'post_status'     =>  'publish',
        'builder'         =>  $_term->slug,
        'orderby'         =>  'menu_order',
        'order'           =>  'ASC',
        'posts_per_page'  =>  3,
        'hide_empty'      =>  1
      );
      $_quickmoves->query($_args);
      ?>

      <div class="builder-homes">
        <div class="row">
        <?php if($_quickmoves->have_posts()): while($_quickmoves->have_posts()): $_quickmoves->the_post(); $_home = get_field('qmi_image'); ?>
          <div class="col-12 col-md-4">
            <div class="builder-home">
              <img src="<?php echo $_home['url'] ?>" alt="<?php the_title() ?>" class="img-fluid aligncenter" />
              <h2 class="home-name"><?php echo get_field('qmi_floorplan') ?></h2>
              <span class="address-price"><?php echo get_field('qmi_address') ?> | <strong><?php echo '$' . get_field('qmi_price') ?></strong></span>
              <p><?php echo 'Available: ' . get_field('qmi_available') ?></p>
              <p class="details">
                <?php echo get_field('qmi_square_footage') . ' sq ft | ' . get_field('qmi_bedrooms') . ' beds | ' . get_field('qmi_bathrooms') . ' bath<br/>' .
									get_field('qmi_garage') ?>
              </p>
							<a href="<?php echo get_field('qmi_link') ?>" class="builder-btn btn outline-btn" target="_blank">View This Home</a>
            </div>
          </div>
        <?php endwhile; else: ?>
          <div class="col-12">
            <div class="builder-nohome">
              <h2>There are currently no quick move-in inventory homes available from <?php echo $_term->name ?>.<br/>Please call the sales office @ <?php echo get_field('homebuilder_phone') ?> for more information.</h2>
            </div>
          </div>
          <?php endif; wp_reset_query(); ?>
        </div>
      </div>

    </section>
    <?php endforeach; ?>








<?php get_footer() ?>
