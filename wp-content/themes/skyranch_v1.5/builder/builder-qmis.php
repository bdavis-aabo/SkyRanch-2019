<?php

$_quickmoves = new WP_Query();
$_args = array(
  'post_type'       =>  'quickmoves',
  'post_status'     =>  'publish',
  'orderby'         =>  'menu_order',
  'order'           =>  'ASC',
  'posts_per_page'  =>  4,
  'hide_empty'      =>  1
);
$_quickmoves->query($_args);
?>

    <div class="builder-homes">
      <div class="row">
      <?php if($_quickmoves->have_posts()): while($_quickmoves->have_posts()): $_quickmoves->the_post(); ?>

        <?php the_title() ?>

      <?php endwhile;
        else:
      ?>
        <div class="col-12">
          <div class="builder-nohome">
            <h2>There are currently no quick move-in inventory homes available from this collection.<br/>Please call the sales office @ <?php echo get_field('homebuilder_phone') ?> for more information.</h2>
          </div>
        </div>
      <?php endif; wp_reset_query(); ?>
      </div>
    </div>
