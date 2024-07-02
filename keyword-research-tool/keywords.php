<?php 

if ( ! defined( 'ABSPATH' ) ) exit; 


add_action( 'wp_ajax_kwt_initial_keywords', 'kwt_initial_keywords' );

function kwt_initial_keywords() {
	
	check_ajax_referer( 'kwt-ajax-nonce', 'security' );

	$keyword = sanitize_text_field($_POST['keyword']);
	$security = sanitize_text_field($_POST['security']);
	
	
	if (empty($security) or empty($keyword)){
		print_r(wp_json_encode(''));	
		wp_die();
	}
	

function get_kwt_data($keyword){
	
	$args = array(
		'user-agent'  => ''
	);
	
	$kwt_engine = 'google';
	$kwt_service_option = 'complete';
	$kwt_service = 'suggestqueries';
	$kwt_browser_option = 'chrome';
	$kwt_option = 'search';
	
	$dataresponse = wp_remote_request( 'https://'.$kwt_service.'.'.$kwt_engine.'.com/'.$kwt_service_option.'/'
	.$kwt_option.'?output='.$kwt_browser_option.'&client=psy-ab&gs_rn=64o&q='.urlencode($keyword).'%20', $args );

	//suggestqueries.google.com/complete/search?output=firefox&q=test
	//the above returns a text file with information encoded

	$data = $dataresponse['body'];	
	$responseCode = $dataresponse['response']['code'];
	
	if (!empty($responseCode) and $responseCode !== 200){
		print_r(wp_json_encode(''));	
		wp_die();
	} 
	
	
	$data = htmlentities($data, ENT_NOQUOTES, "UTF-8");
	
	
	if (($data = json_decode($data, true)) !== null) {
		$keywords = $data[1];
		$keywordsArray = [];
	
		foreach ($keywords as $key => $keywordResults){
			$keywordsArray[$key] = sanitize_text_field($keywordResults[0]);
		}
		
	} else {
		$keywords = '';	
	}
	
	return $keywordsArray;
}

$keywordSpace = $keyword.' ';

$primaryKeywordQueryClean = get_kwt_data($keyword);
$primaryKeywordQuerySpace = get_kwt_data($keywordSpace);
$keywordQueryCombined = array_merge($primaryKeywordQueryClean, $primaryKeywordQuerySpace);

array_unshift($keywordQueryCombined, $keyword);
$keywordQueryCombined = array_values(array_unique($keywordQueryCombined, SORT_REGULAR));

$secondaryKeywordArray = [];
foreach ($keywordQueryCombined as $key => $keywordInput){	
	if ($key <=5 ){	
		$keywordInput = wp_strip_all_tags($keywordInput);
		$secondaryKeywordData = '';
		if (strtolower(trim($keywordInput)) !== strtolower(trim(wp_strip_all_tags($keyword)))){
			$secondaryKeywordData = get_kwt_data($keywordInput);
			$secondaryKeywordArray = array_merge($secondaryKeywordArray, $secondaryKeywordData); 
		} 
	}

}
if (!empty($secondaryKeywordArray)){
	$keywordQueryCombined = array_merge($keywordQueryCombined, $secondaryKeywordArray);
	$keywordQueryCombined = array_values(array_unique($keywordQueryCombined, SORT_REGULAR));
}
print_r(wp_json_encode($keywordQueryCombined));	

wp_die();
		
}