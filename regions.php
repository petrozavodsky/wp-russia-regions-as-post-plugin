<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<svg version="1.1" id="russia_map" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
     y="0px" viewBox="0 0 1200 682.9" style="enable-background:new 0 0 1200 682.9;" xml:space="preserve">
    <g transform="scale(0.68) translate(0, 0)">
        <?php foreach (get_posts(array('post_type' => 'region', 'posts_per_page' => -1)) as $post): ?>
            <a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>"
               title="<?php echo esc_attr(get_the_title(get_the_ID())); ?>">
                <?php echo esc_html(get_post_meta(get_the_ID(), 'regioncode', true)); ?>
            </a>
        <?php endforeach; ?>
        <?php wp_reset_postdata(); ?>
    </g>
</svg>
