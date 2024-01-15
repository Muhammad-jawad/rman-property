<?php
// Retrieve property details from post metadata
$display_price = trim(get_post_meta(get_the_ID(), 'displayprice', true));
$property_type = trim(get_post_meta(get_the_ID(), 'TYPE', true));
$no_bedrooms   = trim(get_post_meta(get_the_ID(), 'beds', true));
$no_bathrooms  = trim(get_post_meta(get_the_ID(), 'baths', true));
$no_receptions = trim(get_post_meta(get_the_ID(), 'receps', true));
$geolocation = trim(get_post_meta(get_the_ID(), 'geolocation', true));
$floorplan_images = trim(get_post_meta(get_the_ID(), 'floorplan', true));
$epc_images = get_post_meta(get_the_ID(), 'epc', true);
$brochers_pdfs = trim(get_post_meta(get_the_ID(), 'brochures', true));
$heating = trim(get_post_meta(get_the_ID(), 'heating', true));
$service_charges = trim(get_post_meta(get_the_ID(), 'servicecharges', true));


// This is how you create field
// if there is a new field you want to get use get_post_meta(get_the_ID(), 'FIELD_NAME_FROM_CUSTOM_FIELDS', true)
// 
// Refrence field creat a variable
 $town = get_post_meta(get_the_ID(), 'address4', true);

// then echo inside html like <p>$xyz</p>


$tour_url = "#";

// Retrieve plugin options for properties
$properties_option = get_option('mj_rman_plugin_options');
$enquiry_form = $properties_option['enquiry_form'];

// Extract short description and key features
$short_desc = get_the_excerpt();
$dimensions = get_post_meta(get_the_ID(), 'displayprice', true);
$council_tax = get_post_meta(get_the_ID(), 'tax', true);
$epc_rating = get_post_meta(get_the_ID(), 'displayprice', true);
$key_features_misc = get_post_meta(get_the_ID(), 'bullets', true);

// Parse bullet points into an array if they exist
if ($key_features_misc && !empty($key_features_misc)) {
    $feature_list = preg_split('/\r\n|\r|\n/', $key_features_misc);
}

// Get full property details
$full_details = get_the_content();

//get the title & Permalink
$current_url = urlencode(get_permalink());
$current_title = urlencode(get_the_title());
?>

<!-- Output property title and price -->
<h1 class="property-single__title"><?php the_title(); ?></h1>
<h2 class="property-town"><?php  echo $town; ?></h2>
<p class="property-single__price property-single__price--intro"><?php echo $display_price; ?></p>

<!-- Display property image carousel -->
<div class="property-single__carousel">
    <?php
    $post_id = get_the_ID();
    $media_gallery = get_post_meta($post_id, 'media_gallery', true);
    ?>
    <!-- Check if media gallery exists -->
    <?php if ($media_gallery) : ?>
        <div class="">
            <!-- Main image carousel -->
            <div class="rman-properties-gallery">
                <!-- Swiper container for main images -->
                <div class="swiper-container carousel-properties">
                    <div class="swiper-wrapper">
                        <!-- Loop through images in media gallery -->
                        <?php
                        foreach ($media_gallery as $image) :
                            $id   = $image;
                            $size = 'large';
                            $img  = wp_get_attachment_image($id, $size);
                        ?>
                            <div class="swiper-slide">
                                <div class="image">
                                    <?php echo $img; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Navigation for main carousel -->
                    <div class="swiper-nav">
                        <div class="swiper-nav-prev"><i class="fal fa-chevron-left"></i></div>
                        <div class="swiper-nav-next"><i class="fal fa-chevron-right"></i></div>
                    </div>
                </div>
                <!-- Swiper container for thumbnail images -->
                <div class="swiper-container carousel-properties-thumbs">
                    <div class="swiper-wrapper">
                        <!-- Loop through images in media gallery for thumbnails -->
                        <?php
                        foreach ($media_gallery as $image) :
                            $id   = $image;
                            $size = 'medium';
                            $img  = wp_get_attachment_image($id, $size);
                        ?>
                            <div class="swiper-slide">
                                <?php echo $img; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
    <?php endif; ?>
</div>

<!-- Display property basic details -->
<ul class="property-single__basics">
    <?php if ($property_type) : ?>
        <li class="item">
            <p>Property Type</p>
            <p><i class="fa-regular fa-house"></i><span><?php echo $property_type; ?></span></p>
        </li>
    <?php endif; ?>
    <?php if ($no_bedrooms) : ?>
        <li class="item">
            <p>Bedrooms</p>
            <p><i class="fa-regular fa-bed"></i><span><?php echo 'x ' . $no_bedrooms; ?>
        </li>
    <?php endif; ?>
    <?php if ($no_bathrooms) : ?>
        <li class="item">
            <p>Bathrooms</p>
            <p><i class="fa-regular fa-bath"></i><span><?php echo 'x ' . $no_bathrooms; ?>
        </li>
    <?php endif; ?>
    <?php if ($no_receptions) : ?>
        <li class="item">
            <p>Reception Rooms</p>
            <p><i class="fa-regular fa-loveseat"></i><span><?php echo 'x ' . $no_receptions; ?>
        </li>
    <?php endif; ?>
</ul>

<!-- Display property sharing options -->
<div class="property-single__sharing">
    <div class="buttons">
        <?php if ($enquiry_form) : ?>
            <!-- Display "Make Enquiry" button if form is available -->
            <a href="javascript:void(0)" class="orange-button" id="ma_enquiryform">Make Enquiry</a>
        <?php endif; ?>
        <?php if ($floorplan_images) : ?>
            <!-- Display "Floor Plan" button if images are available -->
            <a href="javascript:void(0)" class="orange-button" id="ma_floorplans">Floor Plan</a>
        <?php endif; ?>
        <?php if ($tour_url) : ?>
            <!-- Display "Virtual Tour" button if URL is available -->
            <a href="<?php echo $tour_url; ?>" class="orange-button">Virtual Tour</a>
        <?php endif; ?>
    </div>
    <div class="share">
        <!-- Display social sharing icons -->
        <a id="ma_wishlist-button" href="javascript:void(0)" class="wishlist-btn"><i class="fa-regular fa-heart"></i></a>
        <a id="ma_social-share" href="javascript:void(0)"><i class="fa-regular fa-share-nodes"></i></a>
        <a id="printButton" href="javascript:void(0)"><i class="fa-regular fa-print"></i></a>
        <div class="social-share-buttons">
            <button data-share="facebook" data-url="<?php echo $current_url; ?>" data-title="<?php echo $current_title; ?>"><i class="fab fa-facebook-f"></i></button>
            <button data-share="twitter" data-url="<?php echo $current_url; ?>" data-title="<?php echo $current_title; ?>"><i class="fab fa-twitter"></i></button>
            <button data-share="linkedin" data-url="<?php echo $current_url; ?>" data-title="<?php echo $current_title; ?>"><i class="fab fa-linkedin-in"></i></button>
            <button data-share="pinterest" data-url="<?php echo $current_url; ?>" data-title="<?php echo $current_title; ?>"><i class="fab fa-pinterest-p"></i></button>
            <button data-share="whatsapp" data-url="<?php echo $current_url; ?>" data-title="<?php echo $current_title; ?>"><i class="fab fa-whatsapp"></i></button>
        <button data-share="gmail" data-url="<?php echo $current_url; ?>" data-title="<?php echo $current_title; ?>"><i class="far fa-envelope"></i></button>
        </div>
    </div>
    
</div>

<?php if ($floorplan_images) : ?>
    <div id="floorplan-gallery" class="mfp-hide">
        <?php
        foreach ($floorplan_images as $image) :
            $id   = $image;
            $size = 'full';
            $img  = wp_get_attachment_image($id, $size);
            $img_url = wp_get_attachment_image_url($id, $size);
        ?>
            <div class="gallery-item">
                <?= $img; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($enquiry_form) : ?>
    <!-- Display enquiry form if available -->
    <div id="ma-popup-form" class="white-popup mfp-hide">
        <?php
        echo do_shortcode("{$enquiry_form}");
        ?>
    </div>
<?php endif; ?>

<?php if ($dimensions || $council_tax || $epc_rating || $key_features_misc) : ?>
<!--     <h2>Key Features</h2> -->
    <div class="key-features">

        <?php if ($council_tax) : ?>
            <!-- Display council tax information -->
            <p>Council Tax: <span><?php echo $council_tax; ?></span></p>
        <?php endif; ?>
		<?php if ($service_charges) : ?>
            <!-- Display council tax information -->
            <p>Service Charges: <span><?php echo $service_charges; ?></span></p>
        <?php endif; ?>
        <?php if ($epc_images) : ?>
            <!-- Display EPC rating and images -->
            <div class="epc-image-container">
                <p>EPC Rating:</p>
                <a href="javascript:void(0)" id="ma_epc-action">
                    <?php 
                        $id   = $epc_images[0];
                        $size = 'full';
                        echo wp_get_attachment_image($id, $size, "" , ["class" => "epc-image"]);
                    ?>
                </a>
                <div id="epc-gallery" class="mfp-hide">
                    <?php
                    foreach ($epc_images as $image) :
                        $id   = $image;
                        $size = 'full';
                        $img  = wp_get_attachment_image($id, $size);
                        $img_url = wp_get_attachment_image_url($id, $size);
                    ?>
                        <div class="gallery-item">
                            <?= $img; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
		 <?php if ($heating) : ?>
            <!-- Display property dimensions -->
            <p>Heating: <span><?php echo $heating; ?></span></p>
        <?php endif; ?>
        <?php if ($feature_list) : ?>
            <!-- Display key features list -->
            <p>Key Features:</p>
            <ul class="key-features__misc">
                <?php
                foreach ($feature_list as $feature) {
                    $feature = trim($feature);

                    if (empty($feature)) {
                        continue;
                    }
                    echo '<li>' . $feature . '</li>';
                }
                ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($full_details) : ?>
    <h2>Full Details</h2>
    <?php echo $full_details; ?>
<?php endif; ?>

<?php
$options = get_option('mj_rman_plugin_options');

if (!empty($geolocation) && !empty($options['google_map_api'])) {
    // Construct the Google Maps URL
    $encoded_geolocation = urlencode($geolocation);
    $map_api_key = $options['google_map_api'];

    $split_lat_long = explode(",", $geolocation);
    $latitude = trim($split_lat_long[0]);
    $longitude = trim($split_lat_long[1]);

    $map_url = "https://www.google.com/maps/embed/v1/place?q={$latitude},{$longitude}&zoom=20";
    $map_url_with_api_key = $map_url . "&key={$map_api_key}";
    ?>
    <!-- Display Google Maps embed iframe -->
    <div id="properties-map-section">
        <iframe
            width="100%"
            height="292"
            frameborder="0"
            style="border:0"
            src="<?php echo $map_url_with_api_key; ?>"
            allowfullscreen>
        </iframe>
    </div>
<?php } ?>