<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$general_appearance = get_option('wpsc_appearance_general_settings');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
?>

<div class="bootstrap-iso">
  
  <h3>
    <?php _e('Tickets','supportcandy');?>
  </h3>
  
  <div id="wpsc_tickets_container" class="row" style="border-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;"></div>
  
  <div id="wpsc_alert_success" class="alert alert-success wpsc_alert" style="display:none;" role="alert">
    <i class="fa fa-check-circle"></i> <span class="wpsc_alert_text"></span>
  </div>
  
  <div id="wpsc_alert_error" class="alert alert-danger wpsc_alert" style="display:none;" role="alert">
    <i class="fa fa-exclamation-triangle"></i> <span class="wpsc_alert_text"></span>
  </div>
  
</div>

<!-- Pop-up snippet start -->
<div id="wpsc_popup_background" style="display:none;"></div>
<div id="wpsc_popup_container" style="display:none;">
  <div class="bootstrap-iso">
    <div class="row">
      <div id="wpsc_popup" class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
        <div id="wpsc_popup_title" class="row" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_header_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_header_text_color']?> !important;"><h3><?php _e('Modal Title','supportcandy');?></h3></div>
        <div id="wpsc_popup_body" class="row"><?php _e('I am body!','supportcandy');?></div>
        <div id="wpsc_popup_footer" class="row" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_footer_bg_color']?> !important;">
          <button type="button" class="btn wpsc_popup_close" ><?php _e('Close','supportcandy');?></button>
          <button type="button" class="btn wpsc_popup_action"><?php _e('Save Changes','supportcandy');?></button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Pop-up snippet end -->

<!-- Beginning of new Box List scripts -->

<script>
    
    //Start of new attachment section
	function wpsc_spreadsheet_upload(id,name){
		jQuery('#attachment_upload').unbind('change');
    jQuery('#attachment_upload').on('change', function() {
			
			jQuery.fn.dataTable.ext.errMode = 'none';
			var flag = false;
	    var file = this.files[0];
	    
	    jQuery('#attachment_upload').val('');
	    
	    var file_name_split = file.name.split('.');
	    var file_extension = file_name_split[file_name_split.length-1];
			file_extension = file_extension.toLowerCase();	
			<?php 
				$attachment = get_option('wpsc_allow_attachment_type');
				$attachment_data =  explode(',' , $attachment );
				$attachment_data =  array_map('trim', $attachment_data);
				$attachment_data =  array_map('strtolower', $attachment_data);
			?>
			var allowedExtensionSetting = ["xls", "xlsx"];

			if(!flag && (jQuery.inArray(file_extension,allowedExtensionSetting)  <= -1)) {
				flag = true;
				alert("<?php _e('Attached file type not allowed!','supportcandy')?>");
			}

			var current_filesize=file.size/1000000;
		
			if(current_filesize><?php echo get_option('wpsc_attachment_max_filesize')?>){
				flag = true;
				alert("<?php _e('File size exceed allowed limit!','supportcandy')?>");
			}
			
		if (!flag){
            
            jQuery('.row.wpsp_spreadsheet').each(function(i, obj) {
            obj.remove();
            });
            
			var html_str = '<div class="row wpsp_spreadsheet">'+
				'<div class="progress" style="float: none !important; width: unset !important;">'+
					'<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">'+
							file.name+
							'</div>'+
						'</div>'+
						'<img onclick="attachment_cancel(this);clearBoxTable()" class="attachment_cancel" src="<?php echo WPSC_PLUGIN_URL.'asset/images/close.png'?>" style="display:none;" />'+
					'</div>';

					jQuery('#'+id).append(html_str);

					var attachment = jQuery('#'+id).find('.wpsp_spreadsheet').last();

					var data = new FormData();
						data.append('file', file);
						data.append('arr_name', name);
						data.append('action', 'wpsc_tickets');
            data.append('setting_action', 'upload_file');
            data.append('nonce', jQuery('#wpsc_nonce').val().trim());

						jQuery.ajax({
							type: 'post',
							url: wpsc_admin.ajax_url,
              data: data,
							xhr: function(){
								var xhr = new window.XMLHttpRequest();
								xhr.upload.addEventListener("progress", function(evt){
									if (evt.lengthComputable) {
										var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
										jQuery(attachment).find('.progress-bar').css('width',percentComplete+'%');
									}
								}, false);
								return xhr;
							},
							processData: false,
              contentType: false,
              success: function(response) {
						
								var return_obj=JSON.parse(response);
						    jQuery(attachment).find('.attachment_cancel').show();
										
								if( parseInt(return_obj.id) != 0 ){
              		jQuery(attachment).append('<input type="hidden" name="'+name+'[]" value="'+return_obj.id+'">');
                  jQuery(attachment).find('.progress-bar').addClass('progress-bar-success');
                  
                 //Start of new Datatable code
		    
            var datatable = jQuery('#boxinfodatatable').DataTable( {
        "scrollX": "100%",
        "scrollXInner": "110%"
    } );
            
            datatable.clear().draw();


   var FR = new FileReader();
   FR.onload = function(e) {
      
     
      
     var fileTo = event.target.result;
     
     var fileLines = fileTo.toString();
     
     var data = new Uint8Array(e.target.result);
     var workbook = XLSX.read(data, {type: 'array'});
     var firstSheet = workbook.Sheets[workbook.SheetNames[0]];
     
     var result = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
     
     var arrayOfData = JSON.stringify(result, null, 2);
     
     var parsedData = JSON.parse(arrayOfData);
     
     if (parsedData[2] !== undefined)
     {
    if (parsedData[1][0] !== undefined && parsedData[1][15] !== undefined)
    {
     for (var count = 1; count < fileLines.length; count++)
     {
         
     if (parsedData[count] !== undefined && parsedData[count][0].toString().trim() != "Box")
      {
            datatable.row.add( [
         
            parsedData[count][0],
            parsedData[count][1],
            parsedData[count][2],
            parsedData[count][3],
            parsedData[count][4],
            parsedData[count][5],
            parsedData[count][6],
            parsedData[count][7],
            parsedData[count][8],
            parsedData[count][9],
            parsedData[count][10],
            parsedData[count][11],
            parsedData[count][12],
            parsedData[count][13],
            parsedData[count][14],
            parsedData[count][15]
            
            ]).draw()
            .node();
      }
            
     }
     }
     else
     {
        alert("Spreadsheet is not in the correct format! Please try again.");
        jQuery('.row.wpsp_spreadsheet').each(function(i, obj) {
            obj.remove();
            });
     }
     }
     else
     {
         alert("Spreadsheet does not contain Box Info. Please try again.");
         jQuery('.row.wpsp_spreadsheet').each(function(i, obj) {
            obj.remove();
            });
     }
   };
     FR.readAsArrayBuffer(file);       
            document.getElementById("boxdisplaydiv").style.display = "block";
            
            //End of new Datatable code
                  
                } else {
                    jQuery(attachment).find('.progress-bar').addClass('progress-bar-danger');
                  }
								}
							});
						
						}

    });
		jQuery('#attachment_upload').trigger('click');
	}
    
</script>

<!-- End of new Box List scripts -->

<?php
add_action('admin_footer', 'wpsc_page_inline_script');
global $attrs;
$attrs = isset($attr['page'])? $attr['page']:'init';
?>
<script>
  var wpsc_setting_action = '<?php echo $attrs?>';
</script>

<?php
function wpsc_page_inline_script(){
  ?>
  <script>
  <?php
     $url_attrs = array();
     foreach ($_GET as $key => $value) {
       $url_attrs[] = '"'.$key.'":"'.$value.'"';
     }
     $url_attrs = '{'.implode(',',$url_attrs).'}'
  ?>
       var attrs = <?php echo $url_attrs?>;
       jQuery(document).ready(function(){
         wpsc_init(wpsc_setting_action,attrs);
       });
  </script>
  <?php
}
?>
