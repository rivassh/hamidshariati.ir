<?php
/**
 * The template for displaying search form
 *
 *
 * @package WhiteBlack Classic
 */
?>

<form role="search" method="get" class="stg-search-form" action="<?php echo esc_url(home_url('')); ?>">
    <label class="screen-reader-text" for="stg-search-input"><?php _e('Search for:', 'whiteblack-classic'); ?></label>
    <input type="search" id="stg-search-input" class="stg-search-field" placeholder="<?php echo esc_attr_x('Search...', 'placeholder', 'whiteblack-classic'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    <button type="submit" class="stg-search-submit">
        <span class="screen-reader-text"><?php echo _x('Search', 'submit button', 'whiteblack-classic'); ?></span>
		<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/search_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg"  alt="<?php _e( 'Search icon', 'whiteblack-classic' ); ?>">
    </button>
</form>