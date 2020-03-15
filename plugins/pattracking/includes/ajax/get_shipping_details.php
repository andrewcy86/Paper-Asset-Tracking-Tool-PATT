<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$ticket_id 	 = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0 ;

ob_start();
?>

  <style>
  .hide
  {
     display:none;
  }
  
  .jsgrid-header-row>.jsgrid-header-cell {
    text-align: center;
  }
  
  .jsgrid-row>.jsgrid-cell {
    text-align: center;
  }
  
  .jsgrid-alt-row>.jsgrid-cell {
    text-align: center;
  }
  </style>
    </head>  
    <body>  
        <div class="container">  
   <br />
   <div class="table-responsive">
    <div id="grid_table"></div>
   </div>  
  </div>
<script>
(function ($) {
           
    $('#grid_table').jsGrid({

     width: "90%",
     height: "300px",

     filtering: true,
     inserting:true,
     editing: true,
     sorting: true,
     paging: true,
     autoload: true,
     pageSize: 10,
     pageButtonCount: 5,
     deleteConfirm: "Do you really want to delete this tracking number?",

     controller: {
      loadData: function(filter){
       var ticket_id = <?php echo $ticket_id; ?>; 
       return $.ajax({
        type: "GET",
        url: "/wordpress2/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php?ticket_id="+ticket_id,
        data: filter
       });
      },
      insertItem: function(item){
       var ticket_id = <?php echo $ticket_id; ?>; 
       return $.ajax({
        type: "POST",
        url: "/wordpress2/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php?ticket_id="+ticket_id,
        data:item
       });
      },
      updateItem: function(item){
       var ticket_id = <?php echo $ticket_id; ?>; 
       return $.ajax({
        type: "PUT",
        url: "/wordpress2/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php?ticket_id="+ticket_id,
        data: item
       });
      },
      deleteItem: function(item){
       return $.ajax({
        type: "DELETE",
        url: "/wordpress2/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php",
        data: item
       });
      },
     },

     fields: [
      {
       name: "id",
    type: "hidden",
    css: 'hide'
      },
            {
       name: "ticket-id",
    type: "hidden",
    css: 'hide'
      },
      {
       name: "tracking_number",
       title: "Tracking Number",
    type: "text", 
    width: 150, 
    validate: "required",
    formatter: function (cellvalue, options, rowObject) {
                    return "<a href='javascript:void(0);' class='anchor usergroup_name link'>" +
                           cellvalue + '</a>';
                }
      },
      {
       name: "company_name",
       title: "Shipping Company",
    type: "select", 
    items: [
     { Name: "", Id: '' },
     { Name: "UPS", Id: 'ups' },
     { Name: "FedEx", Id: 'fedex' },
     { Name: "USPS", Id: 'usps' },
     { Name: "DHL", Id: 'dhl' },
    ], 
    valueField: "Id", 
    textField: "Name", 
    validate: "required"
      },
      {
       name: "status",
       title: "Shipping Status",
    type: "text", 
    width: 150, 
    editing: false,
    inserting: false
      },
      {
       type: "control"
      }
     ]

    });
                })(jQuery);
</script>

<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_open_ticket(<?php echo htmlentities($ticket_id)?>);wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>

<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);