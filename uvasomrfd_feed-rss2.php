<?php
/**
 * RSS2 Feed Template for displaying RSS2 Posts feed.
 *
 * @package WordPress
 */
ini_set('display_errors',1); 
error_reporting(E_ALL);
//FOR DEVELOPMENT ONLY -- TURN OFF IN PRODUCTION
function do_not_cache_feeds(&$feed) {
   $feed->enable_cache(false);
 }
 add_action( 'wp_feed_options', 'do_not_cache_feeds' );

	 

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php
	/**
	 * Fires at the end of the RSS root to add namespaces.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_ns' );
	?>
>

<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<?php
	/**
	 * Filter how often to update the RSS feed.
	 *
	 * @since 2.1.0
	 *
	 * @param string $duration The update period.
	 *                         Default 'hourly'. Accepts 'hourly', 'daily', 'weekly', 'monthly', 'yearly'.
	 */
	?>
	<?php
	$frequency = '1';
	/**
	 * Filter the RSS update frequency.
	 *
	 * @since 2.1.0
	 *
	 * @param string $frequency An integer passed as a string representing the frequency
	 *                          of RSS updates within the update period. Default '1'.
	 */
	?>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', $frequency ); ?></sy:updateFrequency>
	<?php
	/**
	 * Fires at the end of the RSS2 Feed Header.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_head');
	while( have_posts()) : the_post();
?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php the_permalink_rss() ?></link>
		<comments><?php comments_link_feed(); ?></comments>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<dc:creator><![CDATA[<?php the_author() ?>]]></dc:creator>
		<?php the_category_rss('rss2') ?>

		<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php if (get_option('rss_use_excerpt')) : ?>
		<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
<?php else : ?>
		<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
   <?php //get the variable for the content
   		$post_id = get_the_ID();  
		$output .= '<h2>'.get_post_meta($post_id, 'wpcf-first-name', true).' ';
		$middle = get_post_meta($post_id, 'wpcf-middle-name', true);
		if (!empty($middle)){
			$output .= get_post_meta($post_id, 'wpcf-middle-name', true).' ';
		}
		$output .= get_post_meta($post_id, 'wpcf-last-name', true).'</h2>'."\n";
		$output .= '<h4>Education</h4>'."\n";
		$degrees = get_post_meta($post_id, 'wpcf-degrees-earned', true);
		if (!empty($degrees)){
			$output .= '<ul>'."\n";
			$output .= get_post_meta($post_id, 'wpcf-degrees-earned', true)."\n";
			$output .= '</ul>'."\n";
		}
		$output .= '<h4 class="faculty">Primary Appointment</h4>'."\n";
		$output .= get_post_meta($post_id, 'wpcf-rank', true);
		$primary = wp_get_post_terms( $post_id, 'primary',array("fields" => "names"));
		if (!empty($primary)){
			$output .= ', '.$primary[0]."\n";
		}
		$output .= '<h4 class="faculty">Contact</h4>'."\n";
		$output .= 'Email: <a href="mailto:'. get_post_meta($post_id, 'wpcf-email', true). '">'.get_post_meta($post_id, 'wpcf-email', true).'</a>'."\n";
		$output .= '<h4>Research Interests</h4>'."\n";
		$output .= get_post_meta($post_id, 'wpcf-research-interest-title', true)."\n";
		$output .= '<h4>Research Description</h4>'."\n";
		$output .= get_the_content()."\n";
		$output .= '<h4 class="publications" id="'.get_post_meta($post_id, 'wpcf-curv_id', true).'">Selected Publications</h4>'."\n";
		$output .= '<ul class="publications" id="publications-'.get_post_meta($post_id, 'wpcf-curv_id', true).'"></ul>'."\n";
		$mycontent = html_entity_decode($output); ?>
	<?php //if ( strlen( $mycontent ) > 0 ) : ?>
		<content:encoded><![CDATA[<?php echo $mycontent; ?>]]></content:encoded>
	<?php //else : ?>
         <category>no category</category>
   
<?php endif; ?>
<?php rss_enclosure()."\n";?>
	<?php
	/**
	 * Fires at the end of each RSS2 feed item.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_item' );
	
	echo '</item>'."\n";
	endwhile; ?>
</channel>
</rss>
