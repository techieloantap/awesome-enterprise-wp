<?php
/*
  Description: Import Export Awesome App's as package (XML) for transfering them from one site to another.
  Version: 1.0
  Author: WPoets
  License: GPLv2+
*/

add_action( 'admin_menu', 'awesome_import_export::setup' );

add_action( 'wp_ajax_awesome_export_code', 'awesome_import_export::awesome_export_code' );

/**
 * Registers our command when cli get's initialized.
 *
 */

add_action( 'cli_init', 'awesome_import_export::register_cli_commands' );

class awesome_import_export{
	
	static function register_cli_commands(){
		WP_CLI::add_command( 'awesome-import', 'awesome_import_export' );
	}
		
	static function setup(){
		$import_export_page = add_submenu_page( 'tools.php', 'Export Awesome Apps', 'Export Awesome Apps',
    'develop_for_awesomeui', 'awesome-app-import-export', 'awesome_import_export::import_export_dashboard',3);
		add_action( 'load-' . $import_export_page, 'awesome_import_export::load_js' );
	
		add_action( 'load-' . $import_export_page, 'awesome_import_export::load_js' );
	}
	
	static function load_js(){
		add_action( 'admin_enqueue_scripts', 'awesome_import_export::enqueue_admin_js' );
	}
	
	static function enqueue_admin_js(){
		if (!wp_script_is( "ladda-spin", 'enqueued' )) {
			wp_enqueue_script( 'ladda-spin', '//cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/spin.min.js', array(), '1.2.4' );
		}  
		if (!wp_script_is( 'ladda', 'enqueued' )) { 
			wp_enqueue_script( 'ladda', '//cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/ladda.min.js', array(), '1.2.4' );
		}
		if (!wp_style_is( 'ladda-css', 'enqueued' )) {	
			wp_enqueue_style( 'ladda-css', '//cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/ladda.min.css', array(), '3.1.1' );
		}
		
		wp_enqueue_script( 'awsome-import-export-script', plugins_url('export-apps/js/import-export.js',dirname(__FILE__)), array() );
	}
	
	static function import_export_dashboard(){
		$page = sanitize_text_field($_REQUEST['page']);
		$tab_url=menu_page_url( $page ,false );	
		
		echo '<div class="wrap ">';        	
		echo '<h2 class="hndle">Export Awesome Apps & Services</h2>';
		self::export_panel();
		echo '</div>';		 
	}
	
	static function export_panel(){
	
		$options='';

		echo'
		<style>
			.js-progress{
				display:none;
			}
			progress[value] {
			 
			  -webkit-appearance: none;
			  appearance: none;
			  border:none;
			  width: 315px;
			  height: 10px;
			 
			  margin-bottom: 2px;
			}
			.fields {
				border: #ebebeb solid 1px;
				background: #fafafa;
				border-radius: 3px;
				position: relative;
				margin-bottom: 20px;
				padding: 5px 0px 5px 5px;
			}
			.fields .label {
				vertical-align: top;
				margin: 0 0 10px;
				box-sizing: border-box;
				position: relative;
			}
			.fields .label label{
				display: block;
				font-weight: bold;
				margin: 0 0 3px;
				padding: 0;
			}	
			.awe-input{
				vertical-align: top;
				box-sizing: border-box;
				position: relative;
			}
			.checkbox-list{
				background: transparent;
				position: relative;
				padding: 1px;
				margin: 0;
				list-style: none;
				display: block;
				column-width: 200px;
			}
			.checkbox-list:before, .checkbox-list:after{
			   content: "";
				display: block;
				line-height: 0;
			}
			.checkbox-list:after{
			   clear: both
			}
			.checkbox-list li {
				font-size: 13px;
				line-height: 22px;
			
				position: relative;
				word-wrap: break-word;
				display: block;
				margin: 0;
				padding: 0;
				float: none;
			}
			.awe-submit{
				margin-bottom: 0;
				line-height: 28px;
			}
			.awe-submit button{
				margin-right: 5px;
			}
		</style>';		
		echo '<div class="export postbox">' ;
		echo '<div class="inside">' ;

		echo '<form method="post">
			<p>Select to services and apps to download. Once downloaded the xml files can be improted using WordPress importer plugin.</p>
			<div class="fields">
			<div class="label">
				<label for="services">Select Services</label>
			</div>';
		echo'	
			<div class="awe-input">	';
		$handlers=&aw2_library::get_array_ref('handlers');	
		echo	'<ul class="checkbox-list">';
					

		foreach($handlers as $key => $handler){
			if(!isset($handler['post_type']))
				continue;
			
			if('awesome_core' == $handler['post_type'])
				continue;
			
			if(isset($handler['service']) && strtolower($handler['service']) === 'yes'){
				$service_post_type =  $handler['post_type'];
			} 
			elseif(isset($handler['@service']) && $handler['@service'] === true){
				$service_post_type =  $handler['post_type'];
			}	
			echo '
			<li><label><input type="checkbox" name="services[]" value="'.$service_post_type.'"> '.$handler['service_label'].'</label></li>';
		}
		echo'</ul>
		</div>
		</div>';		
			
		echo '<div class="fields">
			<div class="label">
				<label for="apps">Select Apps </label>
			</div>
			<div class="awe-input">			
				<ul class="checkbox-list">';
			$registered_apps=&aw2_library::get_array_ref('apps');
			foreach ($registered_apps as $app){		
				echo'<li><label><input type="checkbox" name="apps[]" value="'.$app['slug'].'"> '.$app['name'].'</label></li>';
			}		
		echo'</ul>
			</div>
			<div class="fields">
				<div class="label">
					<label for="apps">Select Dates </label>
				</div>
				<div class="awe-input">
					<p>Select dates between which all the posts will be selected</p>
				</div>
			</div>';
		echo'<h4>Export As XML <small>(ideal for creating new app/service)</small></h4>
			<p class="awe-submit">
				<button type="submit" name="action" class="button button-primary ladda-button js-app-export-button" value="export-selected" data-style="zoom-out" data-action="selected" data-file-slug="selective" data-format="xml">Export Selected </button>
				<button type="submit" name="action" class="button ladda-button js-app-export-button" value="export-all" data-style="zoom-out" data-action="all" data-file-slug="all-awesome" data-format="xml">Export All</button>
				<button type="submit" name="action" class="button ladda-button js-app-export-button" value="export-applist" data-style="zoom-out" data-action="applist" data-file-slug="applist" data-format="xml">Export only App list</button>
				<button type="submit" name="action" class="button ladda-button js-app-export-button" value="export-core" data-style="zoom-out" data-action="core" data-file-slug="core" data-format="xml">Export Core</button>
				<button type="submit" name="action" class="button ladda-button js-app-export-button" value="export-services" data-style="zoom-out" data-action="services" data-file-slug="services" data-format="xml">Export All Service</button>
				<button type="submit" name="action" class="button ladda-button js-app-export-button" value="export-apps" data-style="zoom-out" data-action="apps" data-file-slug="all-apps" data-format="xml">Export All Apps</button>
			</p>';	
		echo'<h4>Export As HTML <small>(ideal for migrating code between dev & prod)</small></h4>
		     <p class="awe-submit">
				<button type="submit" name="action" class="button button-primary ladda-button js-app-export-button" value="selected" data-style="zoom-out" data-action="selected" data-file-slug="selective-html" data-format="html">Export Selected</button>
				<button type="submit" name="action" class="button button-primary ladda-button js-app-export-button" value="export-all" data-style="zoom-out" data-action="all" data-file-slug="all-html" data-format="html">Export All</button>
				<button type="submit" name="action" class="button button-primary ladda-button js-app-export-button" value="export-services" data-style="zoom-out" data-action="services" data-file-slug="selective-html" data-format="html">Export All Services</button>
				<button type="submit" name="action" class="button button-primary ladda-button js-app-export-button" value="export-apps" data-style="zoom-out" data-action="apps" data-file-slug="selective-html" data-format="html">Export All Apps</button>
			</p>';
		echo'</form>';
		
		echo '</div>
			</div>';	
	}

	static function awesome_export_code(){
		$output=array();
		$output['status']="fail";
		$file_slug = sanitize_text_field($_GET['file_slug']);
		$activity = sanitize_text_field($_GET['activity']);
		$format = sanitize_text_field($_GET['format']);
		if(empty ( $file_slug )|| empty( $activity )){
			$output['message']='Something\'s Wrong.';
			echo json_encode($output);
			wp_die();
		}
		
		require_once 'export-html.php';
		require_once 'export-xml.php';
		$sitename = sanitize_key( get_bloginfo( 'name' ) );
		if ( ! empty( $sitename ) ) {
			$sitename .= '.';
		}
	
		
		$args=array();
		$args['activity']=$activity;
		
		
		if($format === 'html' ) {
			$args['filename']=$file_slug.'-'.$sitename;
			awesome_export_html($args);
		}
		else if($format === 'xml' ) {
			$args['filename']=$file_slug.'-'.$sitename.date('Ymdhms').'.xml';
			awesome_export_wp($args);
		}

		wp_die();
		
	}
	
	/**
     * Imports HTML code generated using Export Tool of Awesome Enterprise.
     *
     * ## OPTIONS
     *
     * 
     * [--code-path=<absolute_path>]
     * : Absolute path to the directory where code is saved on the server.
     * ---
     * ---
     * [--overwrite=<true|false>]
     * : Set this to false if you don't want to overwirte the code while importing default is true.
     * ---
     * default: true
     * options:
     *   - true
     *   - false
     * ---
     *
     * ## EXAMPLES
     *
     *     wp awesome-import import_html --code-path='/var/www/codedump/' 
     *     wp awesome-import import_html --code-path='/var/www/codedump/' --overwrite=false
     *
     * 
     */
	public function import_html( $args, $assoc_args ) {

        // process arguments 
		$code_path = $assoc_args['code-path'] ;
		$overwrite = $assoc_args['overwrite'] ;
		
		if(empty($overwrite) || $overwrite==='true')
			$overwrite=true;
		
		WP_CLI::line('Importing Code From '. WP_CLI::colorize( '%B '. $code_path.'%n' ) );

		if(!is_dir($code_path )){
			WP_CLI::warning( 'Code Path '.$code_path.' not found.' );
			WP_CLI::halt( 200 );
		}
		
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($code_path));
		$Regex = new RegexIterator($objects, '/^.+\.html$/i', RecursiveRegexIterator::GET_MATCH);
		
		$count= iterator_count($Regex);
		unset($objects);
		unset($Regex);
		$progress = \WP_CLI\Utils\make_progress_bar( 'Importing Modules ', $count );
		
		$folders = glob($code_path . "/*",GLOB_ONLYDIR);
			
		foreach ($folders as $folder){
			$files = glob($folder."/*.module.html");
			$post_type=basename($folder);
			WP_CLI::debug(WP_CLI::colorize( '%M Post Type = '. $post_type.'%n' ) );
			foreach ($files as $filename){
				$module=basename($filename);
				$module=str_replace(".module.html","",$module);
				WP_CLI::debug('module to import = '. $module );	
				
				// read the file 
				$content= file_get_contents($filename, true);
				
				$my_post = array(
					'post_content'  =>  $content
				);
				$my_post['post_type']= wp_unslash( sanitize_post_field( 'post_type', $post_type, 0, 'db' ) );
				$my_post['post_title']= wp_unslash( sanitize_post_field( 'post_title', $module, 0, 'db' ) );
				
				//check if module exits or not
				global $wpdb;
				 $post_id = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}posts WHERE post_name = '" . $module . "' AND post_type = '" . $post_type . "'");
				
				WP_CLI::debug(WP_CLI::colorize( '%B post_id = '. $post_id.'%n' ) );
				if(!empty($post_id)){
					if($overwrite !== true){
						$progress->tick();
						continue;
					}
					$my_post['ID']=$post_id;
				} else {
					$my_post['post_name']=	wp_unslash( sanitize_post_field( 'post_name', $module, 0, 'db' )  );
					$my_post['post_status']='publish';
					
				}
				
				// Insert the post into the database.
				$postid = wp_insert_post( $my_post );
				if(is_wp_error($postid)){
					  WP_CLI::error( $postid->get_error_message() );
				}
				$progress->tick();
			}
		}

		$progress->finish();
		
        // give output
        WP_CLI::success( $count. ' modules imported!' ); // Prepends Success to message

    }
}