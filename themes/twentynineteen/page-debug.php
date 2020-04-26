<?php
/**
 * Template Name: Debug File
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			This is a test template.
			<br/>

<?php

		echo "Obtain array of Box ID's <br/>";
		$box_array = Patt_Custom_Func::fetch_box_id(1);
		print_r($box_array);
		echo '<hr/>';
		
		echo "Convert PATT Request ID to DB ID<br/>";
		$GLOBALS['id'] = $_GET['id'];
		echo $request_id;
		echo Patt_Custom_Func::convert_request_id($GLOBALS['id']);
		echo '<hr/>';
			
		echo "Obtain array of Box Information for frontend/backend Request Details page <br/>";
		$box_details_array = Patt_Custom_Func::fetch_box_details(1);
		print_r($box_details_array);
		echo '<hr/>';

		echo "Function to obtain location value from database <br/>";
        $box_location = Patt_Custom_Func::fetch_location(1);
		print_r($box_location);
		echo '<hr/>';

		echo "Function to obtain program office from database <br/>";
        $box_program_office = Patt_Custom_Func::fetch_program_office(1);
		print_r($box_program_office);
		echo '<hr/>';

        echo 'Function to obtain shelf from database <br/>';
        $box_shelf = Patt_Custom_Func::fetch_shelf(1);
		print_r($box_shelf);
		echo '<hr/>';

        echo 'Function to obtain bay from database <br/>';
		$box_bay = Patt_Custom_Func::fetch_bay(1);
		print_r($box_bay);
		echo '<hr/>';

        echo 'Function to obtain create month and year from database <br/>';
		$box_date = Patt_Custom_Func::fetch_create_date(1);
		print_r($box_date);
		echo '<hr/>';

        echo 'Function to obtain box count <br/>';
		$box_count = Patt_Custom_Func::fetch_box_count(1);
		print_r($box_count);
		echo '<hr/>';

        echo 'Function to obtain request key <br/>';
		$request_key = Patt_Custom_Func::fetch_request_key(1);
		print_r($request_key);
		echo '<hr/>';

        echo 'Function to obtain request ID <br/>';
		$num = Patt_Custom_Func::fetch_request_id(1);
		print_r($num);
		echo '<hr/>';

        echo 'Function to array of Box IDs <br/>';
		$box_array = Patt_Custom_Func::fetch_box_id_a('dddgd,dg4541,4544rhh');
		print_r($box_array);
		echo '<hr/>';

		echo 'Function to insert a new row<br/>';
		global $wpdb;
		$data = [
			box_id  => 'VHG8987YU',
			ticket_id   => '87',
			location    => 'EAST',
			bay => 'Top',
			shelf   => 'Shock',
			user_id => '1',
			index_level => '2',
			program_office_id   => 1,
			record_schedule_id  => '879797978',
			date_created    => date('Y-m-d'),
			date_updated   => date('Y-m-d')
		];
		// $cq_init = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
		// $result = $cq_init->insert($data);
		// print_r($result);
		// echo '<hr/>';

		echo 'Function to update a table<br/>';
		global $wpdb;
		$data = [
			location    => 'UIOOO',
			bay => 'Top',
			shelf   => 'Shock',
			user_id => '1',
			index_level => '20',
			program_office_id   => 1,
			record_schedule_id  => '6876868',
			date_updated   => date('Y-m-d')
		];
		$where = [
			box_id  => 'VHG87YU',	
			ticket_id   => '878'		
		];
		// $cq_init = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
		// $result = $cq_init->update($data, $where);
		// print_r($result);
		// echo '<hr/>';
?>

			<?php

			/* Start the Loop */
			// while ( have_posts() ) :
			// 	the_post();

			// 	get_template_part( 'template-parts/content/content', 'page' );

			// 	// If comments are open or we have at least one comment, load up the comment template.
			// 	if ( comments_open() || get_comments_number() ) {
			// 		comments_template();
			// 	}

			// endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
