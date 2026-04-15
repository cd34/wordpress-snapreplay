<?php
/*
Plugin Name: SnapReplay
Plugin URI: https://github.com/cd34/wordpress-snapreplay
Description: Display latest Event/Venue picture in sidebar via live stream
Author: Chris Davies
Version: 0.3
Author URI: https://cd34.com/
*/

class SnapReplay_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'snapreplay_widget',
			'SnapReplay Widget',
			array( 'description' => 'Display live SnapReplay event/venue stream' )
		);
	}

	public function widget( $args, $instance ) {
		$stream_id = absint( get_option( 'snapreplay-stream-id', 0 ) );
		if ( ! $stream_id ) {
			return;
		}
		echo $args['before_widget'];
		echo $args['before_title'];
		echo '<a href="https://snapreplay.com/event_id/' . esc_attr( $stream_id ) . '">SnapReplay Live Stream</a>';
		echo $args['after_title'];
		echo '<div id="snapreplay-placeholder"></div>';
		echo $args['after_widget'];
	}
}

function snapreplay_register_widget() {
	register_widget( 'SnapReplay_Widget' );
}
add_action( 'widgets_init', 'snapreplay_register_widget' );

function snapreplay_admin_menu() {
	add_options_page(
		'SnapReplay Widget Options',
		'SnapReplay Widget',
		'manage_options',
		'sr-widget-options',
		'snapreplay_options_page'
	);
}
add_action( 'admin_menu', 'snapreplay_admin_menu' );

function snapreplay_admin_init() {
	register_setting( 'snapreplay', 'snapreplay-stream-id', array(
		'type'              => 'integer',
		'sanitize_callback' => 'absint',
		'default'           => 0,
	) );
}
add_action( 'admin_init', 'snapreplay_admin_init' );

function snapreplay_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="wrap">
		<h2>SnapReplay Widget Setup</h2>
		<p>
			<a href="https://snapreplay.com/">SnapReplay.com</a> is a site that allows
			you to crowdsource photos from events. This plugin allows you to select
			an Event or Venue and display the last updated stream item live in your
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
					<td><input type="text" name="snapreplay-stream-id" value="<?php echo esc_attr( get_option( 'snapreplay-stream-id', '' ) ); ?>" /></td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
			</p>
		</form>
	</div>
	<?php
}

function snapreplay_enqueue_scripts() {
	$stream_id = absint( get_option( 'snapreplay-stream-id', 0 ) );
	if ( ! $stream_id || ! is_active_widget( false, false, 'snapreplay_widget' ) ) {
		return;
	}

	wp_enqueue_script(
		'snapreplay-socketio',
		'https://stream.snapreplay.com/socket.io/socket.io.js',
		array(),
		null,
		true
	);

	wp_add_inline_script( 'snapreplay-socketio', sprintf(
		'(function() {
			var streamId = %s;
			var socket = io.connect("https://stream.snapreplay.com");
			socket.emit("newchan", {"chan": streamId});
			socket.on("s-" + streamId, function(data) {
				var el = document.getElementById("snapreplay-placeholder");
				if (!el || !data || !data.data) return;
				var d = data.data;
				if (d.content_type === "text") {
					el.textContent = "";
					var text = document.createTextNode(
						(d.display_name || "") + " says, " + (d.content || "")
					);
					el.appendChild(text);
				}
				if (d.content_type === "image" && d.file_name) {
					el.textContent = "";
					var img = document.createElement("img");
					img.src = "https://cdn.snrly.com/pics/" + encodeURIComponent(d.file_name);
					img.alt = "SnapReplay image";
					el.appendChild(img);
				}
			});
		})();',
		wp_json_encode( $stream_id )
	) );
}
add_action( 'wp_enqueue_scripts', 'snapreplay_enqueue_scripts' );
