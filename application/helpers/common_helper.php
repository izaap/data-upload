<?php
 	function is_logged_in()
     {
 	  
 	  $ci = get_instance();
 	  
       $user_data = get_user_data();
      
      if($user_data && !empty($user_data) && isset($user_data['id']) && !empty($user_data['id'])){
        return true;
        
      }
      else
      {
        return false;
      }
 	}
    
    function get_user_data()
    {
        
        $ci = get_instance();
        
        if($ci->session->userdata('user_data'))
        {
            return $ci->session->userdata('user_data');
        }
        else
        {
            return false;
        }
    }
    
    function get_current_user_id()
    {
        $user_data = get_user_data();
        
        if($user_data && isset($user_data['id']) && (int)$user_data['id'])
            return $user_data['id'];
        
        return FALSE;
    }

    function get_user_role($user_id = 0)
    {
        $ci = get_instance();

        if(!$user_id)
        {
            $user_data = get_user_data();
            $user_id = isset($user_data['id'])?$user_data['id']:0;
        }

        if(!$user_id)
            return false;

        $result = $ci->db->get_where('users', array('id' => $user_id));
        
        if(!$result->num_rows())
        {
            return false;
        }

        return (int)$result->row()->role;
    }
    
    function user_role_name($user_id = "") {
        
         $ci = get_instance();
         
        $res = "";
        $user_id = ($user_id)?$user_id:get_logged_user_id();
        
        $ci->db->select("u.role,r.*");
        $ci->db->from('users u');
        $ci->db->join('roles r', 'r.id = u.role');
        $ci->db->where(array('u.id' => $user_id));
        $result = $ci->db->get()->row_array();

        if(!empty($result)){
            $res = (isset($result['name']))?$result['name']:"";
        }

        return $res;
    }
    
 function is_admin() {
        
        if(get_user_role() === 1 ) {
            
            return true;
        }
        
        return false;
    }
    
 function get_logged_user_id() {

        $user_data = get_user_data();
        $user_id = isset($user_data['id'])?$user_data['id']:0;
        
        return $user_id;
 }
   
 function getCustomDate($str = null, $format = 'Y-m-d')
 {
    $intTime = getTimeStamp($str);

    if ($intTime === FALSE)
      return NULL;
        
    return date($format, $intTime);

 }

 function getTimeStamp($str = '', $format = 'dmy')
 {
    if($str === '')
       return time();

    if($format === 'dmy' OR $format === 'ymd' )
    {
      $str = preg_replace(array('/\//'), array('-'), $str);
    }
    else if($format === 'mdy')
    {
      $str = preg_replace(array('/-/'), array('/'), $str);
    }

    return strtotime($str);
 }

 function str2USDT($str)
    {
        $intTime = strtotime($str);
        if ($intTime === false)
             return NULL;
        return date("m/d/Y h:i A", $intTime);
    }
    
function str2USDate($str)
{
    $intTime = strtotime($str);
    if ($intTime === false)
         return NULL;
    return date("m/d/Y", $intTime);
}

    // no logic for server time to local time.
function str2DBDT($str)
{
    $intTime = strtotime($str);
    if ($intTime === false)
         return NULL;
    return date("Y-m-d H:i:s", $intTime);
}

function str2DBDate($str)
{
    $intTime = strtotime($str);
    
    if ($intTime === false)
        return NULL;
    return date("Y-m-d",$intTime);
}

function addDayswithdate($date,$days){

    $date = strtotime("+".$days." days", strtotime($date));
    return  date("m/d/Y", $date);

}

function day_to_text($date) {
    $day_no = date("N",strtotime($date));
    
    $day_array = array(1 => "Monday" , 2 => "Tuesday" , 3 => "Wednesday" , 4 => "Thursday" , 5 => "Friday" , 6 => "Saturday" , 7 => "Sunday"  );
    
    return $day_array[$day_no];
}

function integerToRoman($integer)
{
     // Convert the integer into an integer (just to make sure)
     $integer = intval($integer);
     $result = '';
     
     // Create a lookup array that contains all of the Roman numerals.
     $lookup = array('M' => 1000,
     'CM' => 900,
     'D' => 500,
     'CD' => 400,
     'C' => 100,
     'XC' => 90,
     'L' => 50,
     'XL' => 40,
     'X' => 10,
     'IX' => 9,
     'V' => 5,
     'IV' => 4,
     'I' => 1);
     
     foreach($lookup as $roman => $value){
      // Determine the number of matches
      $matches = intval($integer/$value);
     
      // Add the same number of characters to the string
      $result .= str_repeat($roman,$matches);
     
      // Set the integer to be the remainder of the integer and the value
      $integer = $integer % $value;
     }
     
     // The Roman numeral should be built, return it
     return $result;
}

function check_time_of($access='add',$edit_date=NULL){

    $ci = get_instance();

    $currdatetime     = date('Y-m-d H:i', local_to_gmt());
    $currdate         = date('Y-m-d', local_to_gmt());   
    $this_friday_date = date('Y-m-d H:i', local_to_gmt(strtotime("This Friday"))); 
    $last_saturday    = date('Y-m-d', local_to_gmt(strtotime("last Saturday"))); 
    $last_sunday      = date('Y-m-d', local_to_gmt(strtotime("last Sunday"))); 
    $this_monday      = date('Y-m-d', local_to_gmt(strtotime("This Monday")));

    $gate_timings = $ci->db->get_where('gate_openings', array('id'=>1))->row_array();

    if($access=='add')
    {

        if(strtotime($currdatetime) >= strtotime($currdate." ".$gate_timings['first_gate_opening']) && strtotime($currdatetime) <= strtotime($currdate." ".$gate_timings['first_gate_closing'])){

            return TRUE;
        }else{

            return FALSE;
        }      
    }
    elseif($access=='edit')
    {
        $edit_date = local_to_gmt($edit_date);

        if(strtotime($currdate) < strtotime($edit_date))
        {

            if(strtotime($this_friday_date) >= strtotime($currdate." ".$gate_timings['second_gate_opening']) && strtotime($this_friday_date) <= strtotime($currdate." ".$gate_timings['second_gate_closing']))
            {

                if(strtotime($edit_date) == strtotime($last_saturday) || strtotime($edit_date) == strtotime($last_sunday) || strtotime($edit_date) == strtotime($this_monday))
                {

                    return TRUE;
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                return FALSE;
            }  

        }
        else
        {
            return TRUE;
        }           

    }


}


function send_mail($user_id=0, $date=NULL)
{
    

  $CI = get_instance();

  $CI->load->model(array(
            'user_model',
            'uploads_view_model',
            'wholesale_dispath_model',
            'wholesale_demand_model',
            'bulk_dispath_model',
            'demand_model',
            'address_model',
            'plant_model'
            ));

  $data = array();

  $result = $CI->user_model->get_by_id($user_id);
  
  $current_user_role = $result['role'];

  //get address data
  $data['address']      = $CI->address_model->get_address($user_id);
  $address_id = isset($data['address']['id'])?$data['address']['id']:0;

  $data['user_details'] = $CI->user_model->get_by_id($user_id);
  $data['demand_details'] = $CI->demand_model->get_where( array('address_id' => $address_id))->result_array(); //'user_id' => $user_id,
  
  
  $data['comments'] = '';
  $result = $CI->db->get_where('comments', array('user_id' => $CI->current_user_id, 'date' => str2DBDate($date)))->row_array();
  if(isset($result['description']))
      $data['comments'] = $result['description'];

  $data['date'] = is_null($date)?time():strtotime($date);

  $str = '';
  switch ($current_user_role) 
  {
      case 2:

          $data['dp_data'] = $CI->bulk_dispath_model->get_data( $user_id, str2DBDate($date) );                    

          $str = $CI->load->view('upload_view/bulk', $data, TRUE);

          break;
      
      case 3:
          $result = $CI->plant_model->get_where(array('address_id' => $address_id))->result_array(); //'user_id' => $user_id, 

          $plants = array();
          foreach ($result as $k=>$plant) 
          {
              $l = $k+1;
             $plants[$plant['id']] = $plant;
          }

          $data['unit_count']   = count($plants);
          $data['dp_data']      = $CI->wholesale_dispath_model->get_unit_wise_data($date, $user_id);


          $data['units']    = array_keys($plants);

          $data['plants']   = $plants;


          

          $str = $CI->load->view('upload_view/wholesale', $data , TRUE);

          break;
      default:
          # code...
          break;
  }

    $stylesheet = file_get_contents(base_url()."assets/css/bootstrap.css");

    $CI->load->library('pdf');
    $pdf = $CI->pdf->load();
    $pdf_path = 'pdf/'.$user_id.'_'.time().'.pdf';
    $pdf->WriteHTML($stylesheet,1); 
    $pdf->WriteHTML($str); // write the HTML into the PDF
    $pdf->Output($pdf_path, 'F'); // save to file
    
    $cc_list = array();
    $to   = "marketoperations@gridcogh.com"; //marketoperations@gridcogh.com
    $from = "admin@gridcogh.com";

    $message = 'Data Added Successfully.<br/> ';                  
    $message .= '<a href = "'.base_url().'index.php">Visit here</a>';

    $CI->config->load('email_conf');

    $CI->load->library('email',$CI->config->item('email'));

    $CI->email->set_newline("\r\n");

    $CI->email->from($from,'Admin');
    $CI->email->to($to);

    if(is_array($cc_list))
      $CI->email->cc($cc_list);

    $CI->email->subject("New Data Upload - Status");
    $CI->email->message($message);

    $CI->email->attach($pdf_path);
    
    $CI->email->send();
   
}

function get_plant_name($id=0)
{
   $CI = get_instance();
    
   $CI->load->model('plant_model');
   $res = $CI->plant_model->get_where(array('id' => $id));

    if($res->num_rows > 0){
        $row = $res->row_array();

        return $row['plant_name'];
    }

    return FALSE;

}