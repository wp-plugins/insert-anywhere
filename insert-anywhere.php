<?php
/*
Plugin Name: Insert Codes
Plugin URI: http://jiehan.org
Description: Insert your code into the place you want, with no need of editing themes!
Version: 0.2
Author: Jiehan Zheng
Author URI: http://jiehan.org
*/

// Requires a very important file run earlier
require_once( ABSPATH . 'wp-includes/pluggable.php' );

// Load options
$code['head']			 = get_option('ia_head'); //wp_head
$code['post_top'] 		 = get_option('ia_post_top');
// $code['post_middle']	 = get_option('ia_post_middle'); //planned
$code['post_bottom']	 = get_option('ia_post_bottom');
$code['foot']			 = get_option('ia_foot'); //wp_footer

$only_single['post']	= get_option('ia_single');

// Add codes when page loading
function add_head() {
	global $code;
	echo $code['head'];
}
function add_post() {
	global $code;
	global $only_single;
	if($only_single['post']) {
		if(is_single())
			echo $code['post_top'] . get_the_content() . $code['post_bottom'];
		else echo get_the_content();
	} else echo $code['post_top'] . get_the_content() . $code['post_bottom'];
}
function add_foot() {
	global $code;
	echo $code['foot'];
}

if($code['head'] != '')
	add_action('wp_head', 'add_head');
if($code['post_top'] != '' || $code['post_bottom'] != '')
	add_action('the_content', 'add_post');
if($code['foot'] != '')
	add_action('wp_footer', 'add_foot');

// I18n support
function ia_load_textdomain() {
	load_plugin_textdomain('insert-anywhere', 'wp-content/plugins/insert-anywhere/languages');
}
if(current_user_can('manage_options'))
	add_action('init', 'ia_load_textdomain');

// Add options page link
function add_menu() {
	add_options_page(__('Insert Codes', 'insert-anywhere'), __('Insert Codes', 'insert-anywhere'), 9, __FILE__, 'config_page');
}

// Admin page content
function config_page() {
	global $code;
	global $only_single;
	if(!current_user_can('manage_options'))
		die('You wanna die young? :) Haha, stop doing that!')
	
	?><div class="wrap">
	<h2><?php _e('Insert Codes to Anywhere', 'insert-anywhere'); ?></h2>
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<h3 style="margin-bottom: 0;"><?php _e('Blog header &amp; footer', 'insert-anywhere'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<td style="width: 49%">
					<h4><?php _e('Blog header', 'insert-anywhere'); ?></h4>
					<textarea rows="5" style="width: 98%;" name="ia_head"><?php echo $code['head']; ?></textarea>
					<p class="setting-description" style="font-style: italic;"><?php _e('Additional code between <code>&lt;head&gt;</code> and <code>&lt;/head&gt;</code>. Usually, code placed here is for loading extra Javascripts, CSS styles, etc.', 'insert-anywhere'); ?></p>
				</td>
				<td style="width: 49%">
					<h4><?php _e('Blog Footer', 'insert-anywhere'); ?></h4>
					<textarea rows="5" style="width: 98%;" name="ia_foot"><?php echo $code['foot']; ?></textarea>
					<p class="setting-description" style="font-style: italic;"><?php _e('Code above will be added immediately before <code>&lt;/body&gt;</code>. Best place for JS stats code, and some extra effects.', 'insert-anywhere'); ?></p>
				</td>
			</tr>
		</table>
		<hr />
		<h3 style="display: inline; margin-bottom: 0;"><?php _e('Articles top &amp; bottom', 'insert-anywhere'); ?></h3>
			<label for="ia_single">
				<input id="ia_single" type="checkbox"<?php if($only_single['post']) echo ' checked="checked"' ?> name="ia_single"/>
				Only shows on single post page.
		</label>
		<table class="form-table">
			<tr valign="top">
				<td style="width: 49%">
					<h4><?php _e('Post Top', 'insert-anywhere'); ?></h4>
					<textarea rows="5" style="width: 98%;" name="ia_post_top"><?php echo $code['post_top']; ?></textarea>
					<p class="setting-description" style="font-style: italic;"><?php _e('Code you put here will be shown above your post content.', 'insert-anywhere'); ?></p>
				</td>
				<td style="width: 49%">
					<h4><?php _e('Post Bottom', 'insert-anywhere'); ?></h4>
					<textarea rows="5" style="width: 98%;" name="ia_post_bottom"><?php echo $code['post_bottom']; ?></textarea>
					<p class="setting-description" style="font-style: italic;"><?php _e('Code you put here will be shown below your post content. It\'s the best place to put your ADs and online sharing gadgets.', 'insert-anywhere'); ?></p>
				</td>
			</tr>
			<tr>
		</table>
		
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="ia_head,ia_post_top,ia_post_bottom,ia_foot,ia_single" />
		
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'insert_anywhere'); ?>" />
		</p>
		
		<div style="margin-top: 20px; text-align: center;"><p><?php _e('More avaliable places coming soon ...', 'insert-anywhere'); ?></p><p><?php _e('Please submit your advice by mailing to <strong>zheng@jiehan.org</strong>', 'insert-anywhere'); ?></p></div>
		
	</form>
</div><!-- /wrap -->
<?php
}

// Insert menu item and config page into WordPress back-end
if(current_user_can('manage_options'))
	add_action('admin_menu', 'add_menu');

?>