<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;
			
/*
Aisle (1-50) - 50 Aisle's to a Facility (5000 Boxes to a Facility)
Bay (1-5) - 5 bays to a Aisle (100 Boxes to a Aisle)
Shelf (1-5) - 5 shelfs to a Bay (20 Boxes per Bay)
Bin (1-4) has 4 positions (4 boxes to a Shelf)


GOAL - Group boxes releated to a request together as much as possible.
Within request, group boxes by accession.

Upon ingestion, the box list needs to be sorted by record schedule?

Total # of boxes for the request: 50 (40 for accession 1) (10 for accession 2)

Loop through Aisle:
how many boxes remain in Aisle?
For each unique Asile count:
2 unqiue Asile's. First Aisle contains 6 Boxes-100 = 94. Second Aisle contains 3 Boxes-100 = 97.

Is the Total # of boxes going to fit in the Aisle? 

Aisle 1 - 94 is not less than 50. Yes
Aisle 1 has Space
Is this the lowest Aisle number that has space? Yes
IF YES then make recommendation

Aisle 2 - 97 is not less than 50. Yes
Aisle 2 has Space
Is this the lowest Aisle number that has space? No
IF YES then make recommendation

What's the lowest Aisle number that has space? #1 THIS IS THE SUGGESTION

Is the Total # of boxes going to fit in the Bay? 

Loop through Bays in Aisle 1.
Total number of bays is up to "e"

Bay a has 6 boxes.
20-6 = 14 boxes remaining

Find the next Bay. Does the next Bay have enough space for the remaining boxes? 50-14 = 44

Recomendation
Aisle 1 Bay a (14)
Aisle 1 Bay b (20)
Aisle 1 Bay c (10)

Automatically apply recommendation sequentially?




*/

$number_of_boxes_in_request = 10;

$last_aisle_number_in_facility = 5;

echo '<link rel="stylesheet" type="text/css" href="' . WPPATT_PLUGIN_URL . 'includes/admin/css/jquery.seat-charts.css"/>';

?>

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
				    <strong>Aisle</strong>
<select id="aisle_selector" name="aisle_selector" class="form-control">    
<option value="0">--Select Aisle--</option>
<?php
$digitization_center = 'East';
$digitization_center_aisle_total = 50;

$aisle_array = range(1, $digitization_center_aisle_total);

foreach ($aisle_array as $value) {
    $get_available_aisle = $wpdb->get_row(
				"SELECT count(id) as count
FROM wpqa_wpsc_epa_storage_location
WHERE aisle = '" . $value . "' AND digitization_center = '" . $digitization_center . "'"
			);
			
echo '<option value="'.$value.'">Aisle #' . $value . ' [' . (100 - $get_available_aisle->count) . ' boxes remain]'.'</option>';
}

?>
</select>
<br /><br />
<div id="bay_div">
<strong>Bay</strong>
<select name="bay_selector" class="form-control" id="bay_selector"></select>
<br /><br />
</div>
					<div class="front-indicator">Top of Bay</div>
				</div>
				<div class="booking-details">
					<h2>Details</h2>
					
					<h3> Selected Positions (<span id="counter">0</span>):</h3>
					<ul id="selected-seats"></ul>
					
					<button class="checkout-button">Submit &raquo;</button>
					
					<div id="legend"></div>
				</div>
			</div>
		</div>
		
		<script>
			var firstSeatLabel = 1;
		
			jQuery(document).ready(function() {
			jQuery("#bay_div").hide();
		  // event called when the aisle select is changed
        jQuery("#aisle_selector").change(function(){
            // get the currently selected aisle selector ID
            var aisleId = jQuery(this).val();
            
            jQuery.ajax({
                // make the ajax call to server and pass the aisle ID as a GET variable
                url: "<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/bay_query.php?aisle_id=" + aisleId,
            }).done(function(data) {
                // our ajax call is finished, we have the data returned from the server in a var called data
                data = JSON.parse(data);
            if (aisleId == '0') {
            jQuery("#bay_div").hide();
            } else {
            jQuery("#bay_div").show();
            }
                    jQuery("#bay_selector").empty();
                // loop through our returned data and add an option to the select for each province returned
                jQuery.each(data, function(i, item) {
                    jQuery('#bay_selector').append(jQuery('<option>', {value:i, text:item}));
                });

            });
        });
        
        
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
						if (this.status() == 'available') {
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
							$total.text(recalculateTotal(sc)+this.data().price);
							
							return 'selected';
						} else if (this.status() == 'selected') {
							//update the counter
							$counter.text(sc.find('selected').length-1);
							//and total
							$total.text(recalculateTotal(sc)-this.data().price);
						
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
					//let's just trigger Click event on the appropriate seat, so we don't have to repeat the logic here
					sc.get(jQuery(this).parents('li:first').data('seatId')).click();
				});

				//let's pretend some seats have already been booked
				sc.get(['1_1', '1_2', '1_3']).status('unavailable');
		
		});

		function recalculateTotal(sc) {
			var total = 0;
		
			//basically find every selected seat and sum its price
			sc.find('selected').each(function () {
				total += this.data().price;
			});
			
			return total;
		}
		
		</script>
