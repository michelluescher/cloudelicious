<?php
/**
 * The template for displaying search forms in Generate
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<form method="get" id="searchform" class="searchform clearfix" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<span class="screen-reader-text"><?php apply_filters( 'generate_search_label', _ex( 'Search for:', 'label', 'generatepress' ) ); // WPCS: XSS ok, sanitization ok. ?></span>
		<label>
			<input type="button" id="searchsubmit" value="" />
			<input type="search" class="search-field" placeholder="<?php esc_html_e('Types...', 'optimize'); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php esc_attr( apply_filters( 'generate_search_label', _ex( 'Search for:', 'label', 'generatepress' ) ) ); // WPCS: XSS ok, sanitization ok. ?>">
	<!--<input type="submit" class="search-submit" value="<?php echo apply_filters( 'generate_search_button', _x( 'Search', 'submit button', 'generatepress' ) ); // WPCS: XSS ok, sanitization ok. ?>">-->
	</label>
</form>
