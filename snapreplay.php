<?php
/*
Plugin Name: SnapReplay
Plugin URI: http://code.google.com/p/cd34-wordpress/wiki/SnapReplay
Description: Display latest Event/Venue picture in sidebar
Author: Chris Davies
Version: 0.2
Author URI: http://cd34.com/

Update Event Title on page

*/

function widget_sr_control() {
?>
<?php
}

function widget_sr($args) {
?>
<h3><a href="http://snapreplay.com/event_id/<?php echo get_option('snapreplay-stream-id'); ?>">SnapReplay Live Stream</a></h3>
<div id="top_placeholder"></div>
<?php
}
	
function widget_sr_init() {
  if ( !function_exists('register_sidebar_widget') ||
       !function_exists('register_widget_control') ) {
    return;
  }

  register_sidebar_widget('SnapReplay Widget', 'widget_sr');

}

function sr_widget_menu() {
  add_options_page('SnapReplay Widget Options', 'SnapReplay Widget', 'manage_options', 'sr-widget-options', 'sr_widget_options');
}

function sr_widget_options() {
    if (!current_user_can('manage_options'))  {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }
?>

<div class="wrap">
<h2>SnapReplay Widget setup</h2>
<p>
<a href="http://snapreplay.com/">SnapReplay.com</a> is a site that allows
you to crowdsource photos from events. This plugin allows you to select
an Event or Venue and display the last updated stream item Live in your
sidebar.
</p>
<p>
You need to configure the Event or Venue ID so that the widget knows
which stream to follow.
</p>
<form method="post" action="options.php">
    <?php settings_fields( 'snapreplay' ); ?>

    <table class="form-table">
        <tr valign="top">
        <th scope="row">Event or Venue ID</th>
        <td><input type="text" name="snapreplay-stream-id" value="<?php echo get_option('snapreplay-stream-id'); ?>" /></td>
        </tr>
    </table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
<?php
}

function sr_widget_init() {
  register_setting('snapreplay', 'snapreplay-stream-id', 'intval');
  update_option('snapreplay-stream-id', 1);
}

add_action('init', 'widget_sr_init');

add_action('admin_menu', 'sr_widget_menu');
add_action('admin_init', 'sr_widget_init');

add_action('wp_footer', 'sr_jscript');

function sr_jscript() {
?>
<script type="text/javaScript" src="http://stream.snapreplay.com/socket.io/socket.io.js"></script>
<script type="text/javaScript">
<!--
  var socket = io.connect('http://stream.snapreplay.com');
  socket.emit('newchan', {'chan':<?php echo get_option('snapreplay-stream-id'); ?>});
  socket.on('s-<?php echo get_option('snapreplay-stream-id'); ?>', function (data) {
    if (data['data'].content_type == "text") {
      content = data['data'].display_name + ' says, ' + data['data'].content;
      document.getElementById('top_placeholder').innerHTML=content;
    }
    if (data['data'].content_type == "image") {
      content='<img src="http://csnap.colocdn.com/pics/' + data['data'].file_name + '"/>';
      document.getElementById('top_placeholder').innerHTML=content;
    }
  });
// -->
</script>
<?php
}
