<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Patt_HooksFilters')) {

    class Patt_HooksFilters
    {

        /**
         * Get things started
         *
         * @access  public
         * @since   1.0
         */
        public function __construct()
        {
            // Print HTML In Request Form - Location: /home/acy3/public_html/wordpress3/wp-content/plugins/supportcandy/includes/admin/tickets/create_ticket/load_create_ticket.php
            add_action('print_listing_form_block', [$this, 'print_listing_form_block']);
            add_action('patt_custom_imports_tickets', [$this, 'patt_custom_imports_tickets']);
            add_action('patt_print_js_functions_create', [$this, 'patt_print_js_functions_create']);
            
            // Print Scripts - Location: /home/acy3/public_html/wordpress3/wp-content/plugins/supportcandy/includes/admin/tickets/tickets.php
            add_action('patt_print_js_tickets_page', [$this, 'patt_print_js_tickets_page']);

            // Location: /home/acy3/public_html/wordpress3/wp-content/plugins/supportcandy/includes/functions/create_ticket.php
            add_action('patt_process_boxinfo_records', [$this, 'patt_process_boxinfo_records']);
        }

        public function patt_process_boxinfo_records($data){
            global $wpdb, $wpscfunction;
            
            $ticket_id = $data['ticket_id'];
            $str_length = 7;
            $request_id = substr("000000{$ticket_id}", -$str_length);

            $data_update = array('request_id' => $request_id);
            $data_where = array('id' => $ticket_id);
            $wpdb->update($wpdb->prefix . 'wpsc_ticket', $data_update, $data_where);

            // END

            //New BoxInfo Code

            $boxinfodata = $data["box_info"];
            $boxinfodata = str_replace('\\', '', $boxinfodata);
            $boxinfo_array = json_decode($boxinfodata, true);

            $box = '';

            foreach ($boxinfo_array as $boxinfo) {
                $box_id = $request_id . '-' . $boxinfo["Box"];
                if ($box !== $boxinfo["Box"]) {
                    $boxarray = array(
                        'box_id' => $box_id,
                        'ticket_id' => $ticket_id,
                        'location' => 'East',
                        'bay' => '1',
                        'shelf' => 'Top',
                        'user_id' => 1,
                        'index_level' => 1,
                        'date_created' => date("Y-m-d H:i:s"),
                        'date_updated' => date("Y-m-d H:i:s"),

                    );

                    $boxinfo_id = $wpscfunction->create_new_boxinfo($boxarray);

                    $wpscfunction->add_boxinfo_meta($boxinfo_id, 'assigned_agent', '0');

                    $wpscfunction->add_boxinfo_meta($boxinfo_id, 'prev_assigned_agent', '0');

                    $box = $boxinfo["Box"];
                }

            }

            //End of New BoxInfo Code

        }

        public function patt_print_js_tickets_page(){
            ?>
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
                
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, {type: 'array'});
                var firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                
                var result = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                var arrayOfData = JSON.stringify(result);
                var parsedData = JSON.parse(arrayOfData);
                var arrayLength = Object.keys(parsedData).length;
                
                if (parsedData[2] !== undefined)
                {
                if (parsedData[1][0] !== undefined && parsedData[1][15] !== undefined)
                {
                for (var count = 1; count < arrayLength; count++)
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
        }
        public function patt_print_js_functions_create(){ ?>
            function clearBoxTable() {
                var datatable = jQuery('#boxinfodatatable').DataTable();
                datatable.clear().draw();
            }

            jQuery.fn.toJson = function () {
                try {
                    if (!this.is('table')) {
                        return;
                    }

                    var results = [],
                        headings = [];

                    var table = jQuery('#boxinfodatatable').DataTable();

                    this.find('thead tr th').each(function (index, value) {
                        headings.push(jQuery(value).text());
                    });

                    table.rows().every(function (rowIdx, tableLoop, rowLoop) {
                        var row = {};
                        var data = this.data();
                        headings.forEach(function (key, index) {
                            var value = data[index];
                            row[key] = value;
                        });
                        results.push(row);
                    });

                    return results;
                } catch (ex) {
                    alert(ex);
                }
            }
        <?php
        }

        public function patt_custom_imports_tickets($file_path){ ?>
            <!-- New imports below -->
            <link rel="stylesheet" type="text/css" href="<?php echo $file_path.'asset/lib/DataTables/datatables.min.css';?>"/>
            <script type="text/javascript" src="<?php echo $file_path.'asset/lib/DataTables/datatables.min.js';?>"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.5/xlsx.full.min.js"></script>
            <!-- End of new imports -->
            <?php
        }

        public function print_listing_form_block($field) {
            if ($field->name == "ticket_category") {
                ?>
				<!-- Beginning of new datatable -->
                <div class="box-body table-responsive" id="boxdisplaydiv" style="width:100%;padding-bottom: 40px;padding-right:20px;padding-left:20px;margin: 0 auto;">
                <label class="wpsc_ct_field_label">Box List <span style="color:red;">*</span></label>
                <table id="boxinfodatatable" class="table table-striped table-bordered nowrap">
                <thead style="margin: 0 auto !important;">
                    <tr>
                        <th>Box</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Author/Addressee</th>
                        <th>Record Type</th>
                        <th>Record Schedule & Item Number</th>
                        <th>Site Name</th>
                        <th>Site ID #</th>
                        <th>Close Date</th>
                        <th>EPA Contact</th>
                        <th>Access Type</th>
                        <th>Source Format</th>
                        <th>Rights</th>
                        <th>Contract #</th>
                        <th>Grant #</th>
                        <th>Program Office</th>
                    </tr>
                </thead>
                </table>

                <div class="row attachment_link">
				<span onclick="wpsc_spreadsheet_upload('attach_16','spreadsheet_attachment');">Attach spreadsheet</span>
				</div>
				<div id="attach_16" class="row spreadsheet_container"></div>
                </div>

            <!-- End of new datatable -->

				<?
            }
        }
    }
    new Patt_HooksFilters;
}