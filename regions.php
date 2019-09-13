<?xml version="1.0" encoding="utf-8"?>
<svg version="1.1" id="russia_map" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1200 682.9" style="enable-background:new 0 0 1200 682.9;" xml:space="preserve">
    <g transform="scale(0.68) translate(0, 0)">
    <?php $query = new WP_Query(array('post_type' => 'region', 'posts_per_page' => -1)); while ($query->have_posts()) : $query->the_post(); ?>
        <a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php echo get_post_meta($post->ID, 'regioncode', 1); ?></a>
    <?php endwhile; wp_reset_postdata(); ?>
    </g>
</svg>