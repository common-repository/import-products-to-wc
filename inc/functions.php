<?php


// create custom plugin settings menu
add_action('admin_menu', 'wcta2w_plugin_menu');

function wcta2w_plugin_menu() {

	//create new top-level menu
	add_menu_page('Add Amazon Products to your WooCommerce Website', 'Amazon Settings', 'administrator', __FILE__, 'wcta2w_plugin_page_3' , plugins_url('/images/icon.png', __DIR__) );
	//add_menu_page('Update Vapornation Stock Status', 'Stock Update', 'administrator', __FILE__."2", 'wcta2w_plugin_page_2' , plugins_url('/images/icon.ico', __FILE__) );
	//add_menu_page('Update Vapornation Fix Products', 'Fix Products', 'administrator', __FILE__."4", 'wcta2w_plugin_page_4_fix' , plugins_url('/images/icon.ico', __FILE__) );
	//add_menu_page('Vapornation settings', 'Settings', 'administrator', __FILE__."3", 'wcta2w_plugin_page_3' , plugins_url('/images/icon.ico', __FILE__) );
		
	//add_submenu_page( __FILE__, 'Update 91mobiles Stock Status', 'Update 91mobiles Stock Status', 'administrator', __FILE__."2", 'wcta2w_plugin_page_2');
	//add_submenu_page( __FILE__, 'Settings', 'Settings', 'administrator', __FILE__."4", 'wcta2w_plugin_page_3');
	//add_submenu_page( __FILE__, '91mobiles settings', '91mobiles settings', 'administrator', __FILE__."3", 'wcta2w_plugin_page_3');
	
	//call register settings function
	add_action( 'admin_init', 'register_wcta2w_plugin' );
}

function register_wcta2w_plugin() {
	//register our settings
	register_setting( 'wct-suhail-settings-group', 'api_key' );
	register_setting( 'wct-suhail-settings-group', 'markup_ratio' );
	register_setting( 'wct-suhail-settings-group', 'affiliate_tag' );
	register_setting( 'wct-suhail-settings-group', 'option_etc' );
	register_setting( 'wct-suhail-settings-group', 'product_type' );
}

function wcta2w_plugin_page() {
?>

<br />
Sample URL: https://www.amazon.com/s?k=led+tv&i=electronics-intl-ship&ref=nb_sb_noss_2 <br />
<br />
<form onSubmit="return wcta2w_get_imdb_data(document.getElementById('get_imdb').value);">
  <label>Please Enter the URL of the category from Amazon.com</label>
  <br />
  <input id="get_imdb" name="get_imdb" type="text" placeholder="search..." style="width:50%;" required />
  <br />
  <br />
  <!--<a href="javascript::void();">Get Data</a>-->
  
  <button type="submit">Fetch</button>
</form>
<br />
<div id="get_all_records" style="font-size:30px;"> <span id="counter1">0</span> / <span id="counter2">0</span> 
   
</div>
<div id="results_imdb"> </div>
<script>

function wcta2w_get_imdb_data(search_term){
	
	document.getElementById('results_imdb').innerHTML = "Searching...";
	document.getElementById('results_imdb').innerHTML = "<img src='<?php echo plugin_dir_url( __DIR__ )."loading.gif"; ?>' width='188'>";
	
	var lin = search_term;
	
	jQuery.ajax({
			type: 'POST',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {
				'search_keyword': lin, 
				'action': 'wcta2w_f711_get_page_results' //this is the name of the AJAX method called in WordPress
			}, success: function (msg) {

					var content = document.createElement('div');
					content.innerHTML = msg;
					document.getElementById('results_imdb').appendChild(content);
					
					var obj = JSON.parse(msg);
			
					//alert(obj.Next)
					
					/*obj.forEach(function(record){
						document.getElementById('results_imdb').appendChild(record);
					});*/
					
					var tt = obj.total;
					
					var i;
					
					for (i = 2; i < tt; i++) {
					  wcta2w_get_single(obj[i].link1);
					  break;
					}
					
					/*if(obj.Next!=""){
						wcta2w_get_imdb_data(obj.Next);
					}*/
			
			},
			error: function () {
				alert("error");
			}
		});
		
		return false;
		
}

function wcta2w_get_single(url1){

	jQuery.ajax({
			type: 'POST',
			url: '<?php echo admin_url('admin-ajax.php'); ?>',
			data: {

				'search_keyword': url1, 
				'action': 'wcta2w_f711_get_product_page_results' //this is the name of the AJAX method called in WordPress
			}, success: function (msg) {
				
					var content = document.createElement('div');
					content.innerHTML = msg;
					document.getElementById('results_imdb').appendChild(content);
					
					var b = document.getElementById("counter2").innerHTML;
					b=parseInt(b)+1;
					document.getElementById("counter2").innerHTML = b;
			
			},
			error: function () {
				alert("error");
			}
		});
		
		
		var a = document.getElementById("counter1").innerHTML;
		a=parseInt(a)+1;
		document.getElementById("counter1").innerHTML = a;	
		
		
		
}

</script>
<?php

} 

function wcta2w_plugin_page_3() {
	?>
    
    <table>
        <tr>
        	<td style='padding:50px;'>
                <form method="post" action="options.php">
                  <?php settings_fields( 'wct-suhail-settings-group' ); ?>
                  <?php do_settings_sections( 'wct-suhail-settings-group' ); ?>
                  <h2>You can change options below</h2>
                  <h3>Copy below value and paste in your Dashboard in Target Website Field</h3>
                  <label><b>Site Key</b> <input type="text" name="api_key" value="<?php echo base64_encode(site_url()); ?>" id="myInput"  style="width:50%" readonly="readonly" /> </label>
                  <button onclick="wcta2w_myFunction()">Copy</button>
                  <br />
                  <p>After clicking on copy button open <a href="https://importwoocommerce.com/my-profile">https://importwoocommerce.com/my-profile</a> and paste that code in the Target Website Field</p>
                  
                  <table class="form-table">
                    <tr valign="top">
                      <th scope="row">Please enter markup ratio such as Price X 2.5 (2.5 is the value if required) Only for Dropship products</th>
                    </tr>
                    <tr>
                      <td><input type="text" name="markup_ratio" value="<?php echo esc_attr( get_option('markup_ratio') ); ?>" placeholder="Example 2.5" /></td>
                    </tr>
                    <tr>
                      <td><p>Please select the Type for Uploaded Products using WCT Amazon to WooCommerce plugin</p>
                        <select name="product_type" >
                          <option value='external' <?php if(esc_attr( get_option('product_type') )=='external'){ echo "selected"; } ?>>Affiliate Products / External Products</option>
                          <option value='simple' <?php if(esc_attr( get_option('product_type') )=='simple'){ echo "selected"; } ?>>Sell Products / Simple Products</option>
                        </select></td>
                    </tr>
                    <tr valign="top">
                      <th scope="row">Please enter your Amazon.com affiliate tag you can find it in your affiliate url e.g. www.amazon.com/xx00/abc-product/dp/09Bkkl0l?ref=abc&tag=myowntag-20 <br>
                        in this url the myowntag-20 is your affiliate id. (Only for Affiliate Products)</th>
                    </tr>
                    <tr>
                      <td><input type="text" name="affiliate_tag" value="<?php echo esc_attr( get_option('affiliate_tag') ); ?>" placeholder="myowntag-20" /></td>
                    </tr>
                  </table>
                  <?php submit_button('Save Options'); ?>
                </form>
			</td>
            
            <td>
            	<iframe width="560" height="315" src="https://www.youtube.com/embed/KDm4ugV0SQw?si=OhsUPv8EzWO6T9g0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </td>
            
       </tr>
            
</table>



<script>
function wcta2w_myFunction() {
  // Get the text field
  var copyText = document.getElementById("myInput");

  // Select the text field
  copyText.select();
  copyText.setSelectionRange(0, 99999); // For mobile devices

   // Copy the text inside the text field
  navigator.clipboard.writeText(copyText.value);

  // Alert the copied text
  alert("Copied the text: " + copyText.value);
}
</script>
<?php
}

function get_attachment_id_from_src ($image_src) {
  global $wpdb;
  
  
  $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
	

  $id = $wpdb->get_var($wpdb->prepare($query));
  return $id;

}

// Rest API endpoint

add_action( 'rest_api_init', function () {
  register_rest_route( 'wct-get-urls/v1', '/keyword', array(
    'methods' => 'POST',
    'callback' => 'wcta2w_get_urls_callback',
  ) );
} );

function wcta2w_get_urls_callback(){
	
	$url = sanitize_url( $_POST['url'] );
	
	wcta2w_get_record($url, $page=1);
	
	//echo json_encode($j);
	
}

// Add Single Product OLD End Point

add_action( 'rest_api_init', function () {
  register_rest_route( 'wct-get-product/v1', '/keyword', array(
    'methods' => 'POST',
    'callback' => 'wcta2w_get_product_callback',
  ) );
} );

function wcta2w_get_product_callback(){
	
	$url = sanitize_url( $_POST['url'] );
	
	wcta2w_get_single_product($url);
	
	//echo json_encode($j);
	
}


// Add Single Amazon Product New End Point

add_action( 'rest_api_init', function () {
  register_rest_route( 'wct-get-amazon-product/v1', '/keyword', array(
    'methods' => 'POST',
    'callback' => 'wcta2w_get_amazon_product_callback',
  ) );
} );

function wcta2w_get_amazon_product_callback(){
	
	$url = $_POST;
	
	$data = wcta2w_get_single_amazon_product($url);
	
	//return $data;
	echo json_encode($data);
	
}

function curl_get_contents($url){
	
	$headers = array(
	'Content-Type: charset=utf-8', 
	'content-type: application/json; charset=utf-8', 
	//'date: Mon, 30 Jan 2023 10:45:47 GMT', 
	'request-context: appId=cid-v1:39b63cbb-9699-45dd-989e-394c13312798', 
	'access-control-allow-credentials: true', 
	'access-control-allow-origin: https://www.newwave.no', 
	'content-encoding: gzip', 
	//'content-length: 2389'
	);
	
	$headers2 = array(
		'accept: application/json, text/plain, */*',
		'accept-encoding: gzip, deflate, br',
		'accept-language: en-US,en;q=0.9',
		'contextid: 6A44A7B9-971E-42BD-9760-EE6AE36D3DE7',
		'origin: https://www.newwave.no',
		'referer: https://www.newwave.no/',
		'sec-ch-ua: "Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
		'sec-ch-ua-mobile: ?0',
		'sec-ch-ua-platform: "Windows"',
		'sec-fetch-dest: empty',
		'sec-fetch-mode: cors',
		'sec-fetch-site: cross-site',
		'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
	
	);
	
	//echo $url."<br>";
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  //CURLOPT_URL => 'https://35.170.158.40/api/city?prefix='.$search_term,
	  //CURLOPT_URL => 'http://44.202.249.53:8080/api/city?prefix='.$search_term,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_SSL_VERIFYPEER => false,
	  CURLOPT_HTTPHEADER => $headers2,
	));
	
	$response = curl_exec($curl);
	
	curl_close($curl);
	return $response;

}

function add_rating_to_product($product_id, $author_name, $author_email, $rating, $review_content){
	
	echo "Add Review<br>";
	
	echo $product_id.": ".$author_name.": ".$author_email.": ".$rating.": ".$review_content."<br>";
	
	
	$d = "13-04-2020";
	
	$int= rand(1687193880,strtotime($d));
	
	// Create the review data array
	$review_data = array(
		'comment_post_ID' => $product_id,
		'comment_author' => $author_name,
		'comment_type' => 'review',
		'comment_author_email' => $author_email,
		'comment_rating' => $rating,
		'comment_content' => $review_content,
		'comment_date'         => date('Y-m-d H:i:s', $int),
	);
	
	// Insert the review
	$comment_id = wp_insert_comment($review_data);
	update_comment_meta( $comment_id, 'rating', $rating );
	// Optionally, you can approve the comment automatically
	$comment = get_comment($comment_id);
	$comment->comment_approved = 1;
	//wp_update_comment($comment);
	
	/*$product = wc_get_product($product_id);
	$product->update_review_count();
	$product->update_average_rating();*/
	
}





//https://www.youtube.com/watch?v=T0i2ev9s9KI


// Function to display the backend notice with image and buttons
function display_custom_backend_notice() {
    // Check if the notice has been dismissed
    $is_dismissed = get_user_meta(get_current_user_id(), 'custom_notice_dismissed', true);
    if ($is_dismissed !== '1') {
        echo '<div class="notice notice-warning">';
        echo '<div style="display:flex; align-items:center;">';
        echo '<img src="https://importwoocommerce.com/wp-content/uploads/2024/03/How-to-Import-Amazon-Products-to-WooCommerce-with-Reviews-Free-Tool.png" style="max-width: 200px; margin-right: 10px;" />';
        echo '<p><strong>Important Notice:</strong> We have Launched the new Amazon to WooCommerce Plugin Feature which is 100% Afficient.<br> You can try it for Free and then we have a very basic pricing per month package. </p>';
        echo '</div>';
        echo '<p><a href="https://importwoocommerce.com/get-amazon-data/" target="_blank" class="button button-primary">Check Now</a> <a href="'.site_url().'/wp-admin/admin.php?page=import-products-to-wc/inc/functions.php" class="button button-secondary">Watch Video</a></p>';
        echo '</div>';
    }
}

// Hook the function to display the notice in admin
add_action('admin_notices', 'display_custom_backend_notice');

// Function to handle the dismissal of the notice
function handle_custom_notice_dismissal() {
    // Do nothing
}

// Hook the function to handle dismissal
add_action('admin_init', 'handle_custom_notice_dismissal');
