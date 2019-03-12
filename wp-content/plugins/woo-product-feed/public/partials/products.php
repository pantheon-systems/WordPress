<?php
/**
 * XML template for displaying WooCommerce products.
 */

header('Content-Type: ' . feed_content_type('products') . '; charset=' . get_option('blog_charset'), true);

echo '<?xml version="1.0" encoding="'. get_option('blog_charset') . '"?>';
?>
<channel>
  <title><?php _e('Products'); ?></title>
  <lastBuildDate><?php
    $date = get_lastpostmodified( 'GMT' );
    echo $date ? mysql2date( 'r', $date, false ) : date( 'r' );
  ?></lastBuildDate>
  <language><?php bloginfo_rss( 'language' ); ?></language>
<?php while( have_posts()) : the_post(); ?>
  <item>
    <title><?php the_title_rss() ?></title>
    <link><?php the_permalink_rss() ?></link>
    <sku><?php $this->the_sku() ?></sku>
    <regularPrice><?php $this->the_regular_price() ?></regularPrice>
    <salePrice><?php $this->the_sale_price() ?></salePrice>
    <image><?php the_post_thumbnail_url( 'large' ); ?></image>
    <brand><?php $this->the_brand() ?></brand>
    <currency><?php $this->the_currency() ?></currency>
    <category><?php $this->the_category() ?></category>
    <description><![CDATA[<?php $this->the_description() ?>]]></description>
    <content><![CDATA[<?php $this->the_content() ?>]]></content>
  </item>
<?php endwhile; ?>
</channel>
