<?php
function generate_url($name,$param=null)
{
    $CI =& get_instance();
	$rt = $CI->config->item('custom_routes');
	$result = false;
	if(isset($rt[$name])) {
		$route_url = $rt[$name]['route'];
		$route_param_count = substr_count($route_url, '{');
		$send_param_count = count($param);
		// jika jumlah parameter berbeda
		if($route_param_count != $send_param_count) {
			throw new Exception("Error generating url for $name. Please check the parameters.");
		}
		$url = $route_url;
        if($param) {
            foreach($param as $p => $pval) {
                $url = str_replace('{'.$p.'}',$pval, $url);
            }
        }
        // echo preg_replace('/{(\w+)}/','(.*)',$route_url).'<br>';
		$route_param_count = substr_count($url, '{');
		if($route_param_count > 0 ){
			throw new Exception("Error generating url for $name. Missing parameters.");
		}
		return base_url($url);

	} else {
		throw new Exception("Route $name not found.");
	}
	return $result;
}