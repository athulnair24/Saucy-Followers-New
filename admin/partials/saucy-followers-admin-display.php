<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://mydigitalsauce.com/
 * @since      0.1.0
 *
 * @package    Saucy_Followers
 * @subpackage Saucy_Followers/admin/partials
 */

$tab = ( ! empty( $_GET['tab'] ) ) ? esc_attr( $_GET['tab'] ) : 'general';

?>

<style>
.saucy-followers-admin-display-wrap ul {
	list-style-type: disc; padding-left: 16px;
}
</style>

<div class="saucy-followers-admin-display-wrap wrap">
	<h1>Saucy Followers - General</h1>
	<p>Allows users to follow other users and see updates from users they follow.</p>

	<h2 class="nav-tab-wrapper">
			<a href="<?php echo get_site_url(); ?>/wp-admin/options-general.php?page=saucy-followers&tab=general" class="nav-tab <?php echo ( $tab === 'general' ) ? 'nav-tab-active': ''; ?>">General</a>
			<a href="<?php echo get_site_url(); ?>/wp-admin/options-general.php?page=saucy-followers&tab=shortcodes" class="nav-tab <?php echo ( $tab === 'shortcodes' ) ? 'nav-tab-active': ''; ?>">Shortcodes</a>
			<a href="<?php echo get_site_url(); ?>/wp-admin/options-general.php?page=saucy-followers&tab=help" class="nav-tab">Help</a>
			<!-- <a href="/wp-admin/options-general.php?page=saucy-followers&tab=misc class="nav-tab">Misc</a> -->
	</h2>

	<?php if ( $tab === 'general' ) { ?>

		<?php require plugin_dir_path( __FILE__ ) . '/admin-general-tab.php'; ?>

	<?php } else if ( $tab === 'shortcodes' ) { ?>

		<?php require plugin_dir_path( __FILE__ ) . '/admin-shortcodes-tab.php'; ?>

	<?php } else { ?>

		<?php require plugin_dir_path( __FILE__ ) . '/admin-help-tab.php'; ?>

	<?php } ?>

</div>