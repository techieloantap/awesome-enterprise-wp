<?php 

//yoast seo

require_once dirname( __FILE__) .'/seo/yoast-seo.php';

require_once dirname( __FILE__) .'/seo/rankmath-seo.php';



class app_seo{
	
	static function enable_sitemap($app){
		
		if(!isset($app['collection']['config'])) return false;
			
		$arr=aw2_library::get_module($app['collection']['config'],'settings');
		if(!$arr) return false;
		aw2_library::module_run($app['collection']['config'],'settings');
		$enable_sitemap = aw2_library::get_post_meta($arr['id'],'enable_sitemap');
		
		if($enable_sitemap !== 'yes')  return false;
		
		return true;
	}

}	