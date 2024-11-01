<div class="div-product-details">
    <a href="#product_details" class="product_bsl_link button alt"><?php _e('View Product Details','ultimate-cross-selling'); ?></a>
</div>
<div id="product_details" class="modal">
    <?php 
        $product_featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($product->get_id()) );

        if (!empty($product_featured_image)) {
            $product_featured_image = $product_featured_image[0];
        } else {
            $product_featured_image = $plugin_path."assets/images/no-image-available.png";
        }

        $parent_product = wc_get_product($product->get_parent_id());
    ?>
    <div class="container">
        <div class="row">
            <div class="col large-6">
                <a class="example-image-link" href="<?php echo $product_featured_image; ?>" data-lightbox="example-set" data-title="<?php echo $product->get_name(); ?>">
                    <img src="<?php echo $product_featured_image; ?>" >
                </a> 
            </div>
   
            <div class="col large-6 product-summary product-info">
                <h1 class="product-title entry-title"><?php echo $product->get_name(); ?></h1>
                <div class="is-divider small"></div>
                <div class="price-wrapper">
                    <p class="price product-page-price ">
                     <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span><?php echo $product->get_price(); ?></span></p>
                </div>
            </div>
         </div>
    </div>
    <div>    
        <?php echo $product->get_description(); ?>
    </div>
</div>