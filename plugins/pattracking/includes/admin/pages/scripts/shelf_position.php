<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

echo '<link rel="stylesheet" type="text/css" href="' . WPPATT_PLUGIN_URL . 'includes/admin/css/jquery.seat-charts.css"/>';

?>
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo WPPATT_PLUGIN_URL.'includes/admin/js/jquery.seat-charts.js';?>"></script>


<style>
body {
	font-family: 'Lato', sans-serif;
}
a {
	color: #b71a4c;
}
.front-indicator {
	width: 95px;
	margin: 5px 32px 15px 32px;
	background-color: #f6f6f6;	
	color: #adadad;
	text-align: center;
	padding: 3px;
	border-radius: 5px;
}
.wrapper {
	width: 100%;
	text-align: center;
}
.container {
	margin: 0 auto;
	width: 500px;
	text-align: left;
}
.booking-details {
	float: left;
	text-align: left;
	margin-left: 35px;
	font-size: 12px;
	position: relative;
	height: 401px;
}
.booking-details h2 {
	margin: 25px 0 20px 0;
	font-size: 17px;
}
.booking-details h3 {
	margin: 5px 5px 0 0;
	font-size: 14px;
}
div.seatCharts-cell {
	color: #182C4E;
	height: 25px;
	width: 25px;
	line-height: 25px;
	
}
div.seatCharts-seat {
	color: #FFFFFF;
	cursor: pointer;	
}
div.seatCharts-row {
	height: 35px;
}
div.seatCharts-seat.available {
	background-color: #B9DEA0;

}
div.seatCharts-seat.available.first-class {
/* 	background: url(vip.png); */
	background-color: #3a78c3;
}
div.seatCharts-seat.focused {
	background-color: #76B474;
}
div.seatCharts-seat.selected {
	background-color: #E6CAC4;
}
div.seatCharts-seat.unavailable {
	background-color: #472B34;
}
div.seatCharts-container {
	border-right: 1px dotted #adadad;
	width: 200px;
	padding: 20px;
	float: left;
}
div.seatCharts-legend {
	padding-left: 0px;
	position: absolute;
	bottom: 16px;
}
ul.seatCharts-legendList {
	padding-left: 0px;
}
span.seatCharts-legendDescription {
	margin-left: 5px;
	line-height: 30px;
}
.checkout-button {
	display: block;
	margin: 10px 0;
	font-size: 14px;
}
#selected-seats {
	max-height: 90px;
	overflow-y: scroll;
	overflow-x: none;
	width: 170px;
}
</style>

<div class="wrapper">
			<div class="container">
				<div id="seat-map">
					<div class="front-indicator">Top of Bay</div>
					
				</div>
				<div class="booking-details">
					<h2>Details</h2>
					<strong>Box # <?php echo $_GET['box_id']; ?> Assignment</strong>
					<h3> Selected Box Position:</h3>
					<ul id="selected-seats"></ul>
					
					<button class="checkout-button">Submit &raquo;</button>
					
					<div id="legend"></div>
				</div>
			</div>
		</div>
		
		
		<script>
			var firstSeatLabel = 1;
	jQuery(document).ready(function() {
				var $cart = jQuery('#selected-seats'),
					$counter = jQuery('#counter'),
					$total = jQuery('#total'),
					sc = jQuery('#seat-map').seatCharts({
					map: [
						'eeee',
						'eeee',
						'eeee',
						'eeee',
						'eeee',
					],
					seats: {
						f: {
							price   : 100,
							classes : 'first-class', //your custom CSS class
							category: 'First Class'
						},
						e: {
							price   : 40,
							classes : 'economy-class', //your custom CSS class
							category: 'Economy Class'
						}					
					
					},
					naming : {
						top : false,
						getLabel : function (character, row, column) {
							return firstSeatLabel++;
						},
					},
					legend : {
						node : jQuery('#legend'),
					    items : [
							[ 'e', 'available',   'Available'],
							[ 'f', 'unavailable', 'Already Assigned']
					    ]					
					},
					click: function () {
						if (this.status() == 'available' && sc.find('selected').length < 1) {
							//let's create a new <li> which we'll add to the cart items
							jQuery('<li>Position # '+this.settings.label+': <a href="#" class="cancel-cart-item">[cancel]</a></li>')
								.attr('id', 'cart-item-'+this.settings.id)
								.data('seatId', this.settings.id)
								.appendTo($cart);
							
							/*
							 * Lets update the counter and total
							 *
							 * .find function will not find the current seat, because it will change its stauts only after return
							 * 'selected'. This is why we have to add 1 to the length and the current seat price to the total.
							 */
							$counter.text(sc.find('selected').length+1);
							
							return 'selected';
						} else if (this.status() == 'selected') {
							//update the counter
							$counter.text(sc.find('selected').length-1);
							//and total
						
							//remove the item from our cart
							jQuery('#cart-item-'+this.settings.id).remove();
						
							//seat has been vacated
							return 'available';
						} else if (this.status() == 'unavailable') {
							//seat has been already booked
							return 'unavailable';
						} else {
							return this.style();
						}
					}
				});

				//this will handle "[cancel]" link clicks
				jQuery('#selected-seats').on('click', '.cancel-cart-item', function () {
					//let's just trigger Click event on the appropriate position, so we don't have to repeat the logic here
					sc.get(jQuery(this).parents('li:first').data('seatId')).click();
				});
<?php
//$digitization_center = 'East';

$shelf_position = $wpdb->get_results(
"SELECT shelf, position
FROM wpqa_wpsc_epa_storage_location
WHERE digitization_center = '" . $_GET['center'] . "' AND aisle = '".$_GET['aisle']."' AND bay = '".$_GET['bay'] ."'"
			);

	$taken_position_array = array();
	
foreach ($shelf_position as $info) {
	$shelf_id = $info->shelf;
	$position_id = $info->position;
	
	$option = $shelf_id . '_' .$position_id;

	array_push($taken_position_array, "'$option'");
}

$string = rtrim(implode(',', $taken_position_array), ',');

if (!empty($taken_position_array)) {
echo 'sc.get([' . $string . ']).status("unavailable")';
}
?>
		});	
		
		</script>
				
