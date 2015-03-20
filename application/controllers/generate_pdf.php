<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Generate_pdf extends CI_controller
{
    public $data = array();

    function __construct()
    {

        parent::__construct();

        if (!is_logged_in()) {
            redirect('login');
        }

        $this->load->model("user_model");


    }


    function test()
    {
        $this->load->library('email');

        $message = "Hi Ji....";

        $cc_list = array();
        $to = "ramakrishnan.k@izaaptech.in";
        $from = "admin@gmail.com";

        $this->email->from($from,'Admin');
        $this->email->to($to);

        if(is_array($cc_list) && $cc_list!="")
            $this->email->cc($cc_list);

        $this->email->subject("New Data Upload - Status");
        $this->email->message($message);        
        
        $this->email->Send();
    }

    function index($user_id=0,$address_id=0,$date=NULL)
    {
        //get user role
        $role = get_user_role();
        $user_data = get_user_data(); 
        $user_name = rtrim($user_data['name']);

        $this->data['user_name'] = $user_name;

        $content = $this->load->view('pdf', $this->data, TRUE);
        $stylesheet = file_get_contents(base_url()."assets/css/bootstrap.css");

        $this->load->library('pdf');
        $pdf = $this->pdf->load();
        $pdf_path = 'pdf/'.$user_id.'_'.time().'.pdf';
        $pdf->WriteHTML($stylesheet,1); 
        $pdf->WriteHTML($content); // write the HTML into the PDF
        $pdf->Output($pdf_path, 'F'); // save to file

        $cc_list = array();
	    $to = "saravanamot90@gmail.com";
	    $from = "admin@gmail.com";

        $message = 'Data Added Successfully.<br/> ';	                
	    $message .= '<a href = "'.base_url().'index.php">Visit here</a>';

        $this->load->library('email');

        $this->email->from($from,'Admin');
		$this->email->to($to);

		if(is_array($cc_list) && $cc_list!="")
			$this->email->cc($cc_list);

		$this->email->subject("New Data Upload - Status");
		$this->email->message($message);
		
		$this->email->attach($pdf_path);
		
		$this->email->Send();
			

		/*
			$str = 'Data Added Successfully.<br/> ';
	                
	        $str .= '<a href = "'.base_url().'index.php">Visit here</a>';
	                	
			$this->load->library('email_manager');
	                
	        $email_details = $this->config->item('email_details'); 

	        $attachments[] = $pdf_path;

	        $cc_list = array();
	        $to = "saravanamot90@gmail.com";
	        
		    if($email_details)
		        $this->email_manager->send_email($to, '', $email_details['mail_from_id'], $email_details['mail_from_name'], "{$email_details['mail_site_name']} - Data Upload", $str,$cc_list,$attachments);  

		*/
    }

}
 

 
        