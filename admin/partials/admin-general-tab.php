<h3>General</h3>
<p>General settings go here</p>

<?php
if ( $_POST['action'] === 'update_email_notif_settings' ) {
  $email_notif_settings = array(
    'logo' => $_POST['email_notif_logo'],
    'from_name' => $_POST['email_notif_from_name'],
    'from_email' => $_POST['email_notif_from_email'],
  );
  update_option( 'email_notif_settings', json_encode( $email_notif_settings ) ); ?>
  <div class="notice notice-success is-dismissible">
      <p><?php _e( 'Email Notification Settings Updated.' ); ?></p>
  </div>
  <?php
}
?>

<form method="post" action="<?php echo get_site_url(); ?>/wp-admin/options-general.php?page=saucy-followers&tab=general" > 
  <?php
  $email_notif_settings = json_decode( get_option( 'email_notif_settings' ) );
  ?>
	<h4 style="margin-top: 0;" >Update Email Notification Settings</h4>
	
	<a href="<?php echo get_site_url(); ?>/wp-admin/options-general.php?page=saucy-followers&tab=email-template" class="button button-default" id="showEmailTemplateButton">Show Email Template View</a>

	<table class="form-table">
		<tbody>
			<tr>
				<th><label id="email_notif_logo" >Logo:</label></th>
				<td>
					<input type="text" name="email_notif_logo" value="<?php echo $email_notif_settings->logo; ?>" >
				</td>
			</tr>
			<tr>
				<th><label id="email_notif_from_name" >From Name:</label></th>
				<td>
					<input type="text" name="email_notif_from_name" value="<?php echo $email_notif_settings->from_name; ?>" >
				</td>
			</tr>
			<tr>
				<th><label id="email_notif_from_email" >From Email:</label></th>
				<td>
			    <input type="text" name="email_notif_from_email" value="<?php echo $email_notif_settings->from_email; ?>" >     
			  </td>
			</tr>
		</tbody>
	</table>

	<button type="submit" name="action" value="update_email_notif_settings" class="button button-primary" >Save Changes</button>
	
</form>
