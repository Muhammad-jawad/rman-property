<?php



get_header();

?>



<div class="fl-row-content fl-row-fixed-width fl-node-content">

	<!-- <div class="row"> -->

		<!-- <div class="fl-content col-md-12"> -->

			<?php



            $response = "";



			if ( have_posts() ) :

				while ( have_posts() ) :

					the_post();

                    

                    // Main Details

                    ob_start();



                    // Include the template and pass $post_data

                    include plugin_dir_path(dirname(__FILE__)) . "partials/ma-rman-properties-single-template.php";

        

                    // Get the buffered output and append to the response

                    $response .= ob_get_clean();



				endwhile;



                echo $response;

                

			endif;

			?>

		<!-- </div> -->

	<!-- </div> -->

</div>



<?php



get_footer();



