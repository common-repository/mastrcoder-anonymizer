<?php
/*
Plugin Name: Mastrcoder Anonymizer
Plugin URI: http://k0nsl.org/blog/plugins/mastrcoder-anonymizer/
Description: Automatically anonymizes all the external links on your website via anonymizer.mastrcoder.com
Version: 1.0
Author: k0nsl, Schalk Burger
Author URI: http://k0nsl.org
*/

/*  Copyright 2013 Schalk Burger and k0nsl (Based on the 'WP Blank Referer' plugin by blackhatzen)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$__anonymize_links = new anonymize_links();

add_action('wp_footer', array($__anonymize_links,'add_anonymize_links_js'));
add_action('admin_menu', array($__anonymize_links,'anonymize_links_menu'));
add_action('wp_enqueue_scripts', array($__anonymize_links, 'anonymize_links_scripts'));
add_action('init', array($__anonymize_links, 'anonymize_links_init'));

register_activation_hook(__FILE__, array($__anonymize_links,'anonymize_links_activate'));
register_deactivation_hook(__FILE__, array($__anonymize_links,'anonymize_links_deactivate'));

function add_settings_link($links, $file) {
static $this_plugin;
if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
 
if ($file == $this_plugin){
$settings_link = '<a href="options-general.php?page=anonymize_links-options">'.__("Settings", "anonymize_links-options").'</a>';
 array_unshift($links, $settings_link);
}
return $links;
 }
add_filter('plugin_action_links', 'add_settings_link', 10, 2 );

final class anonymize_links {
	
	public function anonymize_links_init(){
		wp_register_script('anonymize_links-anonymize_links', WP_PLUGIN_URL . '/mastrcoder-anonymizer/js/anonymize.js');
	}
	
	public function anonymize_links_activate(){
		$opt_name = 'anonymize_links_service';
		$opt_val = get_option( $opt_name );		
		add_option("anonymize_links_service", '', '', 'yes');
	}
	
	public function anonymize_links_deactivate(){
		delete_option("anonymize_links_service");
	}
	
	public function anonymize_links_menu(){
		add_options_page('Anonymize Links Options', 'Mastrcoder Anonymizer', 'administrator', 'anonymize_links-options', array($this,'anonymize_links_options_page'));
	}	
	
	public function anonymize_links_options_page(){
		if($_POST['protected_links']){
			echo '<div class="updated"><p><strong> '. __('Settings saved.'). '</strong></p></div>';	
			update_option("anonymize_links_service", $_POST['protected_links']);
		} elseif(isset($_POST['protected_links'])){
		   echo '<div class="updated"><p><strong> '. __('Settings cleared.'). '</strong></p></div>';	
		   update_option("anonymize_links_service", '');
		}
			
		echo '<div class="wrap">';
		echo '<h2>'. __('Mastrcoder Anonymizer Settings') .'</h2>';
		?>			
		
		<form method="POST" action="">
		
		<table class="form-table">
			<tr><td><p>Do not anonymize the following domains / keywords:</p></td></tr>
			<tr valign="top">				
				<td>
					<input type="text" class="anonym_input" id="protected_links" name="protected_links" size="100" value="<?php echo get_option('anonymize_links_service')?>">		
					<br/><span class="description">Comma separated: domain1.tld, domain2.tld, keyword</span>		
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save'); ?>" />
		</p>
		<?php
		echo '</div>';
	}
	
	public function anonymize_links_scripts(){
		/* change: put it in the footer */
		wp_enqueue_script('anonymize_links-anonymize_links','','','',true);
	}
	
	public function add_anonymize_links_js(){
		$opt_val = get_option('anonymize_links_service');	
		echo '<script type="text/javascript"><!--
		protected_links = "'.$opt_val.'";

		auto_anonyminize();
		//--></script>';
	}
}
?>