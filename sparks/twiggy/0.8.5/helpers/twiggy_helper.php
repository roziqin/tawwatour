<?php

 function asset($path = '',$is_global=FALSE){
 	$CI =& get_instance();
	$_config = $CI->config->item('twiggy');
 	$theme_dir = ($_config['include_apppath']) ? APPPATH . $_config['themes_base_dir'] : $_config['themes_base_dir'];
 	$active_theme = $CI->twiggy->get_theme();
 	if($is_global)return base_url('assets/'.$path);
 	else return base_url($theme_dir.$active_theme.'/_assets/'.$path);
 }
 function asset_css($path = '',$is_global=FALSE){
 	return asset('css/'.$path,$is_global);
 }
 function asset_js($path = '',$is_global=FALSE){
 	return asset('js/'.$path,$is_global);
 }
 function asset_image($path = '',$is_global=FALSE){
 	return asset('images/'.$path,$is_global);
 }
 function asset_file($path = '',$is_global=FALSE){
 	return asset('files/'.$path,$is_global);
 }
 function asset_font($path = '',$is_global=FALSE){
 	return asset('fonts/'.$path,$is_global);
 }
 function resource($name){
 	return '_layouts/resources/'.$name.'.html.twig';
 }
 function part($name){
 	return '_parts/'.$name.'.html.twig';
 }
/* End of file twiggy_helper.php */