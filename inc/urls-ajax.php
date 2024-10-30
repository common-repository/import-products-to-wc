<?php

add_action( 'wp_ajax_wcta2w_f711_get_page_results', 'wcta2w_f711_get_post_single_content_callback' );
// If you want not logged in users to be allowed to use this function as well, register it again with this function:
add_action( 'wp_ajax_nopriv_wcta2w_f711_get_page_results', 'wcta2w_f711_get_post_single_content_callback' );

function wcta2w_f711_get_post_single_content_callback() {
	
	//$search = $_POST['search_keyword'];
	$search = sanitize_text_field( $_POST['search_keyword'] );
	
	wcta2w_get_record($search);
	
	die();	
}

function wcta2w_get_record($search, $page=1){
	
	$url = $search;

	$post_type = "product";

	wcta2w_get_urls($url, $post_type);

}


function wcta2w_get_urls($url, $post_type){
	
	if(strpos($url, "amazon.com")){
		$domain = "amazon.com";
	} elseif (strpos($url, "amazon.in")){
		$domain = "amazon.in";
	} else {
		$domain = "amazon.com";
	}
	
	//$response = wp_remote_get( $url );
	//$data = wp_remote_retrieve_body( $response );
	
	$data = curl_get_contents($url);
	
	$data = str_replace("<", "::::", $data);
	
	//$box1 = explode("sg-col-4-of-12 s-result-item s-asin sg-col-4-of-16 sg-col sg-col-4-of-20", $data);
	//
	
	$box1 = explode('data-index=', $data);
	
	if(isset($box1[1])&&($box1[1]!=NULL)) { 
	
		unset($box1[0]);
		
		$json = array();
		
		foreach($box1 as $box2){
			
			$href1 = explode("href=\"", $box2);
			$href2 = explode("\"", $href1[1]);
			
			$href = $href2[0];
			
			if(!strpos( $href, $domain )){
				$href = "https://www.".$domain.$href;
			}
			
			$json[] = array("link1"=>$href);
			
		}
	
	} else {
		
		$box1 = explode('class="a-size-mini a-spacing-none a-color-base s-line-clamp-2"', $data);
		
		unset($box1[0]);
		
		$json = array();
		
		foreach($box1 as $box2){
			
			$href1 = explode("href=\"", $box2);
			$href2 = explode("\"", $href1[1]);
			
			$href = $href2[0];
			
			if(!strpos( $href, $domain )){
				$href = "https://www.".$domain.$href;
			}
			
			$json[] = array("link1"=>$href);
			
		}
		
	} 
	
	if(!isset($box1[1]))  {
		
		$box1 = explode('sg-col-4-of-12 s-result-item s-asin sg-col-4-of-16 sg-col s-widget-spacing-small sg-col-4-of-20', $data);
		
		unset($box1[0]);
		
		$json = array();
		
		foreach($box1 as $box2){
			
			$href1 = explode("href=\"", $box2);
			$href2 = explode("\"", $href1[1]);
			
			$href = $href2[0];
			
			if(!strpos( $href, $domain )){
				$href = "https://www.".$domain.$href;
			}
			
			$json[] = array("link1"=>$href);
			
		}
		
	}
	
	$total = count($json);
	
	$json["total"] = $total;
	$json["url"] = $url;
	$json["data"] = $data;
	echo json_encode($json);
	
	exit;
	
}