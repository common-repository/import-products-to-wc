<?php

add_action( 'wp_ajax_wcta2w_f711_get_product_page_results', 'wcta2w_f711_get_product_page_callback' );
// If you want not logged in users to be allowed to use this function as well, register it again with this function:
add_action( 'wp_ajax_nopriv_wcta2w_f711_get_product_page_results', 'wcta2w_f711_get_product_page_callback' );

function wcta2w_f711_get_product_page_callback(){
	
}

function wcta2w_get_single_amazon_product($url){
	
	ini_set("max_execution_time", 900000);


	require_once(ABSPATH . 'wp-admin/includes/media.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	
	//echo "We are on Child Plugin Get Amazon Single product function<br>";
	
	//$data = json_decode(base64_decode($url), true);

	$data = $url;
	
	$url1 = explode("?", $data['link']);
	
	$url = $url1[0];
	
	$post_type = "product";
	
	//echo $post_type."<br>";
	
	$original_product_url = $url;
	
	/*$reviews_array = json_decode(base64_decode($data['reviews']), true);
	
	$result = array();
	
	foreach($reviews_array as $review){
		
		$result['reviews'][] = $review;
		
	}*/
	
	$result['original_url'] = $original_product_url;
	
	$result['title'] = $data['title'];
	
	$images = array();
	
	$images3 = explode(', ', $data['images']);
	//unset($images3[0]);
	
	foreach($images3 as $images4){
		
		$images[] = $images4;
		
	}
	
	$result['images'] = $images;
	
	$result['price'] = $data['price'];
	
	$asin1 = explode("/dp/", $original_product_url);
	$asin2 = explode("/", $asin1[1]);
	
	$result['asin'] = $asin2[0];
	
	$result['cat'] = $data['categories'];
	
	$result['short'] = $data['description'];
	$result['description'] = $data['description'];
	
	//$result['manufacturer'] = $data['brand'];
	//$result['weight'] = "";
	//$result['dimensions'] = "";
	
	$post_type = "product";
	
	global $wpdb;
	
	if($wpdb->get_row($wpdb->prepare("SELECT post_title FROM $wpdb->posts WHERE post_title = '" . $result['title'] . "' AND post_type='".$post_type."' ", 'ARRAY_A'))) {

		
	} else {
		
		$cats = explode(", ", $result['cat']);
		
		//$reviews = $result['reviews'];
		
		$categories = array();
		
		foreach($cats as $one_cat){
			
			$loc1 = get_term_by( "name", $one_cat, "product_cat", 'ARRAY_A', 'raw' );
				
				if(!isset($loc1['term_id'])){
					

					
					$term_array = wp_insert_term( $one_cat, 'product_cat', $args = array() );
					
					if(is_array($term_array)){
						$categories[] = $term_array['term_id'];
					}
					
				} else {
					
					$categories[] = $loc1['term_id'];
				
				}
			
		}
	  

		
		$my_post = array(
					'post_title'    => wp_strip_all_tags(sanitize_text_field($result['title'])),
					'post_type'      => $post_type,
					'post_content'  => $result['description'], //esc_html($result['description']),
					'post_excerpt' => $result['short'],
					'post_status'   => 'publish',
					'post_author'   => 1,
					'tax_input' => array(
						'product_cat' => $categories,
						//'product_brand' => array( $brand )
					),
				);

		
		
		
		// Insert the post into the database
		$post_id1 = wp_insert_post( $my_post );
		
		foreach($categories as $one_cat){
		
			wp_set_object_terms( $post_id1, $one_cat, 'product_cat' );
		
		}
		
		//wp_set_object_terms( $post_id1, $category, 'product_cat' );
		
		$new_aur = get_post($post_id1); 
		$slug = $new_aur->post_name;
		
		
		// Update post slug best-price-india-specs-reviews-$post_id1
		  $my_post = array(
			  'ID'           => $post_id1,
			  'post_name'   => $slug."-best-price-specs-reviews-".$post_id1
		  );
		
		// Update the post into the database
		  wp_update_post( $my_post );
	
		$images = $result['images'];
		
		$product_type = esc_attr( get_option('product_type') );
			
		$cat_id = get_term_by('name', $product_type, 'product_type');
		
		$cat_ids = array($cat_id->term_id);
		
		$term_taxonomy_ids = wp_set_object_terms( $post_id1, $cat_ids, 'product_type', true );
		
		$last_id = $post_id1;
		
		$yoast_seo_title = $result['title']." Best Price | Specs | Reviews - Lowest Online";
		
		$result['price'] = str_replace(",", "", $result['price']);
		
		
		
		//if(esc_attr( get_option('product_type') )=='simple'){
			
			$markup = esc_attr( get_option('markup_ratio') );
			
			$result['price'] = str_replace("$", "", $result['price']);
			
			if($markup!=NULL && $markup!=0 && $result['price']!=NULL && $result['price']!=0 ){
				
				
				$result['price'] = (float)$result['price'] * (float)$markup;
				
			}
			
			
		//}
		
		if(strpos("?", $original_product_url)){
			$affiliate_link = $original_product_url."&tag=".get_option('affiliate_tag');
		} else {
			$affiliate_link = $original_product_url."?tag=".get_option('affiliate_tag');
		}
		
		update_post_meta($post_id1, '_regular_price', str_replace("$", "", $result['price']));
		//update_post_meta($post_id1, '_stock_update_url', $stock_update_url);
		//update_post_meta($post_id1, '_sale_price', str_replace("$", "", $price));
		update_post_meta($post_id1, '_price', str_replace("$", "", $result['price']));
		update_post_meta($post_id1, '_original_product_url', $original_product_url);
		update_post_meta($post_id1, '_product_url', $affiliate_link);
		update_post_meta($post_id1, '_yoast_wpseo_title', $yoast_seo_title);
		update_post_meta($post_id1, '_sku', $data['asin']);	
		
		//update_post_meta($post_id1, '_my_custom_attribute', "Suhail Ahmad");
		
		
	   /*$new_specs = array();
	   
	   $new_specs['Asin'] = $result['asin'];
	   $new_specs['Dimensions'] = $result['dimensions'];
	   $new_specs['Weight'] = $result['weight'];
	   $new_specs['Manufacturer'] = $result['manufacturer'];
		
		wcproduct_set_attributes($post_id1, $new_specs, 'color');
		
		echo "<pre>";
		print_r($reviews);
		echo "</pre>";
		
		foreach($reviews as $review){
			
			add_rating_to_product($post_id1, $review['comment_author'], $review['comment_author_email'], (int)$review['comment_rating'], str_replace("style=\"", "abc=\"", $review['comment_content']) );
			
		}*/
		
		$image_id = media_sideload_image($result['images'][0], $post_id1, $title." Best Price");
		

		
		$img1 = explode("src='", $image_id);
		$img2 = explode("'", $img1[1]);
		
		$img_image = $img2[0];
		
		// Get the Attachment ID
		$attachment_id = get_attachment_id_from_src ($img_image);

		set_post_thumbnail( $post_id1, $attachment_id );
			
		
		$gallery_meta_key = "_product_image_gallery";
		$gallery_meta_value = "";
		
		unset($result['images'][0]);
		
		foreach($result['images'] as $key=>$im){
			
			if($key>=8){
				break;
			}
			
			if($key==1){
				$alttext = " Cheapest Price";
			} elseif ($key==2){
				$alttext = " Lowest Price";
			} elseif ($key==3){
				$alttext = " Affordable Price";
			} elseif ($key==4){
				$alttext = " Reasonable Price";
			}
			
			if(strlen($im)>=3){
				
				$image_id = media_sideload_image($im, $post_id1, $title.$alttext);
				
				$img1 = explode("src='", $image_id);
				$img2 = explode("'", $img1[1]);
				
				$img_image = $img2[0];
				
				// Get the Attachment ID
				$attachment_id = get_attachment_id_from_src ($img_image);
				
				if($attachment_id!=0){
					$gallery_meta_value.= $attachment_id.",";
				}
				
			}
			
			update_post_meta($post_id1, $gallery_meta_key, $gallery_meta_value);
		}
		
		
		
		

		
	
		
	}
	
	echo "<pre>";
	print_r($result);
	print_r($data);
	echo "</pre>";
	
	
	
	return $post_id1;
}

function wcta2w_get_single_product($url){
	
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	
	//$url = sanitize_text_field( $_POST['search_keyword'] );
	
	//echo "This is before adding<br>";
	
	$url1 = explode("?", $url);
	
	$url = $url1[0];
	
	$post_type = "product";
	
	//echo $post_type."<br>";
	
	$original_product_url = $url;
	
	//$response = wp_remote_get( $url );
	//$data = wp_remote_retrieve_body( $response );
	
	$data = curl_get_contents($url);
	
	/*echo "<pre>";
	print_r($data);
	echo "</pre>";
	
	exit;*/
	
	$result = array();
	
	$r_link1 = explode('data-hook="see-all-reviews-link-foot"', $data);
	$r_link2 = explode('href="', $r_link1[1]);
	$r_link3 = explode('"', $r_link2[1]);
	
	$r_link = "https://www.amazon.com".$r_link3[0];
	
	echo $r_link."<br>";
	
	$r_data = curl_get_contents($r_link);
	
	$r_box1 = explode('class="a-section review aok-relative"', $r_data);
	unset($r_box1[0]);
	
	foreach($r_box1 as $r_box2){
		
		$review1_name1 = explode('class="a-profile-name">', $r_box2);
		$review1_name2 = explode('</', $review1_name1[1]);
		
		$review1_name = $review1_name2[0];
		
		$auth_email1 = explode(" ", $review1_name);
		$auth_email = $auth_email1[0]."@mail.com";
		
		$rating1 = explode('class="a-icon-alt">', $r_box2);
		$rating2 = explode('</', $rating1[1]);
		$rating3 = explode(" out", $rating2[0]);
		$rating = trim(str_replace(".0", "", $rating3[0]));
		$rating = trim(preg_replace('/\s+/',' ', $rating));
		
		$rev_content1 = explode('review-data">', $r_box2);
		$rev_content2 = explode('</div', $rev_content1[1]);
		$rev_content = str_replace('style="', 'abc="', $rev_content2[0]);
		$rev_content = trim(preg_replace('/\s+/',' ', $rev_content));
		$result['reviews'][] = array('comment_author'=>$review1_name, "comment_author_email"=>$auth_email, "comment_rating"=>$rating, "comment_content"=>$rev_content);

		
	}
	
	
	
	$htm_data = $data;
	
	$data = str_replace("<", "::::", $data);
		
	
	$title1 = explode('id="productTitle"', $data);
	$title2 = explode("\">", $title1[1]);
	$title3 = explode("::::/", $title2[1]);
	
	$title = $title3[0];
	
	$title = trim(preg_replace('/\s+/',' ', $title));
	
	if($title==NULL){
		
		//echo "No title";
		return false;
	}
	
	//echo "yes title<br>";
	
	
	
	//echo $title."<br>";
	
	$result['original_url'] = $original_product_url;
	
	$result['title'] = $title;
	
	
	$images = array();
	
	$images1 = explode("'colorImages': {", $data);
	$images2 = explode("}]},", $images1[1]);
	
	$images3 = explode('"hiRes":"', $images2[0]);
	unset($images3[0]);
	
	foreach($images3 as $images4){
		
		$images5 = explode('"', $images4);
		
		$images[] = $images5[0];
		
	}
	
	/*$images1 = explode('data-a-dynamic-image="', $data);
	$images2 = explode('}"', $images1[1]);
	
	$j_data = html_entity_decode($images2[0]."}");
	

	
	$images = json_decode($j_data, true);*/
	
	$result['images'] = $images;
	
	
	
	
	$price1 = "";//explode('[{"displayPrice":"$', $data);
	
	
	
	$price2 = explode('"', $price1[1]);
	
	$price = $price2[0];
	
	if($price==NULL){
		
		echo "Price is not coming";
		
		$price1 = explode('a-color-base">', $data);
		
		
		
		//echo $price1[1];
		
		$price2 = explode("::::/", $price1[1]);
		
		$price = $price2[0];
		
		echo $price."<br>";
		
	}
	
	$price = trim(preg_replace('/\s+/',' ', $price));
	
	/*$price1 = explode('class="a-size-medium a-color-price">', $data);
	$price2 = explode("::::/", $price1[1]);
	
	$price = $price2[0];
	
	$price = trim(preg_replace('/\s+/',' ', $price));*/
	

	
	$result['price'] = $price;
	
	$form1 = explode('id="addToCart"', $data);
	$form2 = explode("::::/form", $form1[1]);
	
	$f_data = $form2[0];
	
	$asin1 = explode('id="ASIN"', $f_data);
	$asin2 = explode('value="', $asin1[1]);
	$asin3 = explode('"', $asin2[1]);
	
	$asin = $asin3[0];
	
	$result['asin'] = $asin;
	
	$short1 = explode('class="a-unordered-list a-vertical a-spacing-mini">', $data);
	$short2 = explode("::::/ul", $short1[1]);
	
	
	$short3 = explode("::::li>", $short2[0]);
	unset($short3[0]);
	
	$short = "<ul>";
	
	foreach($short3 as $short4){
		
		$short5 = explode("::::/li", $short4);
		
		$short6 = $short5[0];
		$short6 = trim(preg_replace('/\s+/',' ', $short6));
		
		$short .= "<li>".$short6."</li>";
		
	}
	
	$short.="</ul>";
	
	
	$short = str_replace(array("::::", "::marker"), array("<", ""), $short);	
	
	$short = preg_replace('/<span[^>]+\>/i', '', $short);
	
	$result['short'] = $short;
	
	$info1 = explode('id="prodDetails"', $data);
	
	if($info1[1]!=NULL){
		//$info = $info[1];
	} else {
		$info[1] = $info[0];
	}
	
	
	$dimenssion1 = explode("Product Dimensions", $info1[1]);
	$dimenssion2 = explode('prodDetAttrValue">', $dimenssion1[1]);
	$dimenssion3 = explode('::::/', $dimenssion2[1]);
	
	
	
	$dimensions = $dimenssion3[0];
	
	//$dimensions = str_replace(">", "", $dimensions);
	
	$dimensions = trim(preg_replace('/\s+/',' ', $dimensions));
	
	$result['dimensions'] = $dimensions;
	
	$weight1 = explode("Item Weight", $info1[1]);
	$weight2 = explode('prodDetAttrValue">', $weight1[1]);
	$weight3 = explode('::::/', $weight2[1]);
	
	$weight = $weight3[0];
	
	$weight = trim(preg_replace('/\s+/',' ', $weight));
	
	$result['weight'] = $weight;
	
	$manufacturer1 = explode("Manufacturer", $info1[1]);
	$manufacturer2 = explode('prodDetAttrValue">', $manufacturer1[1]);
	$manufacturer3 = explode('::::/', $manufacturer2[1]);
	
	$manufacturer = $manufacturer3[0];
	
	$manufacturer = trim(preg_replace('/\s+/',' ', $manufacturer));
	
	$result['manufacturer'] = $manufacturer;
	
	$cats1 = explode('class="a-unordered-list a-horizontal a-size-small">', $data);
	$cats2 = explode('::::/ul', $cats1[1]);
	
	$cats3 = explode('::::li>', $cats2[0]);
	unset($cats3[0]);
	
	$cats = array();
	
	foreach($cats3 as $key=>$cats4){
		
		$cats5 = explode("::::/li", $cats4);
		
		$cats6 = trim(strip_tags(str_replace("::::", "<", $cats5[0])));
		
		
		
		$cats6 = trim(preg_replace('/\s+/',' ', $cats6));
		
		$cats[] = $cats6;
		
		//break;
		
	}
	
	
	
	$result['cat'] = implode(",", $cats);
	
	
	/*$description1 = explode('class="aplus-module-wrapper aplus-3p-fixed-width">', $data);
	$description2 = explode("::::/div", $description1[1]);
	
	$description = trim(strip_tags(str_replace("::::", "<", $description2[0])));
	
	$result['description'] = $description;*/
	
	
	$dom = new DOMDocument();
	
	@$dom->loadHTML($htm_data);
	
	$desc2 = $dom->getElementByID('productDescription');
	
	$description2 = $desc2->textContent;
	
	
	
	//$description2 = trim(preg_replace('/\s+/',' ', $description2));
	
	$result['description'] = $description2;
	
	
	$images2 = array();
	
	$imgs1 = explode('"large":"', $data);
	unset($imgs1[0]);
	
	foreach($imgs1 as $key=>$imgs2){
		
		$imgs3 = explode('"', $imgs2);
		$imgs = $imgs3[0];
		
		$images2[] = $imgs;
		
		if($key>=5){
			break;
		}
		
	}
	
	$result['images2'] = $images2;
	
	$imgs = array();
	
	foreach($images as $key=>$img){
		
		$imgs[] = $key;
		
	}
	
	$main = array();
	$main['title'] = $title;
	$main['imgs'] = $imgs;
	
	$main['price'] = $price;
	
	
	
	$description = $description2;
	
	$short_description = $short;
	
	
	/*$description = $main['title']." runs on ".$specs['Operating System'].". The phone boast a ".$specs['Screen Size']." ".$specs['Display Type']." display with resolution of ".$specs['Screen Resolution'].". Display is secured with ".$specs['Screen Protection'].", aluminum body. On accounting cameras, it has a ".$specs['Resolution1']." with ".$specs['Camera Features']."";
	
	
	$sample_description = $main['title']." runs on ".$specs['Operating System'].". The phone boast a ".$specs['Screen Size']." ".$specs['Display Type']." display with resolution of ".$specs['Screen Resolution'].". Display is secured with ".$specs['Screen Protection'].". On accounting cameras, it has a ".$specs['Resolution1']." with ".$specs['Camera Features']." and it also have ".$specs['Resolution2'].".<br><br>

The ".$main['title']." is fueled by a  ".$specs['Capacity']." battery which can’t be removed. battery and promises long hours of utilization before next charge. Discussing processors, mobile phone is packed with ".$specs['Processor']." to provide efficient and effective usage without any issues.<br><br>


".$main['title']." has ".$specs['RAM']." of RAM and ".$specs['Internal Memory']." of internal memory which can be expanded upto 256 GB (uses Hybrid slot) with the help of memory cards. It has smooth and sharp design that make gadget look elegant and exquisite. Thickness of phone is just ".$specs['Thickness'].". Make a note that it is a ".$specs['SIM Slot(s)']." mobile phone.<br><br>

".$main['title']." connectivity highlights include ".$specs['Network'].". It has certain sensors like ".$specs['Other Sensors'].". On the last note, dimension measurement are as follows : ".$specs['Height']." (height) x ".$specs['Width']." (width) x ".$specs['Thickness']." (Thickness) and it weighs approximately ".$specs['Weight'].".";
	
	
	$short_description = "
	<strong>".$main['title']." Best Price</strong><br>
	<ul>
		<li>".$specs['Screen Size']." ".$specs['Display Type']." display</li>
		<li>".$specs['Resolution1']." | ".$specs['Resolution2']."</li>
		<li>".$specs['Capacity']." battery</li>
		<li>".$specs['Processor']."  ".$specs['Chipset']."</li>
		<li>".$specs['RAM']." RAM | ".$specs['Internal Memory']." ROM</li>
		<li>".(($specs['VoLTE']=="yes")?"VoLTE supported":"VoLTE Not Supported")."</li>
	</ul>
	";*/
	
	
	
	
	//exit;
	
	global $wpdb;
	
	if($wpdb->get_row($wpdb->prepare("SELECT post_title FROM $wpdb->posts WHERE post_title = '" . $title . "' AND post_type='".$post_type."' ", 'ARRAY_A'))) {

		
	} else {
		
		$reviews = $result['reviews'];
		
		$categories = array();
		
		foreach($cats as $one_cat){
			
			$loc1 = get_term_by( "name", $one_cat, "product_cat", 'ARRAY_A', 'raw' );
				
				if(!isset($loc1['term_id'])){
					

					
					$term_array = wp_insert_term( $one_cat, 'product_cat', $args = array() );
					
					if(is_array($term_array)){
						$categories[] = $term_array['term_id'];
					}
					
				} else {
					
					$categories[] = $loc1['term_id'];
				
				}
			
		}
	  

		
		$my_post = array(
					'post_title'    => wp_strip_all_tags(sanitize_text_field($title)),
					'post_type'      => $post_type,
					'post_content'  => $result['description'], //esc_html($result['description']),
					'post_excerpt' => $short_description,
					'post_status'   => 'publish',
					'post_author'   => 1,
					'tax_input' => array(
						'product_cat' => $categories,
						//'product_brand' => array( $brand )
					),
				);

		
		
		
		// Insert the post into the database
		$post_id1 = wp_insert_post( $my_post );
		
		foreach($categories as $one_cat){
		
			wp_set_object_terms( $post_id1, $one_cat, 'product_cat' );
		
		}
		
		//wp_set_object_terms( $post_id1, $category, 'product_cat' );
		
		$new_aur = get_post($post_id1); 
		$slug = $new_aur->post_name;
		
		
		// Update post slug best-price-india-specs-reviews-$post_id1
		  $my_post = array(
			  'ID'           => $post_id1,
			  'post_name'   => $slug."-best-price-specs-reviews-".$post_id1
		  );
		
		// Update the post into the database
		  wp_update_post( $my_post );
	
		$images=$imgs;
		
		$product_type = esc_attr( get_option('product_type') );
			
		$cat_id = get_term_by('name', $product_type, 'product_type');
		
		$cat_ids = array($cat_id->term_id);
		
		$term_taxonomy_ids = wp_set_object_terms( $post_id1, $cat_ids, 'product_type', true );
		
		$last_id = $post_id1;
		
		$yoast_seo_title = $main['title']." Best Price | Specs | Reviews - Lowest Online";
		
		$main['price'] = str_replace(",", "", $main['price']);
		
		
		
		//if(esc_attr( get_option('product_type') )=='simple'){
			
			$markup = esc_attr( get_option('markup_ratio') );
			
			$main['price'] = str_replace("$", "", $main['price']);
			
			if($markup!=NULL && $markup!=0 && $main['price']!=NULL && $main['price']!=0 ){
				
				
				$main['price'] = (float)$main['price'] * (float)$markup;
				
			}
			
			
		//}
		
		if(strpos("?", $original_product_url)){
			$affiliate_link = $original_product_url."&tag=".get_option('affiliate_tag');
		} else {
			$affiliate_link = $original_product_url."?tag=".get_option('affiliate_tag');
		}
		
		update_post_meta($post_id1, '_regular_price', str_replace("$", "", $main['price']));
		//update_post_meta($post_id1, '_stock_update_url', $stock_update_url);
		//update_post_meta($post_id1, '_sale_price', str_replace("$", "", $price));
		update_post_meta($post_id1, '_price', str_replace("$", "", $main['price']));
		update_post_meta($post_id1, '_original_product_url', $original_product_url);
		update_post_meta($post_id1, '_product_url', $affiliate_link);
		update_post_meta($post_id1, '_yoast_wpseo_title', $yoast_seo_title);
		update_post_meta($post_id1, '_sku', $asin);	
		
		//update_post_meta($post_id1, '_my_custom_attribute', "Suhail Ahmad");
		
		
	   $new_specs = array();
	   
	   $new_specs['Asin'] = $asin;
	   $new_specs['Dimensions'] = $dimensions;
	   $new_specs['Weight'] = $weight;
	   $new_specs['Manufacturer'] = $manufacturer;
		
		wcproduct_set_attributes($post_id1, $new_specs, 'color');
		
		echo "<pre>";
		print_r($reviews);
		echo "</pre>";
		
		foreach($reviews as $review){
			
			add_rating_to_product($post_id1, $review['comment_author'], $review['comment_author_email'], (int)$review['comment_rating'], str_replace("style=\"", "abc=\"", $review['comment_content']) );
			
		}
		
		$image_id = media_sideload_image($result['images'][0], $post_id1, $title." Best Price");
		

		
		$img1 = explode("src='", $image_id);
		$img2 = explode("'", $img1[1]);
		
		$img_image = $img2[0];
		
		// Get the Attachment ID
		$attachment_id = get_attachment_id_from_src ($img_image);

		set_post_thumbnail( $post_id1, $attachment_id );
			
		
		$gallery_meta_key = "_product_image_gallery";
		$gallery_meta_value = "";
		
		unset($result['images'][0]);
		
		foreach($result['images'] as $key=>$im){
			
			if($key>=8){
				break;
			}
			
			if($key==1){
				$alttext = " Cheapest Price";
			} elseif ($key==2){
				$alttext = " Lowest Price";
			} elseif ($key==3){
				$alttext = " Affordable Price";
			} elseif ($key==4){
				$alttext = " Reasonable Price";
			}
			
			if(strlen($im)>=3){
				
				$image_id = media_sideload_image($im, $post_id1, $title.$alttext);
				
				$img1 = explode("src='", $image_id);
				$img2 = explode("'", $img1[1]);
				
				$img_image = $img2[0];
				
				// Get the Attachment ID
				$attachment_id = get_attachment_id_from_src ($img_image);
				
				if($attachment_id!=0){
					$gallery_meta_value.= $attachment_id.",";
				}
				
			}
			
			
		}
		
		update_post_meta($post_id1, $gallery_meta_key, $gallery_meta_value);
		
		

		
	}
	
	
}