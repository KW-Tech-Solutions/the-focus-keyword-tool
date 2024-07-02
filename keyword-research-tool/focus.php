<?php 

if ( ! defined( 'ABSPATH' ) ) exit; 


add_action( 'wp_ajax_kwt_focus_keywords', 'kwt_focus_keywords' );



function kwt_focus_keywords() {
	
	check_ajax_referer( 'kwt-ajax-nonce', 'security' );
	
	$security = sanitize_text_field($_POST['security']);
	
	$keywords = isset( $_POST['keywords'] ) ? (array) $_POST['keywords'] : array();
	$keywords = array_map( 'esc_attr', $keywords );
	
	if (empty($security) or empty($keywords)){
		print_r(wp_json_encode(''));	
		wp_die();
	}
	
	//goes through each item in the $keywords array, and look up google	
	$args = array(
		'User-Agent' => ''
	);
	

	$tempArray = [];
	foreach ($keywords as $x) {
  		$dataresponse = wp_remote_get('https://www.startpage.com/do/search?cmd=process_search&query='.$x);
		$body = $dataresponse['body']; 
		preg_match_all('#(?=web-google)(.*?)(?=window\.dispatchEvent\(new Event\(\'app:hydrated\'\))#is', $body, $matches);	
		$tempCheck = $matches[0][0];
		preg_match_all('#(?<=description":)(.*?)(?=","displayUrl")#is', $tempCheck, $matchOut);
		foreach ($matchOut[0] as $y) {
			$tempArray[]= strtolower(preg_replace('/\s+/', ' ', preg_replace("/[^A-Za-z ]/", '', html_entity_decode(wp_strip_all_tags((sanitize_text_field($y))), ENT_QUOTES|ENT_HTML5|ENT_COMPAT, 'UTF-8'))));
									 }
		sleep(10);
	};
	
	$result = implode(' ', $tempArray);
	$result = explode(' ', $result);
	$result = array_count_values($result);
	arsort($result);
	print_r(wp_json_encode($result));
	
	wp_die();
}


