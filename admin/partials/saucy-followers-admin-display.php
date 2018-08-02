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
		<h3>General</h3>
		<p>General settings go here</p>
	<?php } else { ?>
		<h3>Shortcodes</h3>
		<p>Available shortcodes</p>

		<h4>Saucy Follow Links - Shortcode</h4>
		<pre><code>[saucy_follow_links]</code></pre>
		<p>Shows the links to follow/unfollow a user. <!-- Modify this shortcodes output with parameters: --></p>
		<hr/>

		<h4>Saucy Following Feed - Shortcode</h4>
		<pre><code>[saucy_following_feed]</code></pre>
		<p>Outputs a feed of the users you follow. Modify this shortcodes output with parameters:</p>
		<ul>
			<li>
				<b>posttype</b>
				<ul>
					<li>(default) post</li>
					<li>all</li>
					<li>page</li>
					<li>products</li>
					<li>ect...</li>
				</ul>
			</li>
		</ul>
		<ul>
			<li>
				<b>style</b>
				<ul>
					<li>(default) list</li>
					<li>grid</li>
				</ul>
			</li>
		</ul>
		<ul>
			<li>
				<b>showauthor</b>
				<ul>
					<li>(default) false</li>
					<li>true</li>
				</ul>
			</li>
		</ul>
		<pre><code>[saucy_following_feed posttype="all" style="grid" showauthor="true"]</code></pre>
	<?php } ?>

</div>