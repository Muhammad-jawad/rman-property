<?php
// var_dump($_GET['lat']);
// if (isset($_GET['lat']) && isset($_GET['lng'])) {
    
    // Retrieve latitude and longitude from the frontend request (you may need to handle security checks here)
    // $latitude = $_GET['lat'];
    // $longitude = $_GET['lng'];
    $latitude = 51.125580;
    $longitude = -2.742173;

    // Your Google Maps API key
    $map_api_key = $options['google_map_api'];

    // Create the URL for the static map using the Google Maps API
    $map_url = "https://www.google.com/maps/embed/v1/place?q={$latitude},{$longitude}&zoom=20&key={$map_api_key}";

    // Fetch the map data from the Google Maps API
    $map_data = file_get_contents($map_url);

    // Set the appropriate response headers
    header('Content-Type: text/html');
    header('Cache-Control: public, max-age=3600'); // Cache the response for 1 hour

    // Output the map data
    echo $map_data;
// } 