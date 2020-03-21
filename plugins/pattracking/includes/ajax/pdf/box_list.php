<?php

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

//Check to see if URL has the correct Request ID
if (isset($_GET['id']))
{
    //Set SuperGlobal ID variable to be used in all functions below
    $GLOBALS['id'] = $_GET['id'];
    
    //Pull in the TCPDF library
    require_once ('tcpdf/tcpdf.php');
    
    //Set styles
      $style_barcode = array('border' => 0,'vpadding' => 'auto','hpadding' => 'auto','fgcolor' => array(0,0,0),'bgcolor' => false,'module_width' => 1,'module_height' => 1);
    
    //Set overall values for PDF
    $obj_pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $obj_pdf->SetCreator(PDF_CREATOR);
    $obj_pdf->SetTitle("Box Labels - Paper Asset Tracking Tool");
    $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN,'',PDF_FONT_SIZE_MAIN));
    $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA,'',PDF_FONT_SIZE_DATA));
    $obj_pdf->SetDefaultMonospacedFont('helvetica');
    $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
    $obj_pdf->setPrintHeader(false);
    $obj_pdf->setPrintFooter(false);
    $obj_pdf->SetAutoPageBreak(true, 10);
    $obj_pdf->SetFont('helvetica', '', 9);

$record_schedules = $wpdb->get_results("SELECT DISTINCT record_schedule_id FROM wpqa_wpsc_epa_boxinfo WHERE ticket_id =" .$GLOBALS['id']);

foreach($record_schedules as $rs_num)
    {

$box_list = $wpdb->get_results("SELECT wpqa_wpsc_epa_folderdocinfo.folderdocinfo_id as id, SUBSTR(wpqa_wpsc_epa_boxinfo.box_id, INSTR(wpqa_wpsc_epa_boxinfo.box_id, '-') + 1) as box, wpqa_wpsc_epa_folderdocinfo.title as title, wpqa_wpsc_epa_folderdocinfo.date as date, wpqa_epa_record_schedule.Record_Schedule_Number as record_schedule, wpqa_wpsc_epa_folderdocinfo.site_name as site, wpqa_wpsc_epa_folderdocinfo.epa_contact_email as contact, wpqa_wpsc_epa_folderdocinfo.source_format as source_format, wpqa_wpsc_epa_program_office.acronym as program_office
FROM wpqa_wpsc_epa_folderdocinfo
INNER JOIN wpqa_wpsc_epa_boxinfo ON wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id
INNER JOIN wpqa_epa_record_schedule ON wpqa_wpsc_epa_folderdocinfo.record_schedule_id = wpqa_epa_record_schedule.id
INNER JOIN wpqa_wpsc_epa_program_office ON wpqa_wpsc_epa_folderdocinfo.program_office_id = wpqa_wpsc_epa_program_office.id
WHERE wpqa_wpsc_epa_folderdocinfo.record_schedule_id = " .$rs_num->record_schedule_id);

$tbl = '
<table style="width: 638px;" cellspacing="0" nobr="true">
  <tr>
    <th style="border: 1px solid #000000; width: 100px; background-color: #f5f5f5; font-weight: bold;">ID</th>
    <th style="border: 1px solid #000000; width: 40px; background-color: #f5f5f5; font-weight: bold;">Box #</th>
    <th style="border: 1px solid #000000; width: 150px; background-color: #f5f5f5; font-weight: bold;">Title</th>
    <th style="border: 1px solid #000000; width: 100px; background-color: #f5f5f5; font-weight: bold;">Date</th>
    <th style="border: 1px solid #000000; width: 80px; background-color: #f5f5f5; font-weight: bold;">Record Schedule</th>
    <th style="border: 1px solid #000000; width: 120px; background-color: #f5f5f5; font-weight: bold;">Contact</th>
    <th style="border: 1px solid #000000; width: 80px; background-color: #f5f5f5; font-weight: bold;">Source Format</th>
    <th style="border: 1px solid #000000; width: 80px; background-color: #f5f5f5; font-weight: bold;">Program Office</th>    
  </tr>
';
        
foreach($box_list as $info){
    $boxlist_id = $info->id;
    $boxlist_box = $info->box;
    $boxlist_title = $info->title;
    $boxlist_date = $info->date;
    $boxlist_rs = $info->record_schedule;
    $boxlist_site = $info->site;
    $boxlist_contact = $info->contact;
    $boxlist_sf = $info->source_format;
    $boxlist_po = $info->program_office;

    $tbl .= '<tr>
            <td style="border: 1px solid #000000; width: 100px;">'.$boxlist_id.'</td>
            <td style="border: 1px solid #000000; width: 40px;">'.$boxlist_box.'</td>
            <td style="border: 1px solid #000000; width: 150px;">'.$boxlist_title.'</td>
            <td style="border: 1px solid #000000; width: 100px;">'.$boxlist_date.'</td>
            <td style="border: 1px solid #000000; width: 80px;">'.$boxlist_rs.'</td>
            <td style="border: 1px solid #000000; width: 120px;">'.$boxlist_contact.'</td>
            <td style="border: 1px solid #000000; width: 80px;">'.$boxlist_sf.'</td>
            <td style="border: 1px solid #000000; width: 80px;">'.$boxlist_po.'</td>
            </tr>';
    
}
$tbl .= '</table>';

$obj_pdf->AddPage();
$obj_pdf->writeHTML($tbl, true, false, false, false, '');

    }
    
    //Generate PDF
    $obj_pdf->Output('file.pdf', 'I');
}

else
{
    //Define message for when no ID exists in URL
    echo "Pass request ID in URL";
}

?>
