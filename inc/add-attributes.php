<?php

if (!function_exists('wcproduct_set_attributes')) {

	function wcproduct_set_attributes($post_id, $attributes, $attribute_name, $visible=1, $variation=0) {
		
		/*global $wpdb;
		
		$table_prefix = $wpdb->prefix;*/
		
		/*echo "<pre>";
		print_r($attributes);
		echo "</pre>";*/
		
		$i = 1;
		// Loop through the attributes array
		foreach ($attributes as $name => $value) {
			
			$is_taxonomy = 0;
			
			//$term_taxonomy_ids = wp_set_object_terms( $post_id, $value, str_replace(" ", "_", "pa_".strtolower($name)), true );
			
			$product_attributes[str_replace(" ", "_", "pa_".strtolower($name))] = array (
				'name' => htmlspecialchars( stripslashes( $name ) ), // set attribute name
				'value' => $value, // set attribute value
				'position' => $i,
				'is_visible' => $visible,
				'is_variation' => $variation,
				'is_taxonomy' => $is_taxonomy
			);
	
			$i++;
			
		}
	
		/*echo "<pre>";
		print_r($product_attributes);
		echo "</pre>";*/
		
		/*echo "<pre>";
		$new_attribute = unserialize('a:2:{s:5:"color";a:6:{s:4:"name";s:5:"Color";s:5:"value";s:5:"white";s:8:"position";i:0;s:10:"is_visible";i:1;s:12:"is_variation";i:0;s:11:"is_taxonomy";i:0;}s:4:"size";a:6:{s:4:"name";s:4:"Size";s:5:"value";s:2:"20";s:8:"position";i:1;s:10:"is_visible";i:1;s:12:"is_variation";i:0;s:11:"is_taxonomy";i:0;}}');
		echo "</pre>";*/
		
		// Now update the post with its new attributes
		//update_post_meta($post_id, '_my_custom_attribute', "Suhail Ahmad");
		update_post_meta($post_id, '_product_attributes', $product_attributes);
		
	}

}