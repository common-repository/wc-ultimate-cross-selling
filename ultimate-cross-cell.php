<?php
/*
Plugin Name: WC Ultimate Cross Selling
Plugin URI: #
Description: WC Ultimate Cross Selling Allows You To Exclusively Cross Sell More Products On Product Details Page. Keep your customers engaged and your products fresh in mind. Bring in more sales with little effort.
Version: 1.0
Author: Technovartz Services
Author URI: http://technovartz.com/
Copyright: Technovartz Services
Text Domain: ultimate-cross-selling
Domain Path: /lang
*/


/***************************
* Get Current WC Version.
***************************/

function ultimate_cross_selling_check_wc_version() {
  //Checking if get_plugins is available.
  if( !function_exists( 'get_plugins' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
  }

  //Adding required variables
  $woo_folder = get_plugins( '/' . 'woocommerce' );
  $woo_file = 'woocommerce.php';

  //Checking if Version number is set.
  if( isset( $woo_folder[$woo_file]['Version'] ) ) {
    return $woo_folder[$woo_file]['Version'];
  } else {
    return NULL;
  }

}


/***************************
* Activation Notice
***************************/
$woo_version = ultimate_cross_selling_check_wc_version();

if ( $woo_version < 2.4 ) {

  register_activation_hook(__FILE__, 'hpy_plugin_activation');

  function ultimate_cross_selling_plugin_activation() {
    $notices= get_option('ultimate_cross_selling_plugin_deferred_admin_notices', array());
    $notices[]= "Attention: WooCommerce Force Default Variant requires at least WooCommerce Version 2.5, you currently have " . hpy_check_wc_version() . ". Please update WooCommerce before activating this plugin.";
    update_option('ultimate_cross_selling_plugin_deferred_admin_notices', $notices);
  }

  add_action('admin_notices', 'ultimate_cross_selling_plugin_admin_notices');
  function ultimate_cross_selling_plugin_admin_notices() {
    if ($notices= get_option('ultimate_cross_selling_plugin_deferred_admin_notices')) {
      foreach ($notices as $notice) {
        echo "<div id='message' class='error'><p>$notice</p></div>";
      }
      delete_option('ultimate_cross_selling_plugin_deferred_admin_notices');
    }
    deactivate_plugins( plugin_basename( __FILE__ ) );
  }

}

/**************************************
* Check If Theme Supports Woocommmerce
***************************************/
function ultimate_cross_selling_add_woocommerce_support() {
  if (get_theme_support('woocommerce') != true) {
	   add_theme_support( 'woocommerce' );
  }
}
add_action( 'after_setup_theme', 'ultimate_cross_selling_add_woocommerce_support' );

/**************************************
* Plugin Scripts
***************************************/
add_action( 'wp_enqueue_scripts', 'ultimate_cross_selling_style' );
function ultimate_cross_selling_style() {

  if (is_product()) {

    wp_enqueue_script('jquery');

    wp_enqueue_style ( 'select-style', plugin_dir_url( __FILE__ ) . 'assets/css/select2.css');
    wp_enqueue_style ( 'modal-style' , plugin_dir_url( __FILE__ ) . 'assets/css/jquery.modal.min.css' );
    wp_enqueue_style ( 'custom-style' , plugin_dir_url( __FILE__ ). 'assets/css/ultimate-cross-sell-custom.css' );
    wp_enqueue_style ( 'custom-responsive-style' , plugin_dir_url( __FILE__ ). 'assets/css/responsive.css' );

    wp_enqueue_script( 'select-js' , plugin_dir_url( __FILE__ ) . 'assets/js/select2.js');
    wp_enqueue_script( 'modal-js'  , plugin_dir_url( __FILE__ ) . 'assets/js/jquery.modal.min.js', array('jquery') );
    wp_enqueue_script( 'custom-js' , plugin_dir_url( __FILE__ ) . 'assets/js/ultimate-cross-sell-custom.js', array('jquery') );

  }

}

/**************************************
* Save Product Fileds
***************************************/
add_action('woocommerce_process_product_meta', 'ultimate_cross_selling_fields_save');
function ultimate_cross_selling_fields_save($post_id) {

  	$enable_second_product  = isset( $_POST['ultimate_cross_selling_enable_second_product'] ) ? __('yes', 'ultimate-cross-selling' ) : __('no','ultimate-cross-selling');
    $second_field_type    = array_map( 'absint', $_POST['ultimate_cross_selling_second_products'] );
    $header_text            = sanitize_text_field( $_POST['ultimate_cross_selling_header_text'] );

    update_post_meta( $post_id, 'ultimate_cross_selling_header_text', $header_text);
    update_post_meta( $post_id, 'ultimate_cross_selling_enable_second_product', $enable_second_product );
    update_post_meta( $post_id, 'ultimate_cross_selling_second_products_ids', $second_field_type);
}

/**************************************
* Add Data Tab To Product Page
***************************************/

add_filter( 'woocommerce_product_data_tabs', 'ultimate_cross_selling_cross_sell_products_data_tab' );
function ultimate_cross_selling_cross_sell_products_data_tab( $product_data_tabs ) {
    $product_data_tabs['additional-products'] = array(
        'label' => __( 'Cross Sells', 'ultimate-cross-selling' ),
        'target' => 'additional_products_data',
        'class'     => array( 'show_if_simple','show_if_variable','show_if_grouped' ),
    );
    return $product_data_tabs;
}

/**************************************
* Data Panels For Cross Sell
***************************************/

add_action('woocommerce_product_data_panels', 'ultimate_cross_selling_data_fields');
function ultimate_cross_selling_data_fields() {
    global $woocommerce,$post; ?>
  	<div id='additional_products_data' class='panel woocommerce_options_panel'> 
  		<div class = 'options_group' >
      		<div style="border-bottom: 1px solid #eee;">
  			    <?php
    					
              		woocommerce_wp_checkbox( 
    					    array( 
    					      'id' => 'ultimate_cross_selling_enable_second_product', 
    					      'label' => __('Enable Cross Sell', 'ultimate-cross-selling' )
    					    )
    				);
  			    
		            $ultimate_cross_selling_header_text = array(
		                'id' => 'ultimate_cross_selling_header_text',
		                'label' => __( 'Header Text', 'textdomain' ),
		                'data_type' => ''
		            );
                
              		woocommerce_wp_text_input( $ultimate_cross_selling_header_text );
            	?>
  			    <p class="form-field">
  			        <label for="menu_products"><?php esc_html_e('Cross Sell Product', 'woocommerce'); ?></label>
  			        <select class="wc-product-search" style="width: 70%;" multiple="multiple" id="menu_products" name="ultimate_cross_selling_second_products[]" data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="ultimate_cross_selling_json_search_only_variations_products" data-exclude="<?php echo intval($post->ID); ?>">
  			            <?php
  			            $product_ids = get_post_meta($post->ID, 'ultimate_cross_selling_second_products_ids', true);

  			            foreach ($product_ids as $product_id) {
  			                $product = wc_get_product($product_id);
  			                if (is_object($product)) {
  			                    echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
  			                }
  			            }
  			            ?>
  			        </select> <?php echo wc_help_tip(__('Select Products Here.', 'ultimate-cross-selling')); ?>
  			    </p>
  			</div>
       	</div>
  </div><?php
}

/**************************************
* Show Cross Sell On Product Page
***************************************/

add_action( 'woocommerce_share', 'ultimate_cross_selling_show_cross_sell_single_page', 10 );
function ultimate_cross_selling_show_cross_sell_single_page() {

    global $product;

    $second_products_ids = get_post_meta($product->get_id(),'ultimate_cross_selling_second_products_ids',true);
    $header_text         = get_post_meta($product->get_id(), 'ultimate_cross_selling_header_text', true);

    if ( isset($_REQUEST['selectedproduct']) ) {
      $selected_product = sanitize_text_field( $_REQUEST['selectedproduct'] );  
    } else {
      $selected_product = '';
    }

    if (isset($selected_product) && $selected_product != '') {
      $second_products_id_first = $selected_product;
    } else {
      $second_products_id_first = $second_products_ids[0]; 
    }
    
    if (isset($second_products_ids) && !empty($second_products_ids)) { ?>
      <div>

        <?php if($header_text != '') { ?>
          <h3 class="cross-sell-title"><?php esc_html_e($header_text,'ultimate-cross-selling')?></h3>
        <?php } ?>

        <select name="cross-sell-product" id="select-binding">              
              <option value=""><?php esc_html_e('Select Binding','ultimate-cross-selling'); ?></option>              
              <?php

                if (is_array($second_products_ids) && !empty($second_products_ids)) {

                  foreach ($second_products_ids as $second_product_key => $second_product_value) { 
                    $product_image = wp_get_attachment_image_src( get_post_thumbnail_id($second_product_value) );
                  ?>
                      <option data-price="<?php echo $product->get_price();  ?>" data-img_src="<?php echo $product_image[0]; ?>" value="<?php echo $second_product_value; ?>" <?php if ($second_products_id_first ==  $second_product_value): ?> selected <?php endif ?>><?php echo get_the_title($second_product_value); ?></option>
                  <?php } ?>
              <?php } ?>
        </select>
    
        <?php

          $product     = wc_get_product($second_products_id_first);
          $plugin_path = plugin_dir_url( __FILE__ );

          include('woocommerce/single-product/add-to-cart/simple.php');
          include('includes/product-details.php');
        ?>
      </div>
    <?php } ?>
<?php
}

/**************************************
* Product Search For Variation
***************************************/
function ultimate_cross_selling_json_search_only_variations_products($term) {
    $searchTermForCalendarWeek = sanitize_text_field ( $_REQUEST['term'] );

    $args = array(
        's' => $searchTermForCalendarWeek,
        'post_type' => 'product_variation'
    );

    $resultCalendarWeeksQuery = new WP_Query($args);

    if ($resultCalendarWeeksQuery->have_posts()) :

        while ($resultCalendarWeeksQuery->have_posts()) : $resultCalendarWeeksQuery->the_post();

          $resultCalendarWeeksArray[get_the_ID()] = get_the_title();
            
        endwhile;
    endif;

    wp_reset_postdata();

    wp_send_json($resultCalendarWeeksArray);
    exit;
}

add_action('wp_ajax_ultimate_cross_selling_json_search_only_variations_products', 'ultimate_cross_selling_json_search_only_variations_products');
add_action('wp_ajax_nopriv_ultimate_cross_selling_json_search_only_variations_products', 'ultimate_cross_selling_json_search_only_variations_products');