<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$wpsc_captcha                   = get_option('wpsc_captcha');
$wpsc_terms_and_conditions      = get_option('wpsc_terms_and_conditions');
$wpsc_set_in_gdpr               = get_option('wpsc_set_in_gdpr');
$wpsc_gdpr_html                 = get_option('wpsc_gdpr_html');
$term_url                       = get_option('wpsc_term_page_url');
$wpsc_terms_and_conditions_html = get_option('wpsc_terms_and_conditions_html');
$wpsc_recaptcha_type            = get_option('wpsc_recaptcha_type');
$wpsc_get_site_key= get_option('wpsc_get_site_key');
$wpsc_allow_rich_text_editor = get_option('wpsc_allow_rich_text_editor');

$fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
		array(
      'key'       => 'agentonly',
      'value'     => '0',
      'compare'   => '='
    )
	),
]);

include WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/class-ticket-form-field-format.php';

$form_field = new WPSC_Ticket_Form_Field();

$general_appearance = get_option('wpsc_appearance_general_settings');

$create_ticket_btn_css = 'background-color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_crt_ticket_btn_action_bar_text_color'].' !important;';
$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$wpsc_appearance_create_ticket = get_option('wpsc_create_ticket');

$description = get_term_by('slug', 'ticket_description', 'wpsc_ticket_custom_fields' );
$wpsc_desc_status = get_term_meta( $description->term_id, 'wpsc_tf_status', true);

?>

<div class="row wpsc_tl_action_bar" style="background-color:<?php echo $general_appearance['wpsc_action_bar_color']?> !important;">
  <div class="col-sm-12">
    <button type="button" id="wpsc_load_new_create_ticket_btn" onclick="wpsc_get_create_ticket();" class="btn btn-sm wpsc_create_ticket_btn" style="<?php echo $create_ticket_btn_css?>"><i class="fa fa-plus"></i> <?php _e('New Ticket','supportcandy')?></button>
    <?php if($current_user->ID):?>
			<button type="button" id="wpsc_load_ticket_list_btn" onclick="wpsc_get_ticket_list();" class="btn btn-sm wpsc_action_btn" style="<?php echo $action_default_btn_css?>"><i class="fa fa-list-ul"></i> <?php _e('Ticket List','supportcandy')?></button>
		<?php endif;?>
  </div>
</div>
<?php
do_action('wpsc_before_create_ticket');
if(apply_filters('wpsc_print_create_ticket_html',true)):
?>


<div id="create_ticket_body" class="row" style="background-color:<?php echo $general_appearance['wpsc_bg_color']?> !important;color:<?php echo $general_appearance['wpsc_text_color']?> !important;">
	<form id="wpsc_frm_create_ticket" onsubmit="return wpsc_submit_ticket();" method="post">
		<div class="row create_ticket_fields_container">
			<?php 
			foreach ($fields as $field) {
				//echo $field->name . " - ";
				if ($field->name == "ticket_category")
				{
				
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
				
				$form_field->print_field($field);
			}
			?>
		</div>
		
		<?php if($wpsc_captcha) {
			if($wpsc_recaptcha_type){?>
				<div class="row create_ticket_fields_container">
					<div class="col-md-6 captcha_container" style="margin-bottom:10px;margin-right:15px; display:flex; background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_captcha_text_color']?> !important;">
						<div style="width:25px;">
							<input type="checkbox" onchange="get_captcha_code(this);" class="wpsc_checkbox" value="1">
							<img id="captcha_wait" style="width:16px;display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" alt="">
						</div>
						<div style="padding-top:3px;"><?php _e("I'm not a robot",'supportcandy')?></div>
					</div>
				</div>
				<?php  
			}
			else {
				?>
				<div class="row create_ticket_fields_container">
					<div class="col-sm-12" style="margin-bottom:10px;margin-right:15px; display:flex">
						<div style="width:25px;">
							<div class="g-recaptcha" data-sitekey=<?php echo $wpsc_get_site_key ?>></div>
						</div>
					</div>
				</div>
				<?php  
			}
		}
		?>
		
		<?php if($wpsc_set_in_gdpr) {?>
			<div class="row create_ticket_fields_container">
				<div class="col-sm-12" style="margin-bottom:10px; display:flex;">
					<div style="width:25px;">
						<input type="checkbox" name="wpsc_gdpr" id="wpsc_gdpr" value="1">
					</div>			   
					<div style="padding-top:3px;">
						<?php echo stripcslashes(html_entity_decode($wpsc_gdpr_html))?>	
					</div>			
				</div>										
			</div>
			<?php  
		   }
			?>
		
		<?php 
		if($wpsc_terms_and_conditions) {?>
			
			<div class="row create_ticket_fields_container">
				<div class="col-sm-12" style="margin-bottom:10px; display:flex;">
					<div style="width:25px;">
						<input type="checkbox" name="terms" id="terms" value="1">
					</div>
					<div style="padding-top:3px;">
						<?php 
						echo stripcslashes(html_entity_decode($wpsc_terms_and_conditions_html));						
						 ?>
					</div>
				</div>						
			</div>
			<?php  
		  }
		?>
		
		<?php
		$wpsc_notify = get_option('wpsc_do_not_notify_setting');
		$wpsc_notify_checkbox = get_option('wpsc_default_do_not_notify_option');
		if($current_user->has_cap('wpsc_agent') && $wpsc_notify) {?>
			
			<div class="row create_ticket_fields_container">
				<div class="col-sm-6" style="margin-bottom:10px; display:flex;">
					<div style="width:25px;">
						<?php $checked = ($wpsc_notify_checkbox == 1) ? 'checked="checked"' : '';?>
						<input <?php echo $checked ?> type="checkbox" name="notify_owner" id="notify_owner" value="1">
					</div>
					<div class="wpsc_notify_owner"style="padding-top:3px;">
						<?php echo __("Don't notify owner",'supportcandy'); ?>
					</div>
				</div>						
			</div>
			<?php  
		  }
		?>
		
		<div class="row create_ticket_frm_submit">
			<button type="submit" id="wpsc_create_ticket_submit" class="btn" style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_submit_button_border_color']?> !important;"><?php _e('Submit Ticket','supportcandy')?></button>
			<button type="button" id="wpsc_create_ticket_reset" onclick="wpsc_get_create_ticket();" class="btn" style="background-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_text_color']?> !important;border-color:<?php echo $wpsc_appearance_create_ticket['wpsc_reset_button_border_color']?> !important;"><?php _e('Reset Form','supportcandy')?></button>
		  <?php do_action('wpsc_after_create_ticket_frm_btn');?>
		</div>
		
		<input type="file" id="attachment_upload" class="hidden" onchange="">
		<input type="hidden" id="wpsc_nonce" value="<?php echo wp_create_nonce()?>">
		
		<input type="hidden" name="action" value="wpsc_tickets">
		<input type="hidden" name="setting_action" value="submit_ticket">
		<input type="hidden" id="captcha_code" name="captcha_code" value="">			
		
	</form>
</div>
<!-- New imports below -->
<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.14.5/xlsx.full.min.js"></script>
<!-- End of new imports -->
<script type="text/javascript">
	jQuery(document).ready(function(){
		
		if(jQuery('.wpsc_drop_down,.wpsc_checkbox,.wpsc_radio_btn,.wpsc_category,.wpsc_priority').val != ''){
			wpsc_reset_visibility();
		}

		jQuery('.wpsc_drop_down,.wpsc_checkbox,.wpsc_radio_btn,.wpsc_category,.wpsc_priority').change(function(){
			wpsc_reset_visibility();
		});
    });
		
		/*jQuery( "#customer_name" ).autocomplete({
      minLength: 1,
      appendTo: jQuery("#wpsc_agent_name").parent(),
      source: function( request, response ) {
        var term = request.term;
        request = {
          action: 'wpsc_tickets',
          setting_action : 'get_users',
          term : term
        }
        jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
          response(data);
        });	
      },
			select: function (event, ui) {
        jQuery('#customer_name').val(ui.item.value);
				jQuery('#customer_email').val(ui.item.email);
      }
    });*/		
		jQuery('.wpsc_datetime').datetimepicker({
			 dateFormat : '<?php echo get_option('wpsc_calender_date_format')?>',
				showAnim : 'slideDown',
				changeMonth: true,
				changeYear: true,
			 timeFormat: 'HH:mm:ss'
		 });
	
	function get_captcha_code(e){
		jQuery(e).hide();
		jQuery('#captcha_wait').show();
		var data = {
	    action: 'wpsc_tickets',
	    setting_action : 'get_captcha_code'
	  };
		jQuery.post(wpsc_admin.ajax_url, data, function(response) {
			jQuery('#captcha_code').val(response.trim());;
			jQuery('#captcha_wait').hide();
			jQuery(e).show();
			jQuery(e).prop('disabled',true);
	  });
	}
	
	function wpsc_reset_visibility(){
		
		jQuery('.wpsc_form_field').each(function(){
			var visible_flag = false;
			var visibility = jQuery(this).data('visibility').trim();
			if(visibility){
				visibility = visibility.split(';;');
				jQuery(visibility).each(function(key, val){
					var condition = val.split('--');
					var cond_obj = jQuery('.field_'+condition[0]);
					var field_type = jQuery(cond_obj).data('fieldtype');
					switch (field_type) {
						
						case 'dropdown':
							if ( jQuery(cond_obj).hasClass('visible') && jQuery(cond_obj).find('select').val()==condition[1] ) visible_flag=true;
							break;
							
						case 'checkbox':
							var check = false;
							jQuery(cond_obj).find('input:checked').each(function(){
								if(jQuery(this).val()==condition[1]) check=true;
							});
							if ( jQuery(cond_obj).hasClass('visible') && check ) visible_flag=true;
							break;
							
						case 'radio':
							if ( jQuery(cond_obj).hasClass('visible') && jQuery(cond_obj).find('input:checked').val()==condition[1] ) visible_flag=true;
							break;
							
					}
				});
				if (visible_flag) {
					jQuery(this).removeClass('hidden');
					jQuery(this).addClass('visible');
				} else {
					jQuery(this).removeClass('visible');
					jQuery(this).addClass('hidden');
					var field_type = jQuery(this).data('fieldtype');
					switch (field_type) {
						
						case 'text':
						case 'email':
						case 'number':
						case 'date':
						case 'datetime':
						case 'url':
						case 'time':
							jQuery(this).find('input').val('');
							break;
							
						case 'textarea':
							jQuery(this).find('textarea').val('');
							break;
						
						case 'dropdown':
							jQuery(this).find('select').val('');
							break;
							
						case 'checkbox':
							jQuery(this).find('input:checked').each(function(){
								jQuery(this).prop('checked',false);
							});
							break;
							
						case 'radio':
							jQuery(this).find('input:checked').prop('checked',false);
							break;
							
					}
				}
			}
		});
		
	}
	
	function clearBoxTable()
	{
	    var datatable = jQuery('#boxinfodatatable').DataTable();
	    datatable.clear().draw();
	}
	
	function wpsc_submit_ticket(){
		
		var validation = true;
		
		/*
			Required fields
		*/
		jQuery('.visible.wpsc_required').each(function(e){
			var field_type = jQuery(this).data('fieldtype');
			switch (field_type) {
			    case 'hidden':
				case 'text':
				case 'email':
				case 'number':
				case 'date':
				case 'url':
				case 'time':
					if(jQuery(this).find('input').val()=='') validation=false;
					break;
		
				case 'textarea':
					if(jQuery(this).find('textarea').val()=='') validation=false;
					break;
		
				case 'dropdown':
					if(jQuery(this).find('select').val()=='') validation=false;
					break;
		
				case 'checkbox':
				case 'radio':
					if(jQuery(this).find('input:checked').length==0) validation=false;
					break;
					
				case 'file_attachment':
					if(jQuery(this).find('.attachment_container').is(':empty')){
						validation=false;
					}
					break;
					
				case 'tinymce':
				 	<?php 
					
					$rich_editing = $wpscfunction->rich_editing_status($current_user);
				 
					 $flag = false;
				 	if($wpsc_desc_status && is_user_logged_in() && (in_array('register_user',$wpsc_allow_rich_text_editor) && !$current_user->has_cap('wpsc_agent') ) && $rich_editing){
						$flag = true;
					} elseif ($wpsc_desc_status && $current_user->has_cap('wpsc_agent') && is_user_logged_in() && $rich_editing){
						$flag = true;
					}elseif (!is_user_logged_in() && $wpsc_desc_status && in_array('guest_user',$wpsc_allow_rich_text_editor)){
						$flag = true;
					}
				 
					 if($flag){
				 		?>
						var description = tinyMCE.activeEditor.getContent();
						if(description.trim().length==0) validation=false;
						break;
					<?php 
					}else {?>
						if(jQuery('#ticket_description').val()=='') validation=false;
						break;
						<?php 
					}?>
			}
			
			if (!validation) return;
		});
		
		    //New DataTable validation check
			if ( !jQuery('#boxinfodatatable').DataTable().data().any() ) {
			 
			 validation=false;
			    
			}
		
		if (!validation) {
			alert("<?php _e('Required fields can not be empty!','supportcandy')?>");
			return false;
		}
		
		/*
			Emails
		*/
		jQuery('.wpsc_email').each(function(e){
			var email = jQuery(this).val().trim();
			if(email.length>0 && !validateEmail(email)) {
				validation=false;
				jQuery(this).focus();
			}
			if (!validation) return;
		});
		if (!validation) {
			alert("<?php _e('Incorrect email address!','supportcandy')?>");
			return false;
		}
		
		/*
			URLs
		*/
		jQuery('.wpsc_url').each(function(e){
			var url = jQuery(this).val().trim();
			if(url.length>0 && !validateURL(url)) {
				validation=false;
				jQuery(this).focus();
			}
			if (!validation) return;
		});
		if (!validation) {
			alert("<?php _e('Incorrect URL!','supportcandy')?>");
			return false;
		}
			
		<?php	do_action('wpsc_create_ticket_validation');	?>
		
		/*
			Captcha
		*/
		<?php
		if( $wpsc_captcha ) { 
			if( $wpsc_recaptcha_type ){?>
				if (jQuery('#captcha_code').val().trim().length==0) {
					alert("<?php _e('Please confirm you are not a robot!','supportcandy')?>");
					validation=false;
					return false;
				}
				<?php
			}
			else {?>
				var recaptcha = jQuery("#g-recaptcha-response").val();
				if (recaptcha === "") {
					alert("<?php _e('Please confirm you are not a robot!','supportcandy')?>");
					validation=false;
					return false;
				}<?php
			}
		}
		?>
		
		<?php
		if($wpsc_set_in_gdpr) { ?>
				if (!jQuery('#wpsc_gdpr').is(':checked')){
	 	     alert("<?php _e('Ticket can not be created unless you agree to privacy policy.','supportcandy')?>");
	 	     return false;
	 	   }
		<?php
		}
		?>
			
		<?php
		if($wpsc_terms_and_conditions) { ?>
				if (!jQuery('#terms').is(':checked')){
	 	     alert("<?php _e('Ticket can not be created unless you agree to terms & coditions.', 'supportcandy')?>");
	 	     return false;
	 	   }
		<?php
		}
		?>
		
		if (validation) {
		    
		    //New get DataTable data in the form of an
		    var data = jQuery('#boxinfodatatable').DataTable().rows().data().toArray();

            //alert( 'The table contents are ' + data );
		    
			var dataform = new FormData(jQuery('#wpsc_frm_create_ticket')[0]);
			
			dataform.append('boxinfo', JSON.stringify(data));
			
			//alert( 'The table contents are ' + JSON.stringify(data) );
			
			var is_tinymce = true;
			<?php

			$rich_editing = $wpscfunction->rich_editing_status($current_user);
			$flag = false;
			
			if( is_user_logged_in() && (in_array('register_user',$wpsc_allow_rich_text_editor) && !$current_user->has_cap('wpsc_agent') ) && $rich_editing){
				$flag = true;
			} elseif (  $current_user->has_cap('wpsc_agent') && is_user_logged_in() && $rich_editing){
				$flag = true;
			}elseif (!is_user_logged_in() && in_array('guest_user',$wpsc_allow_rich_text_editor)){
				$flag = true;
			}
			if($wpsc_desc_status){
				if($flag){
				?>
					//var description = tinyMCE.get('ticket_description').getContent().trim();
					//dataform.append('ticket_description', description);
					//is_tinymce = true;
				<?php
				}else{
					?>
					//var description = jQuery('#ticket_description').val();
					//dataform.append('ticket_description',description);
					//is_tinymce = false;
					<?php
				}
			}
			?>
			jQuery('#create_ticket_body').html(wpsc_admin.loading_html);
			//wpsc_doScrolling('.wpsc_tl_action_bar',1000);
		  jQuery.ajax({
		    url: wpsc_admin.ajax_url,
		    type: 'POST',
		    data: dataform,
		    processData: false,
		    contentType: false
		  })
		  .done(function (response_str) {
		    var response = JSON.parse(response_str);
				if(response.redirct_url==''){
					jQuery('#create_ticket_body').html(response.thank_you_page);
				} else {
					window.location.href = response.redirct_url;
				}
		  });
			<?php  if($wpsc_desc_status){ ?>
				// if(is_tinymce) tinyMCE.activeEditor.setContent('');
			<?php } ?>
			return false;
		}
		
	}
	<?php do_action('wpsc_print_ext_js_create_ticket');	?>
	
</script>
 <?php if (!$wpsc_recaptcha_type && $wpsc_captcha): ?>
	 <script src='https://www.google.com/recaptcha/api.js'></script>
 <?php endif; ?>
<?php
endif;
?>
