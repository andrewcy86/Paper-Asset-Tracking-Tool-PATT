<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Patt_Custom_Func')) {

    class Patt_Custom_Func
    {

        public $table_prefix;
        /**
         * Get things started
         *
         * @access  public
         * @since   1.0
         */
        public function __construct()
        {            
            global $wpdb; 
            $this->table_prefix = $wpdb->prefix;
        }

public static function calc_max_gap_val($dc_final){

global $wpdb; 
$find_sequence = $wpdb->get_row("
WITH 
cte1 AS
(
SELECT id, 
       CASE WHEN     occupied  = LAG(occupied) OVER (ORDER BY id)
                 AND remaining = LAG(remaining) OVER (ORDER BY id)
            THEN 0
            ELSE 1 
            END values_differs
FROM wpqa_wpsc_epa_storage_status
WHERE digitization_center = '" . $dc_final . "'
),
cte2 AS 
(
SELECT id,
       SUM(values_differs) OVER (ORDER BY id) group_num
FROM cte1
ORDER BY id
)
SELECT MIN(id) as id
FROM cte2
GROUP BY group_num
ORDER BY COUNT(*) DESC LIMIT 1;
");

$sequence_shelfid = $find_sequence->id;

$seq_shelfid_final = $sequence_shelfid-1;
            
// Determine largest Gap of consecutive shelf space
$find_gaps = $wpdb->get_row("
WITH 
cte1 AS
(
SELECT shelf_id, remaining, SUM(remaining = 0) OVER (ORDER BY id) group_num
FROM wpqa_wpsc_epa_storage_status
WHERE digitization_center = '" . $dc_final . "' AND
id BETWEEN 1 AND '" . $seq_shelfid_final . "'
)
SELECT GROUP_CONCAT(shelf_id) as shelf_id,
       GROUP_CONCAT(remaining) as remaining,
       SUM(remaining) as total
FROM cte1
WHERE remaining != 0
GROUP BY group_num
ORDER BY total DESC
LIMIT 1
");

$max_gap_value = $find_gaps->total;

return $max_gap_value;
    }


public static function get_unassigned_boxes($tkid){

global $wpdb; 

$obtain_box_ids_details = $wpdb->get_results("
SELECT wpqa_wpsc_epa_boxinfo.storage_location_id
FROM wpqa_wpsc_epa_boxinfo 
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id 
WHERE
wpqa_wpsc_epa_storage_location.aisle = 0 AND 
wpqa_wpsc_epa_storage_location.bay = 0 AND 
wpqa_wpsc_epa_storage_location.shelf = 0 AND 
wpqa_wpsc_epa_storage_location.position = 0 AND
wpqa_wpsc_epa_boxinfo.ticket_id = '" . $tkid . "'
");

$box_id_array = array();
foreach ($obtain_box_ids_details as $box_id_val) {
$box_id_array_val = $box_id_val->storage_location_id;
array_push($box_id_array, $box_id_array_val);
}
return $box_id_array;

    }
        
public static function get_default_digitization_center($id)
        {
            global $wpdb;

// Get Distinct program office ID
$get_program_office_id = $wpdb->get_results("
SELECT wpqa_wpsc_epa_program_office.organization_acronym as acronym
FROM wpqa_wpsc_epa_boxinfo 
LEFT JOIN wpqa_wpsc_epa_program_office ON wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.office_code 
WHERE wpqa_wpsc_epa_boxinfo.ticket_id = '" . $id . "'
");

$program_office_east_array = array();
$program_office_west_array = array();

foreach ($get_program_office_id as $program_office_id_val) {
$program_office_val = $program_office_id_val->acronym;

$east_region = array("R01", "R02", "R03", "AO", "OITA", "OCFO", "OCSPP", "ORD", "OAR", "OW", "OIG", "OGC", "OMS", "OLEM", "OECA");
$west_region = array("R04", "R05", "R06", "R07", "R08", "R09", "R10");

if (in_array($program_office_val, $east_region))
  {
  array_push($program_office_east_array, $program_office_val);
  }

if (in_array($program_office_val, $west_region))
  {
  array_push($program_office_west_array, $program_office_val);
  }
}

$east_count = count($program_office_east_array);
$west_count = count($program_office_west_array);

$set_center = '';

if ($east_count > $west_count)
{
$set_center = 62;
}

if ($west_count > $east_count)
{
$set_center = 2;
}

if ($west_count == $east_count)
{
$set_center = 666;
}

return $set_center;
        }
        public static function fetch_request_id($id)
        {
            global $wpdb; 
            $args = [
                'where' => ['id', $id],
            ];
            $wpqa_wpsc_ticket = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_ticket");
            $box_details = $wpqa_wpsc_ticket->get_row($args, false);
            $asset_id = $box_details->request_id;
            return $asset_id;
        }

        //Function to obtain serial number (box ID) from database based on Request ID
        public static function fetch_box_id($id)
        {
            global $wpdb; 
            // die(print_r($wpdb->prefix));
            $array = array();
            $args = [
                'where' => ['ticket_id', $id],
            ];
            $wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
            $box_result = $wpqa_wpsc_epa_boxinfo->get_results($args, false);

            foreach ($box_result as $box) {
                array_push($array, $box->box_id);
            }
            return $array;
        }
        
        //Function to obtain full list of Program Offices
        public static function fetch_program_office_array()
        {
            global $wpdb;
            $array = array();
            $args = [
                'select' => 'office_acronym',
            ];
            $wpqa_wpsc_epa_program_office = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_program_office");
            $po_result = $wpqa_wpsc_epa_program_office->get_results($args, false);

            foreach ($po_result as $po) {
                array_push($array, $po->office_acronym);
            }
            return $array;
        }
        
        //Convert box patt id to id
        public static function convert_box_id( $id )
        {
            global $wpdb;
            $id = '"'.$id.'"';
            $args = [
                'select' => 'id',
                'where' => ['box_id',  $id],
            ];
            $wpqa_wpsc_box = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
            $request_key = $wpqa_wpsc_box->get_row($args, false);

            $key = $request_key->id;
            return $key;
        }
        
        //Convert box patt id to patt request id
        public static function convert_box_request_id( $id )
        {
            global $wpdb;
            $id = '"'.$id.'"';
            $args = [
                'select' => 'request_id',
                'where' => [
                    ['box_id',  $id],
                    ['wpqa_wpsc_epa_boxinfo.ticket_id', 'wpqa_wpsc_ticket.id', 'AND'],
                ]
            ];
            $wpqa_wpsc_box = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo, {$wpdb->prefix}wpsc_ticket");
            $request_key = $wpqa_wpsc_box->get_row($args, false);

            $key = $request_key->request_id;
            return $key;
        }
        
        //Convert patt request id to id
        public static function convert_request_id( $id )
        {
            global $wpdb;
            $id = '"'.$id.'"';
            $args = [
                'select' => 'id',
                'where' => ['request_id',  $id],
            ];
            $wpqa_wpsc_request = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_ticket");
            $id_key = $wpqa_wpsc_request->get_row($args, false);

            $key = $id_key->id;
            return $key;
        }
        
        //Convert id to patt request id
        public static function convert_request_db_id( $id )
        {
            global $wpdb;
            $id = '"'.$id.'"';
            $args = [
                'select' => 'request_id',
                'where' => ['id',  $id],
            ];
            $wpqa_wpsc_request = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_ticket");
            $id_key = $wpqa_wpsc_request->get_row($args, false);

            $key = $id_key->request_id;
            return $key;
        }
        
        //Function to obtain box ID, title, date and contact 
        
        public static function fetch_box_content($id)
        {
            global $wpdb; 
            // die(print_r($wpdb->prefix));
            $array = array();
            $args = [
                'where' => ['box_id', $id],
            ];
            $wpqa_wpsc_epa_folderdocinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_folderdocinfo");
            $box_content = $wpqa_wpsc_epa_folderdocinfo->get_results($args, false);

            foreach ($box_content as $box) {
                $parent = new stdClass;
                $parent->id = $box->folderdocinfo_id;
                $parent->title = $box->title;
                $parent->date = $box->date;
                $parent->contact = $box->epa_contact_email;
                $parent->source_format = $box->source_format;
                $array[] = $parent;

            }
            return $array;
        }
        
        //Function to obtain box ID, location, shelf, bay and index from ticket 
        
        public static function fetch_box_details($id)
        {
            global $wpdb; 
            // die(print_r($wpdb->prefix));
            $array = array();
            $args = [
                'where' => [
                    ['ticket_id',  $id],
                    ['wpqa_wpsc_epa_boxinfo.storage_location_id', 'wpqa_wpsc_epa_storage_location.id', 'AND'],
                ]
            ];
            $wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo, {$wpdb->prefix}wpqa_wpsc_epa_storage_location");
            $box_result = $wpqa_wpsc_epa_boxinfo->get_results($args, false);
        
            foreach ($box_result as $box) {
                $box_shelf_location = $box->aisle . 'a_' .$box->bay .'b_' . $box->shelf . 's_' . $box->position .'p';
                $parent = new stdClass;
                $parent->id = $box->box_id;
                $parent->shelf_location = $box_shelf_location;
                $array[] = $parent;

            }
            return $array;
        }

        //Function to obtain box details from box ID
        public static function fetch_box_id_a( $id )
        {
            $boxidArray = explode(',', $id);
            return $boxidArray;
        }

        //Function to obtain location value from database
        public static function fetch_location( $id )
        {
            global $wpdb;
            $array = array();
            // $box_digitization_center = $wpdb->get_results( "SELECT * FROM wpqa_wpsc_epa_boxinfo WHERE ticket_id = " . $GLOBALS['id']);
            $args = [
                'where' => ['ticket_id', $id],
            ];
            $wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
            $box_digitization_center = $wpqa_wpsc_epa_boxinfo->get_results($args, false);

            foreach ($box_digitization_center as $location) {
                array_push($array, strtoupper($location->location));
            }
            return $array;
        }

        //Function to obtain program office from database
        public static function fetch_program_office( $id )
        {
            global $wpdb;
            $array = array();
            // $request_program_office = $wpdb->get_results("SELECT acronym FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_program_office WHERE wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.id AND ticket_id = " . $GLOBALS['id']);
            $args = [
                'select' => 'acronym',
                'where' => [
                    ['ticket_id',  $id],
                    ['wpqa_wpsc_epa_boxinfo.program_office_id', 'wpqa_wpsc_epa_program_office.id', 'AND'],
                ]
            ];

            $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_epa_program_office = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo, {$wpdb->prefix}wpsc_epa_program_office");
            $request_program_office = $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_epa_program_office->get_results($args, false);
            // dd($request_program_office);
            foreach ($request_program_office as $program_office) {
                array_push($array, strtoupper($program_office->acronym));
            }
            // dd($array);
            return $array;
        }

        //Function to obtain shelf from database
        public static function fetch_shelf( $id )
        {
            global $wpdb;
            $array = array();
            // $request_shelf = $wpdb->get_results("SELECT shelf FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_ticket WHERE wpqa_wpsc_epa_boxinfo.ticket_id = wpqa_wpsc_ticket.id AND ticket_id = " . $GLOBALS['id']);
            $args = [
                'select' => 'shelf',
                'where' => [
                    ['ticket_id',  $id],
                    ['wpqa_wpsc_epa_boxinfo.ticket_id', 'wpqa_wpsc_ticket.id', 'AND'],
                ],
            ];
            $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_ticket = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo, {$wpdb->prefix}wpsc_ticket");
            $request_shelf = $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_ticket->get_results($args, false);

            foreach ($request_shelf as $shelf) {
                array_push($array, strtoupper($shelf->shelf));
            }
            return $array;
        }

        //Function to obtain bay from database
        public static function fetch_bay( $id )
        {
            global $wpdb;
            $array = array();
            // $request_bay = $wpdb->get_results("SELECT bay FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_ticket WHERE wpqa_wpsc_epa_boxinfo.ticket_id = wpqa_wpsc_ticket.id AND ticket_id = " . $GLOBALS['id']);

            $args = [
                'select' => 'bay',
                'where' => [
                    ['ticket_id',  $id],
                    ['wpqa_wpsc_epa_boxinfo.ticket_id', 'wpqa_wpsc_ticket.id', 'AND'],
                ],
            ];
            $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_ticket = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo, {$wpdb->prefix}wpsc_ticket");
            $request_bay = $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_ticket->get_results($args, false);

            foreach ($request_bay as $bay) {
                array_push($array, strtoupper($bay->bay));
            }
            return $array;
        }

        //Function to obtain create month and year from database
        public static function fetch_create_date( $id )
        {
            global $wpdb;
            // $request_create_date = $wpdb->get_row( "SELECT date_created FROM wpqa_wpsc_ticket WHERE id = " . $GLOBALS['id']);

            $args = [
                'select' => 'date_created',
                'where' => ['id',  $id],
            ];
            $wpqa_wpsc_ticket = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_ticket");
            $request_create_date = $wpqa_wpsc_ticket->get_row($args, false);

            $create_date = $request_create_date->date_created;
            $date = strtotime($create_date);

            return strtoupper(date('M y', $date));
        }

        //Function to obtain request key
        public static function fetch_request_key( $id )
        {
            global $wpdb;
            // $request_key = $wpdb->get_row( "SELECT ticket_auth_code FROM wpqa_wpsc_ticket WHERE id = " . $GLOBALS['id']);

            $args = [
                'select' => 'ticket_auth_code',
                'where' => ['id',  $id],
            ];
            $wpqa_wpsc_ticket = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_ticket");
            $request_key = $wpqa_wpsc_ticket->get_row($args, false);

            $key = $request_key->ticket_auth_code;
            return $key;
        }

        //Function to obtain box count
        public static function fetch_box_count( $id )
        {
            global $wpdb;
            // $box_count = $wpdb->get_row( "SELECT COUNT(ticket_id) as count FROM wpqa_wpsc_epa_boxinfo WHERE ticket_id = " . $GLOBALS['id']);

            $args = [
                'select' => 'COUNT(ticket_id) as count',
                'where' => ['ticket_id', $id],
            ];
            $wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
            $box_count = $wpqa_wpsc_epa_boxinfo->get_row($args, false);

            $count_val = $box_count->count;
            return $count_val;
        }
        
        function convert_epc_pattboxid($epc) 
        {
            $remove_E = strtok($epc, 'E');
            
            $newstr = substr_replace($remove_E, '-', 7, 0);
            
            return $newstr;
        }
            
        function convert_pattboxid_epc($pattid) 
        {
            $add_E = str_replace('-', '', $pattid).'E';
            
            $str_length = 24;
            
            $newstr = str_pad($add_E, $str_length, 0);
            
            
            return $newstr;
        }

    }
    // new Patt_Custom_Func;
}
