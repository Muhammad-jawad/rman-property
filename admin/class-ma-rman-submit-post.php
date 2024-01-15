<?php 
/**
 * Ma_rman_submit_post class handles submitting posts and their featured images from Rentman data.
 */
class Ma_rman_submit_post {
    private $post_data;
    private $token;
    private $properties_ref;

    private $json_responses;

    /**
     * Constructor to initialize the class with Rentman post data and API token.
     *
     * @param array  $post_data Rentman post data to submit.
     * @param string $token     Rentman API token.
     */
    public function __construct($post_data, $token) {
        $this->post_data = $post_data;
        $this->token = $token;
        $this->properties_ref = array();
        $this->json_responses = array();
    }

    /**
     * Process each property in the post data and submit posts with featured images.
     */
    public function process_properties() {

        $index = 0;
        foreach ($this->post_data as $property) {
            // if($index > 0) {
            //     continue;
            // }
            if (!in_array($property["propref"], $this->properties_ref)) {
                $this->properties_ref[] = $property["propref"];
                $media_data = $this->fetch_property_media($property["propref"]);
                $images_array = $this->categorize_media_urls($media_data);

                $this->check_and_insert_post($property, $images_array);
            }
            $index++;   
        }

        $this->check_post_avaialble_in_properties();

        $this->generate_json_data(implode($this->json_responses));
    }

    /**
     * Fetch property media data from Rentman API.
     *
     * @param string $propref Property reference.
     * @return array Rentman property media data.
     */
    public function fetch_property_media($propref): array {
        $url = "https://www.rentman.online/propertymedia.php?token={$this->token}&propref={$propref}";
        $curl = curl_init($url);
        $headers = array(
            'Accept: application/json'
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        if (!empty($response)) {
            $data = json_decode($response, true);
            if (is_array($data)) {
                return $data;
            }
        } else {

            $this->generate_json_data("No response from the server.");
        }
    }

    /**
     * Detect the MIME type of the image based on Base64 data.
     *
     * @param string $base64 Base64 encoded image data.
     * @return string|bool MIME type of the image or false if not found.
     */
    public function detectMimeType(string $base64) {
        $signaturesForBase64 = array(
            'JVBERi0' => "application/pdf",
            'R0lGODdh' => "image/gif",
            'R0lGODlh' => "image/gif",
            'iVBORw0KGgo' => "image/png",
            '/9j/' => "image/jpeg"
        );

        foreach ($signaturesForBase64 as $sign => $mimeType) {
            if (strpos($base64, $sign) === 0) {
                return $mimeType;
            }
        }

        return false;
    }

    /**
     * Categorize media URLs based on their imgorder.
     *
     * @param array $image_base Media URLs with imgorder information.
     * @return array Categorized media URLs.
     */
    public function categorize_media_urls($image_base): array {
        $floorplan = $epc = $brochures = $media_gallery = array();

        foreach ($image_base as $image) {
            $file_type = $this->detectMimeType($image["base64data"]);

            if ($image["imgorder"] == 9005) {
                $floorplan[] = array(
                    "file_type" => $file_type,
                    "file_base64" => $image["base64data"]
                );
            } elseif ($image["imgorder"] == 9000) {
                $epc[] = array(
                    "file_type" => $file_type,
                    "file_base64" => $image["base64data"]
                );
            } elseif ($image["imgorder"] == 9006) {
                $brochures[] = array(
                    "file_type" => $file_type,
                    "file_base64" => $image["base64data"]
                );
            } else {
                $media_gallery[] = array(
                    "file_type" => $file_type,
                    "file_base64" => $image["base64data"]
                );
            }
        }

        $image_data = array(
            "floorplan" => $floorplan,
            "epc" => $epc,
            "brochures" => $brochures,
            "media_gallery" => $media_gallery
        );
    
        return $image_data;
    }

    /**
     * Upload the Base64 image data to WordPress media library and return the attachment ID.
     *
     * @param string   $base64_data Base64 encoded image data.
     * @param string   $filetype    MIME type of the image.
     * @param int|null $post_id     Post ID to attach the image to (optional).
     * @param string   $image_name  Name to use for the image file.
     * @return int|bool Attachment ID on success, false on failure.
     */
    public function upload_base64_image_to_media($base64_data, $filetype, $post_id = null, $image_name) {
        $binary_data = base64_decode($base64_data);
        $token = "/";
        $extension = "";
        $index = strpos($filetype, $token);
        if ($index !== false) {
            $extension = explode($token, $filetype, 2)[1];
        }
        $filename = $image_name . '_' . time() . rand() . '.' . $extension;
        $upload = wp_upload_bits($filename, null, $binary_data, date('Y-m', time()));

        if (!$upload['error']) {
            $attachment_id = wp_insert_attachment(
                array(
                    'guid' => $upload['url'],
                    'post_mime_type' => $filetype,
                    'post_title' => $filename,
                    'post_content' => '',
                    'post_status' => 'inherit',
                ),
                $upload['file'],
                $post_id
            );

            if (!is_wp_error($attachment_id)) {
                return $attachment_id;
            }
        }

        return false;
    }

    /**
     * Set the uploaded image as the featured image for the post.
     *
     * @param int $post_id        Post ID to set the featured image for.
     * @param int $attachment_id  Attachment ID of the uploaded image.
     * @return bool True on success, false on failure.
     */
    public function set_featured_image($post_id, $attachment_id) {
        if (set_post_thumbnail($post_id, $attachment_id)) {
            return true;
        }

        return false;
    }

    /**
     * Add custom fields and attachments to the post meta.
     *
     * @param int    $post_id           Post ID to attach the custom fields.
     * @param array  $custom_fields_data Custom fields data with base64 image and file type.
     * @param string $image_name        Name to use for the image file.
     * @return void
     */
    public function add_custom_fields_to_post_meta($post_id, $custom_fields_data, $image_name): void {
        if (empty($custom_fields_data) || !is_array($custom_fields_data)) {
            return;
        }

        foreach ($custom_fields_data as $field_key => $field_data) {
            if (!is_array($field_data) || empty($field_data)) {
                continue;
            }
            $gallery_data = array();
            foreach ($field_data as $media) {
                if (isset($media['file_base64']) && isset($media['file_type'])) {
                    $attachment_id = $this->upload_base64_image_to_media($media['file_base64'], $media['file_type'], $post_id, $image_name);
                    $gallery_data[] = $attachment_id;
                }
            }
            if (!empty($gallery_data)) {
                add_post_meta($post_id, $field_key, $gallery_data, false);
            }
        }
    }

    /**
     * Delete post, attachments, and custom fields if ID is not in the array.
     *
     * @param int   $post_id        Post ID to check and delete.
     * @param array $properties_refs Array of custom field IDs to compare against.
     * @return void
     */
    public function delete_post_with_attachments_and_custom_fields_if_id_not_in_array($post_id, $properties_refs): void {
        if (empty($post_id) || !is_numeric($post_id)) {
            return;
        }
		

        $propref = get_post_meta($post_id, 'propref', true);

        if (!in_array($propref, $properties_refs)) {
            $attachments = get_posts(array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_parent' => $post_id
            ));

            foreach ($attachments as $attachment) {
                wp_delete_attachment($attachment->ID, true);
            }

            wp_delete_post($post_id, true);
        }
    }

    /**
     * Check if posts are available in properties, and delete them if not in the properties_refs array.
     *
     * @return void
     */
    public function check_post_avaialble_in_properties(): void {
        // Setup query to show the 'properties' post type with all posts.
        $args = array(
            'post_type' => 'properties',
            'posts_per_page' => -1,
            'order' => 'ASC',
        );

        // Get all properties posts.
        $loop = new WP_Query($args);

        while ($loop->have_posts()) : $loop->the_post();
            // Check if the post ID exists in the properties_refs array and delete if not.
            $this->delete_post_with_attachments_and_custom_fields_if_id_not_in_array(get_the_ID(), $this->properties_ref);
        endwhile;

        wp_reset_postdata();
    }

    /**
     * Check if the post exists, insert it if not, and set the featured image.
     *
     * @param array $property Rentman property data.
     * @param array $images   Categorized media URLs.
     * @return void
     */
    public function check_and_insert_post($property, $images) {
        $post_title = $property["displayaddress"];
        $featured_image_base64 = $images['media_gallery'][0]["file_base64"];
        $filetype = $images['media_gallery'][0]["file_type"];
		
		$propref = $property['propref'];
		
		
        $post_type = "properties";
		
		
		$existing_post = get_posts(array(
			'post_type' => $post_type,
			'meta_key' => 'propref',
			'meta_value' => $propref,
			'posts_per_page' => 1,
		));

//         $existing_post = get_page_by_path(sanitize_title($post_title), OBJECT, $post_type);
		
		 $exclude_data = ["displayaddress", "comments", "DESCRIPTION", "photo1binary", "floorplan", "photo1", "photo2", "photo3", "photo4", "photo5", "photo6", "photo7", "photo8", "photo9", "photo1binary", "epc", "brochure"];

        //$existing_post = post_exists( $post_title,'','',$post_type);

        if (empty($existing_post)) {

            $post_content = $property["comments"];
            $post_excerpt = $property["DESCRIPTION"];
            $post_author = 1;
            $post_status = 'publish';

            $new_post = array(
                'post_title'   => $post_title,
                'post_content' => $post_content,
                'post_status'  => $post_status,
                'post_author'  => $post_author,
                'post_type'    => $post_type,
                'post_excerpt' => $post_excerpt,
            );

            $post_id = wp_insert_post($new_post);

            if ($post_id) {

                $this->json_responses[] = "Post inserted with ID: " . $post_id . "<br>";

                foreach ($property as $key => $value) {
                    if (in_array($key, $exclude_data)) {
                        continue;
                    }
                    add_post_meta($post_id, $key, $value, true);
                }

                $image_data = base64_decode($featured_image_base64);
                $image_title = sanitize_title($post_title);

                if (is_array($images)) {
                    $this->add_custom_fields_to_post_meta($post_id, $images, $image_title);
                }

                if ($image_data) {
                    
                    $featured_attachment_id = $this->upload_base64_image_to_media($featured_image_base64, $filetype, $post_id, $image_title);

                    if ($featured_attachment_id !== false) {

                        $this->json_responses[] = "Featured image upload in media folder<br>";

                        $result = $this->set_featured_image($post_id, $featured_attachment_id);

                        if ($result) {

                            $this->json_responses[] = "Featured image is set for post ID : " . $post_id . "<br>";

                        } else {

                            $this->json_responses[] = "Failed to set the featured image for the post " . $post_id . "<br>";
                        
                        }
                    } else {

                        $this->json_responses[] = "Failed to upload the image to the media library.";

                    }
                } else {

                    $this->json_responses[] = "Failed to decode the base64 image data.";

                }
            } else {

                $this->json_responses[] = "Error inserting post.";

            }
        } else {
            
            $this->json_responses[] = "Post already exists with ID: " . $existing_post[0]->ID . "<br>";
			// Update the existing post
			$post_id = $existing_post[0]->ID;
			$post_title = $property["displayaddress"];
			$post_content = $property["comments"];
			$post_excerpt = $property["DESCRIPTION"];

			$updated_post = array(
				'ID'           => $post_id,
				'post_title'   => $post_title,
				'post_content' => $post_content,
				'post_excerpt' => $post_excerpt,
			);
			
			wp_update_post($updated_post);
			
			 foreach ($property as $key => $value) {
				if (in_array($key, $exclude_data)) {
					continue;
				}
				update_post_meta($post_id, $key, $value);
			}
			
			$this->json_responses[] = "Post updated with ID: " . $post_id . "<br>";

        }
    }


    public function generate_json_data($message = null) {

        $response = array(
            'message' => $message,
        );
        echo json_encode($response);
        die();
    }
}

