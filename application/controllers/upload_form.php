<?php

class Upload_form extends CI_controller
{
    public $data = array();
    public $error_message = '';
    public $role;
    public $current_user_id;
    public $current_user_role;
    
    function __construct()
    {

        parent::__construct();

        $this->data=array();
        
        if (!is_logged_in()) 
        {
            redirect('login');
        }

        $this->load->library('upload');
        $this->load->library('form_validation');
        $this->load->helper('form');
        
        $this->load->model(array(
            'user_model',
            'uploads_view_model',
            'wholesale_dispath_model',
            'wholesale_demand_model',
            'bulk_dispath_model',
            'demand_model',
            'address_model',
            'plant_model'
            ));

        $this->role = get_user_role();
        $this->current_user_id = get_current_user_id();
        $this->current_user_role = get_user_role();
    }


    function index( $user_id = 0, $date = null )
    {
        try
        {
            //check time of add
            $access = ($this->role==1)?TRUE:check_time_of("add",$editdate=(!empty($date))?$date:NULL);
            
            if(!$access)
            {
                $this->service_message->set_flash_message('web_form_add_error'); 

                if($this->role==1){
                    redirect('upload_type'); 
                }else{
                    redirect('upload/list_view'); 
                }    
            }
            

            //get current user id
            if ($this->role == 1) 
            {

                $result = $this->user_model->get_by_id($user_id);

                if(!count($result))
                    throw new Exception('Invalid User ID.');
                        
                $this->current_user_role = $result['role'];
                $this->current_user_id = $result['id'];
            } 

            $this->data['current_user_id'] = $this->current_user_id;

            $form = $_POST;

            if( isset($form['date']) )
            {
                $date = getCustomDate($form['date'], 'd-m-Y');
            }

            
            //get address data
            $this->data['user_details'] = $this->user_model->get_by_id($this->current_user_id);

            //check if no address means throw error message
            if($this->data['user_details']['address_id']==0){
                $this->service_message->set_flash_message('address_empty');
                redirect('upload/list_view');
            }

            $this->data['address']      = $this->address_model->get_address($this->current_user_id);
            
            
            $address_id = isset($this->data['address']['id'])?$this->data['address']['id']:0;

            $this->data['comments'] = '';
            $comment_date = is_null($date)?date('Y-m-d'):$date;
            $result = $this->db->get_where('comments', array('user_id' => $this->current_user_id, 'date' => str2DBDate($comment_date)))->row_array();
            
            if(isset($result['description']))
                $this->data['comments'] = $result['description'];

            $comments_data = array();
            $comments_data['user_id'] = $user_id; 
            $comments_data['date']    = str2DBDate($date);
            
//die($comments_data['date']);
            switch ($this->current_user_role) 
            {
                case 2:

                    //check if no Demands means throw error message
                    $this->data['demand_details'] = $this->demand_model->get_where( array('address_id' => $address_id))->result_array(); //'user_id' => $this->current_user_id,                 
                    
                    if(is_array($this->data['demand_details']) && !count($this->data['demand_details'])){
                        $this->service_message->set_flash_message('demand_empty');
                        if ($this->role == 1){
                            redirect('upload_type');
                        }else{
                            redirect('upload/list_view');
                        }    
                    }


                    $this->data['dp_data'] = $this->bulk_dispath_model->get_data( $this->current_user_id, str2DBDate($date) );

                    for($i =1; $i<=24; $i++){
                    	$this->form_validation->set_rules("dp[$i]", "dp[$i]", 'trim|required|numeric');
                    }
                    	
                    if($this->form_validation->run())
                    {
                        $insert_data = array(); 
                        $insert_data['user_id']         = $this->current_user_id;
                        $insert_data['created_id']      = $this->current_user_id;
                         
                        

                        $dp = $this->input->post('dp');
                        foreach ($dp as $k=>$v) 
                        {
                            $insert_data['field_'.($k)]   = $v;
                        }

                        
                        $result = $this->bulk_dispath_model->get_where(array('user_id' => $this->current_user_id, 'date' => str2DBDate($date)));
                        
                        $action_type = 'insert';
                        if( $result->num_rows() )
                            $action_type = 'edit';

                        

                        switch ($action_type) 
                        {
                            case 'insert':
                                $insert_data['date']            = str2DBDate($date);
                                $insert_data['created_time']    = str2DBDT(date("Y-m-d H:i:s")); 
                                $insert_data['updated_time']  = str2DBDT(date("Y-m-d H:i:s"));
                                
                                $this->db->insert('bulk_dispath_period_data', $insert_data);

                                $comments_data['description'] = $form['comments'];
                                $this->db->insert('comments', $comments_data);
                                $message = 'Record inserted successfully!.';

                                //send email
                                send_mail($this->current_user_id, $date);
                                break;
                            
                            case 'edit':
                            //echo '<pre>';print_r($this->data['dp_data']);die;
                                if(!isset($this->data['dp_data']['id']))
                                    throw new Exception("Invalid ID.");
                                
                                $this->db->where('user_id',$user_id);
                                $this->db->where('date',str2DBDate($date));
                                $insert_data['updated_time']  = str2DBDT(date("Y-m-d H:i:s"));                                  
                                $this->db->update('bulk_dispath_period_data', $insert_data);

                                $this->db->where($comments_data);
                                $comments_data['description'] = $form['comments'];
                                $this->db->update('comments', $comments_data);
                                $message = 'Record updated successfully!.';
                                break;

                            default:
                                # code...
                                break;
                        }
                        
                        $this->service_message->set_flash_message('custom_message_success',$message);

                        //$this->session->unset_userdata('upload_user');

                        redirect('upload/list_view');
                    }

                    $this->data['date'] = is_null($date)?time():strtotime($date);


                    $this->layout->view('upload_form/bulk', $this->data);
                    break;
                
                case 3:
                    $result = $this->plant_model->get_plant_supply_details($address_id);  //'user_id' => $this->current_user_id 

                    //check if no Plants means throw error message
                    if(is_array($result) && !count($result)){
                        $this->service_message->set_flash_message('plant_empty');
                        if ($this->role == 1){
                            redirect('upload_type');
                        }else{
                            redirect('upload/list_view');
                        }  
                    }

                    $plants = array();
                    foreach ($result as $k=>$plant) 
                    {
                        $l = $k+1;
                       $plants[$plant['id']] = $plant;

                       for($j =0; $j<24; $j++)
                       {
                            $this->form_validation->set_rules("dp{$l}_mw{$plant['id']}[$j]", "dp{$l}_mw{$plant['id']}[$j]", 'trim|required|numeric');
                            $this->form_validation->set_rules("dp{$l}_mv{$plant['id']}[$j]", "dp{$l}_mv{$plant['id']}[$j]", 'trim|required|numeric');
                       }
                       
                    }
//die;
                    $this->data['unit_count']   = count($plants);
                    $this->data['dp_data']      = $this->wholesale_dispath_model->get_unit_wise_data($date, $this->current_user_id);


                    $this->data['units']    = array_keys($plants);

                    $this->data['plants']   = $plants;

                    if($this->form_validation->run())
                    {
                        $insert_data = array(); 
                        $insert_data['date']            = str2DBDate($date);
                        $insert_data['user_id']         = $this->current_user_id;
                        $insert_data['created_id']      = $this->current_user_id;
                        
                        
                        $result = $this->wholesale_dispath_model->get_where(array('user_id' => $this->current_user_id, 'date' => str2DBDate($date)));
                        
                        $action_type = 'insert';
                        if( $result->num_rows() )
                            $action_type = 'edit';

                        switch ($action_type) 
                        {
                            case 'insert':
                                $comments_data['description'] = $form['comments'];
                                $this->db->insert('comments', $comments_data);
                                break;
                            
                            case 'edit':
                                $this->db->where($comments_data);
                                $comments_data['description'] = $form['comments'];
                                $this->db->update('comments', $comments_data);
                                break;
                        }

                        foreach ($this->data['units'] as $k => $unit_id) 
                        {
                            $l = $k+1;

                            $insert_data['unit_no'] = $unit_id;

                            $data_mw = $insert_data;
                            $data_mv = $insert_data;
                            //collect MW data
                            $data_mw['type'] = 'MW';
                            $dp_mw = $form["dp{$l}_mw{$unit_id}"];
                            foreach ($dp_mw as $m=>$v) 
                            {
                                $data_mw['field_'.($m+1)]   = $v;
                            }

                            //collect MVAR data
                            $data_mv['type'] = 'MVAR';
                            $dp_mv = $form["dp{$l}_mv{$unit_id}"];
                            foreach ($dp_mv as $m=>$v) 
                            {
                                $data_mv['field_'.($m+1)]   = $v;
                            }

                            

                            switch ($action_type) 
                            {
                                case 'insert':
                                    $data_mw['created_time']    = str2DBDT(date("Y-m-d H:i:s")); 
                                    $data_mv['created_time']    = str2DBDT(date("Y-m-d H:i:s"));
                                    $data_mw['updated_time']    = str2DBDT(date("Y-m-d H:i:s")); 
                                    $data_mv['updated_time']    = str2DBDT(date("Y-m-d H:i:s")); 

                                    $this->db->insert('wholesale_dispath_period_data', $data_mw);
                                    $this->db->insert('wholesale_dispath_period_data', $data_mv);
                                    $message = 'Record inserted successfully!.';

                                    //send email
                                    send_mail($this->current_user_id, $date);

                                    break;
                                
                                case 'edit':

                                    $data_mw['updated_time']    = str2DBDT(date("Y-m-d H:i:s")); 
                                    $data_mv['updated_time']    = str2DBDT(date("Y-m-d H:i:s")); 

                                    $where = array();
                                    $where['unit_no']   = $unit_id;
                                    $where['user_id']   = $this->current_user_id;
                                    $where['date']      = str2DBDate($date);

                                   
                                    $where['type']      = 'MW';
                                    $this->db->where($where);
                                    $this->db->update('wholesale_dispath_period_data', $data_mw);

                                    $where['type']      = 'MVAR';
                                    $this->db->where($where);
                                    $this->db->update('wholesale_dispath_period_data', $data_mv);
                                    $message = 'Record updated successfully!.';
                                    break;

                                default:
                                    # code...
                                    break;
                            }
                        }
                        
                        $this->service_message->set_flash_message('custom_message_success',
                $message);

                        //$this->session->unset_userdata('upload_user');

                        redirect('upload/list_view');
                    }

                    $this->data['date'] = is_null($date)?time():strtotime($date);

                    $this->layout->view('upload_form/wholesale', $this->data);
                    break;
                default:
                    # code...
                    break;
            }
            

            
        }
        catch(Exception $e)
        {
            $this->service_message->set_flash_message('custom_message_error',
                $e->getMessage());

            redirect('upload/list_view');
        }
        
        
    }


    function view( $user_id = 0, $date = null , $download='' , $flag = FALSE, $path='', $save='',$objinc=1)
    {
        try
        {

            //get current user id
            if ($this->role == 1) 
            {
                $result = $this->user_model->get_by_id($user_id);

                if(!count($result))
                    throw new Exception('Invalid User ID.');
                        
                $this->current_user_role = $result['role'];
                $this->current_user_id = $result['id'];
            } 
            $uploaddatee = $date;
            
            $this->data['current_user_id'] = $this->current_user_id;

            $submit_date = $this->uploads_view_model->get_where(array('user_id'=>$this->current_user_id,'date'=>str2DBDate($date)));

            $this->data['submit_date'] = (isset($submit_date['submission_date']))?$submit_date['submission_date']:date('m/d/Y');
            //get address data
            $this->data['address']      = $this->address_model->get_address($this->current_user_id);
            $this->data['user_details'] = $this->user_model->get_by_id($this->current_user_id);
            
            $address_id = isset($this->data['address']['id'])?$this->data['address']['id']:0;
            
            $this->data['comments'] = '';
            $result = $this->db->get_where('comments', array('user_id' => $this->current_user_id, 'date' => str2DBDate($date)))->row_array();
            if(isset($result['description']))
                $this->data['comments'] = $result['description'];
                
            
            $this->data['date'] = is_null($date)?time():strtotime($date);
                
            
            switch ($this->current_user_role) 
            {
                case 2:

                    $this->data['demand_details'] = $this->demand_model->get_where( array('address_id'=>$address_id))->result_array(); //'user_id' => $this->current_user_id

                    $this->data['dp_data'] = $this->bulk_dispath_model->get_data( $this->current_user_id, str2DBDate($date) );                    

                    if( strcmp($download, 'excel') === 0 )
                        $this->generate_excel($path,$save,$this->data['user_details']['name'],$uploaddatee,$objinc);

                    $str = $this->layout->view('upload_view/bulk', $this->data, TRUE);
                    

                    if($flag)
                        return $str;

                    break;
                
                case 3:

                    //$result = $this->plant_model->get_where(array('address_id' => $address_id))->result_array(); //'user_id' => $this->current_user_id
                    $result = $this->plant_model->get_plant_supply_details($address_id);  //'user_id' => $this->current_user_id 

                    $plants = array();
                    foreach ($result as $k=>$plant) 
                    {
                        $l = $k+1;
                       $plants[$plant['id']] = $plant;

                       $this->form_validation->set_rules("dp{$l}_mw{$plant['id']}[]", 'dp_mw', '');
                       $this->form_validation->set_rules("dp{$l}_mv{$plant['id']}[]", 'dp_mv', '');
                    }

                    $this->data['unit_count']   = count($plants);
                    $this->data['dp_data']      = $this->wholesale_dispath_model->get_unit_wise_data($date, $this->current_user_id);


                    $this->data['units']    = array_keys($plants);

                    $this->data['plants']   = $plants;

                    if( strcmp($download, 'excel') === 0 )
                        $this->generate_excel($path,$save,$this->data['user_details']['name'],$uploaddatee,$objinc);
                    

                    $str = $this->layout->view('upload_view/wholesale', $this->data , TRUE);

                    if($flag)
                        return $str;

                    break;
                default:
                    # code...
                    break;
            }
            

            
        }
        catch(Exception $e)
        {
            $this->service_message->set_flash_message('custom_message_error',
                $e->getMessage());

            redirect('upload/list_view');
        }
        
        
    }

    function generate_excel($path='',$save='',$user_id='',$date='',$objinc=1)
    {
        $this->load->library('excel', array(), 'excel'.$objinc);
        
        $data = $this->data;

        $submission_date = $this->uploads_view_model->get_where(array('user_id'=>$data['current_user_id'],'date'=>$date));

        $data['submit_date'] = (isset($submission_date['submission_date']))?$submission_date['submission_date']:date('Y-m-d');

        $excel = 'excel'.$objinc;

        $this->excel = $this->{$excel};

        $demand_details = array();
        extract($data);

        $this->excel->setActiveSheetIndex(0);

        //default style set for sheet
        $this->excel->getDefaultStyle()->getFont()->setName('Arial');
        $this->excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
            HORIZONTAL_LEFT);

        //fill color set for sheet
        $this->excel->getActiveSheet()->getDefaultStyle()->getFill()->applyFromArray(array
            ('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' =>
                    'FFFFFF')));

        //font style set
        for ($i = 1; $i <= 60; $i++) 
        {
            $this->excel->getActiveSheet()->getStyle("A$i:Z$i")->getFont()->setSize(14);
        }

        //B column width set
        $this->excel->getActiveSheet()->getColumnDimension("B")->setWidth(27.5);

        //logo image add (1st row)
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Gridco');
        $objDrawing->setDescription('Gridco');
        $gdImage = imagecreatefrompng(BASEPATH_CUSTOM . "/assets/images/logo.png");
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setCoordinates('B1');
        $objDrawing->setOffsetY(15);
        $objDrawing->setWorksheet($this->excel->getActiveSheet());
        $this->excel->getActiveSheet()->getRowDimension('1')->setRowHeight(46.5);
        $this->excel->getActiveSheet()->mergeCells('B1:U1');
        $this->excel->getActiveSheet()->getStyle('B1:U1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);

        //2nd row
        $this->excel->getActiveSheet()->getRowDimension('2')->setRowHeight(33.75);

        //third row
        $title = 'DAILY AVAILABILITY DECLARATION FOR DISPATCH DAY '.str2USDate($date);
        if($this->current_user_role == 2)
            $title = 'DAILY DEMAND SHEET FOR DISPATCH DAY '.str2USDate($date);

        $this->excel->getActiveSheet()->getRowDimension('3')->setRowHeight(24);
        $this->excel->getActiveSheet()->mergeCells('B3:H3');
        $this->excel->getActiveSheet()->setCellValue('B3', $title);
        $this->excel->getActiveSheet()->setCellValue('I3', 'DATE');
        $this->excel->getActiveSheet()->setCellValue('J3', date('m/d/Y', $date));
        $this->excel->getActiveSheet()->mergeCells('J3:U3');

        $this->excel->getActiveSheet()->getStyle('B3:U3')->getBorders()->getTop()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B3:U3')->getBorders()->getBottom()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B3')->getBorders()->getLeft()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('U3')->getBorders()->getRight()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('Q3')->getBorders()->getLeft()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //4th row
        $this->excel->getActiveSheet()->getRowDimension('4')->setRowHeight(19.5);
        $this->excel->getActiveSheet()->mergeCells('J4:U4');
        $this->excel->getActiveSheet()->setCellValue('J4', 'MM/DD/YYYY');
        $this->excel->getActiveSheet()->getStyle("J4")->getFont()->setSize();
        $this->excel->getActiveSheet()->getStyle("J4")->getFont()->setItalic(true);

        //5th row
        $this->excel->getActiveSheet()->getRowDimension('5')->setRowHeight(16.5);
        $this->excel->getActiveSheet()->getStyles("B5", "MAIN ADDRESS");

        //6th row
        $this->excel->getActiveSheet()->getRowDimension('6')->setRowHeight(24);
        $this->excel->getActiveSheet()->setCellValue('B6', 'Name of Organisation:');
        $this->excel->getActiveSheet()->getStyle('B6:U6')->getBorders()->getTop()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B6:U6')->getBorders()->getBottom()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B6')->getBorders()->getAllBorders()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('V6')->getBorders()->getLeft()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->mergeCells('C6:U6');
        $this->excel->getActiveSheet()->setCellValue('C6', ((isset($address['organization'])) ?
            $address['organization'] : ""));
        //7th row
        $this->excel->getActiveSheet()->getRowDimension('7')->setRowHeight(24);
        $this->excel->getActiveSheet()->setCellValue('B7', 'Mailing Address:');
        $this->excel->getActiveSheet()->getStyle('B7:U7')->getBorders()->getTop()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B7:U7')->getBorders()->getBottom()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B7')->getBorders()->getAllBorders()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('V7')->getBorders()->getLeft()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->mergeCells('C7:U7');
        $this->excel->getActiveSheet()->setCellValue('C7', ((isset($address['mailing_address'])) ?
            $address['mailing_address'] : ""));

        //8th row
        $this->excel->getActiveSheet()->getRowDimension('8')->setRowHeight(24);
        $this->excel->getActiveSheet()->setCellValue('B8', 'Location:');
        $this->excel->getActiveSheet()->getStyle('B8:U8')->getBorders()->getTop()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B8:U8')->getBorders()->getBottom()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B8')->getBorders()->getAllBorders()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('V8')->getBorders()->getLeft()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->mergeCells('C8:U8');
        $this->excel->getActiveSheet()->setCellValue('C8', ((isset($address['location'])) ?
            $address['location'] : ""));

        //9th row
        $this->excel->getActiveSheet()->getRowDimension('9')->setRowHeight(24);
        $this->excel->getActiveSheet()->setCellValue('B9', 'City:');
        $this->excel->getActiveSheet()->setCellValue('C9', ((isset($address['city'])) ?
            $address['city'] : ""));
        $this->excel->getActiveSheet()->getStyle('B9:U9')->getBorders()->getTop()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B9:U9')->getBorders()->getBottom()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('B9')->getBorders()->getAllBorders()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('K9:M9')->getBorders()->getAllBorders()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('V9')->getBorders()->getLeft()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->mergeCells('C9:J9');
        $this->excel->getActiveSheet()->mergeCells('N9:U9');
        $this->excel->getActiveSheet()->mergeCells('K9:M9');
        $this->excel->getActiveSheet()->setCellValue('K9', 'Telephone:');
        $this->excel->getActiveSheet()->setCellValue('N9', ((isset($address['telephone'])) ?
            $address['telephone'] : ""));
        //10th row
        $this->excel->getActiveSheet()->getRowDimension('10')->setRowHeight(22.5);

        //11th row
        $this->excel->getActiveSheet()->getRowDimension('11')->setRowHeight(24);
        $this->excel->getActiveSheet()->setCellValue('B11', "Market Coordinator");
        $this->excel->getActiveSheet()->getStyle("B11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
            HORIZONTAL_LEFT);
        $this->excel->getActiveSheet()->mergeCells('B11:U11');
        $this->excel->getActiveSheet()->getStyle("B11")->getFont()->setBold(true);

        //12 - 15row
        $market_co_ord = array(
            "12" => array(
                'field1' => 'name',
                'label1' => 'Name',
                'field2' => 'role',
                'label2' => 'Type of Participant'),
            "13" => array(
                'field1' => 'cellphone',
                'label1' => 'Cell Phone',
                'field2' => 'otherphone',
                'label2' => 'Other Phone'),
            "14" => array(
                'field1' => 'email',
                'label1' => 'Email',
                'field2' => 'fax',
                'label2' => 'Fax'),
            "15" => array(
                'field1' => 'id',
                'label1' => 'Special unique ID',
                'field2' => 'date',
                'label2' => 'Submit Date'));
        for ($i = 12; $i <= 15; $i++) 
        {

            $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(24);
            $this->excel->getActiveSheet()->setCellValue("B$i", "{$market_co_ord[$i]['label1']}:");
            $field1 = $user_details[$market_co_ord[$i]['field1']];
            $this->excel->getActiveSheet()->setCellValue("C$i", $field1);
            $this->excel->getActiveSheet()->getStyle("C$i")->getFont()->setBold(true);

            $this->excel->getActiveSheet()->getStyle("B$i:U$i")->getBorders()->getTop()->
                setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $this->excel->getActiveSheet()->getStyle("B$i:U$i")->getBorders()->getBottom()->
                setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $this->excel->getActiveSheet()->getStyle("B$i")->getBorders()->getAllBorders()->
                setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $this->excel->getActiveSheet()->getStyle("K$i:M$i")->getBorders()->
                getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $this->excel->getActiveSheet()->getStyle("V$i")->getBorders()->getLeft()->
                setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle("K$i")->getFont()->setBold(true);
            $this->excel->getActiveSheet()->mergeCells("C$i:J$i");
            $this->excel->getActiveSheet()->mergeCells("N$i:U$i");
            $this->excel->getActiveSheet()->mergeCells("K$i:M$i");
            $this->excel->getActiveSheet()->setCellValue("K$i", "{$market_co_ord[$i]['label2']}:");

            if ($market_co_ord[$i]['field2'] == "role") {
                $field2 = user_role_name($user_details['id']);
            } else{
                if ($market_co_ord[$i]['field2'] == "date") {
                    $field2 = (!empty($submit_date)) ? str2USDate($submit_date):"";
                    $this->excel->getActiveSheet()->getStyle("L$i")->getNumberFormat()->
                        setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14);
                } else {
                    $field2 = $user_details[$market_co_ord[$i]['field2']];
                }
                
            }    
            $this->excel->getActiveSheet()->setCellValue("N$i", $field2);
        }

        //16th row
        $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(19.5);
        $this->excel->getActiveSheet()->mergeCells("N$i:Q$i");
        $this->excel->getActiveSheet()->setCellValue("N$i", "MM/DD/YYYY");
        $this->excel->getActiveSheet()->getStyle("N$i")->getFont()->setSize();
        $this->excel->getActiveSheet()->getStyle("N$i")->getFont()->setItalic(true);

        $column = array(
            "C",
            "D",
            "E",
            "F",
            "G",
            "H",
            "I",
            "J",
            "K",
            "L",
            "M",
            "N",
            "O",
            "P",
            "Q",
            "R",
            "S",
            "T",
            "U",
            "V",
            "W",
            "X",
            "Y",
            "Z");

        switch ($this->current_user_role) 
        {

            case 3:

                /*
                $i++;
                $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(24);
                $this->excel->getActiveSheet()->mergeCells("B$i:U$i");
                $this->excel->getActiveSheet()->setCellValue("B$i", "Supply Details");
                $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_LEFT);
                */

                $i++;
                $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(24);
                $this->excel->getActiveSheet()->setCellValue("B$i", "Plant Name:");
                $this->excel->getActiveSheet()->setCellValue("C$i", (!empty($user_details['plant']))?get_plant_name($user_details['plant']):"");
                $this->excel->getActiveSheet()->getStyle("C$i")->getBorders()->getAllBorders()->
                    setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                /*    
                $i++;
                //17th row
                $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(24);
                $this->excel->getActiveSheet()->setCellValue("C$i",
                    "Recipient of Power Supply ");
                $this->excel->getActiveSheet()->mergeCells("C$i:H$i");
                $this->excel->getActiveSheet()->setCellValue("J$i", "Amount of Power (MW) ");
                $this->excel->getActiveSheet()->mergeCells("J$i:R$i");

                $sno=1;
                foreach ($this->data['demand_details'] as $rows) 
                {
                    $i++;
                    $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(29.25);
                    $this->excel->getActiveSheet()->setCellValue("B$i", integerToRoman($sno).". ");
                    $this->excel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                        HORIZONTAL_RIGHT);
                    $this->excel->getActiveSheet()->mergeCells("C$i:H$i");
                    $this->excel->getActiveSheet()->setCellValue("C$i", ((isset($rows['source'])) ?
                        trim($rows['source'], '_') : ""));
                    $this->excel->getActiveSheet()->mergeCells("J$i:R$i");
                    $this->excel->getActiveSheet()->setCellValue("J$i", ((isset($rows['power'])) ?
                        trim($rows['power'], '_') : ""));

                    $sno++;
                }
                */

                $i++;
                $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(29.25);
                $this->excel->getActiveSheet()->mergeCells("B$i:Z$i");
                $this->excel->getActiveSheet()->setCellValue("B$i", "Generation Details ");
                $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_LEFT);

                foreach ($units as $k=>$v) 
                {
                    $l = $k+1;

                    $i++;
                    $temp = isset($plants[$v]['no_units'])?$plants[$v]['no_units']:"";
                    $temp .= isset($plants[$v]['plant_name'])?'('.$plants[$v]['plant_name'].')':"";

                    $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);
                    $this->excel->getActiveSheet()->setCellValue("B$i", "Unit No.:");
                    $this->excel->getActiveSheet()->setCellValue("C$i", $temp);
                    $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                    $this->excel->getActiveSheet()->mergeCells("C$i:Z$i");
                    $this->excel->getActiveSheet()->getStyle("C$i:Z$i")->getBorders()->getTop()->
                        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $this->excel->getActiveSheet()->getStyle("C$i:Z$i")->getBorders()->getBottom()->
                        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $this->excel->getActiveSheet()->getStyle("B$i")->getBorders()->getAllBorders()->
                        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $this->excel->getActiveSheet()->getStyle("AA$i")->getBorders()->getLeft()->
                        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    //header column
                    $i++;
                    $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);
                    $this->excel->getActiveSheet()->setCellValue("B$i", "Disptach Period:");
                    $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                    $this->excel->getActiveSheet()->getStyle("B$i")->getBorders()->getAllBorders()->
                        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    $j = 1;
                    foreach ($column as $val) {
                        $this->excel->getActiveSheet()->setCellValue($val . "$i", "$j");
                        $this->excel->getActiveSheet()->getStyle($val . "$i")->getBorders()->
                            getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $j = $j + 1;
                    } 

                    //mw row
                    $i++;
                    $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);
                    $this->excel->getActiveSheet()->setCellValue("B$i", "MW:");
                    $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                    $this->excel->getActiveSheet()->getStyle("B$i")->getBorders()->getAllBorders()->
                        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


                    $j = 1;
                    foreach ($column as $val) 
                    {
                        $temp = (isset($dp_data[$v]['MW']['field_'.$j]))?$dp_data[$v]['MW']['field_'.$j]:"";

                        $this->excel->getActiveSheet()->setCellValue($val . "$i", $temp);
                        $this->excel->getActiveSheet()->getStyle($val . "$i")->getBorders()->
                            getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $j = $j + 1;
                    }  


                    //mVAr row
                    $i++;
                    $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);
                    $this->excel->getActiveSheet()->setCellValue("B$i", "MVAr:");
                    $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                    $this->excel->getActiveSheet()->getStyle("B$i")->getBorders()->getAllBorders()->
                        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    $j = 1;
                    foreach ($column as $val) 
                    {
                        $temp = (isset($dp_data[$v]['MVAR']['field_'.$j]))?$dp_data[$v]['MVAR']['field_'.$j]:"";
                        $this->excel->getActiveSheet()->setCellValue($val . "$i", $temp);
                        $this->excel->getActiveSheet()->getStyle($val . "$i")->getBorders()->
                            getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                        $j = $j + 1;
                    }  

                    $i++;
                    $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);


                    $i++;
                    $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(24);
                    $this->excel->getActiveSheet()->mergeCells("B$i:U$i");
                    $this->excel->getActiveSheet()->setCellValue("B$i", "Supply Details");
                    $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                    $this->excel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                        HORIZONTAL_LEFT);

                    $i++;
                    //17th row
                    $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(24);
                    $this->excel->getActiveSheet()->setCellValue("C$i",
                        "Recipient of Power Supply ");
                    $this->excel->getActiveSheet()->mergeCells("C$i:H$i");
                    $this->excel->getActiveSheet()->setCellValue("J$i", "Contracted Power (MW) ");
                    $this->excel->getActiveSheet()->mergeCells("J$i:R$i");

                    $sno=1;
                    $source_det = explode(",",$plants[$v]['source']);
                    $power_det = explode(",",$plants[$v]['power']);

                    foreach ($source_det as $ke =>$rows) 
                    {
                        $i++;
                        $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(29.25);
                        $this->excel->getActiveSheet()->setCellValue("B$i", integerToRoman($sno).". ");
                        $this->excel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                            HORIZONTAL_RIGHT);
                        $this->excel->getActiveSheet()->mergeCells("C$i:H$i");
                        $this->excel->getActiveSheet()->setCellValue("C$i", ((isset($rows)) ?
                            trim($rows, '_') : ""));
                        $this->excel->getActiveSheet()->mergeCells("J$i:R$i");
                        $this->excel->getActiveSheet()->setCellValue("J$i", ((isset($power_det[$ke])) ?
                            trim($power_det[$ke], '_') : ""));

                        $sno++;
                    }

                    $i++;
                    $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);

                } 
                break;
            case 2:
                
                $i++;
                $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(24);
                $this->excel->getActiveSheet()->mergeCells("B$i:U$i");
                $this->excel->getActiveSheet()->setCellValue("B$i", "Demand Details");
                $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_LEFT);

                $i++;
                $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(24);
                $this->excel->getActiveSheet()->setCellValue("B$i", "Withdrawal Point:");
                $this->excel->getActiveSheet()->setCellValue("C$i", ((isset($address['deliverypoint'])) ?
            $address['deliverypoint'] : ""));
                $this->excel->getActiveSheet()->getStyle("C$i")->getBorders()->getAllBorders()->
                    setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $i++;
                $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(24);
                $this->excel->getActiveSheet()->setCellValue("C$i",
                    "Source (s) of Power Supply ");
                $this->excel->getActiveSheet()->mergeCells("C$i:H$i");
                $this->excel->getActiveSheet()->setCellValue("J$i", "Contracted Power (MW) ");
                $this->excel->getActiveSheet()->mergeCells("J$i:R$i");

                $sno=1;
                foreach ($this->data['demand_details'] as $row) 
                {
                    $i++;                   

                    $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(29.25);
                    $this->excel->getActiveSheet()->setCellValue("B$i", integerToRoman($sno).". ");
                    $this->excel->getActiveSheet()->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                        HORIZONTAL_RIGHT);
                    $this->excel->getActiveSheet()->mergeCells("C$i:H$i");
                    $this->excel->getActiveSheet()->setCellValue("C$i", ((isset($row['source'])) ?
                        trim($row['source'], '_') : ""));
                    $this->excel->getActiveSheet()->mergeCells("J$i:R$i");
                    $this->excel->getActiveSheet()->setCellValue("J$i", ((isset($row['power'])) ?
                        trim($row['power'], '_') : ""));

                    $sno++;
                }
                $i++;
                $this->excel->getActiveSheet()->getRowDimension($i)->setRowHeight(29.25);

                $i++;
                $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);
                $this->excel->getActiveSheet()->setCellValue("B$i", "Disptach Period:");
                $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle("B$i")->getBorders()->getAllBorders()->
                    setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $j = 1;
                foreach ($column as $val) 
                {
                    $this->excel->getActiveSheet()->setCellValue($val . "$i", "$j");
                    $this->excel->getActiveSheet()->getStyle($val . "$i")->getBorders()->
                        getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $j = $j + 1;
                }
                

                $i++;
                $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);
                $this->excel->getActiveSheet()->setCellValue("B$i", "Demand (MW):");
                $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle("B$i")->getBorders()->getAllBorders()->
                    setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


                $j = 1;
                $k = 0;
                foreach ($column as $val) 
                {
                    $this->excel->getActiveSheet()->setCellValue($val . "$i", ((isset($dp_data['field_' .
                        $j])) ? $dp_data['field_' . $j] : ""));
                    $this->excel->getActiveSheet()->getStyle($val . "$i")->getBorders()->
                        getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $j = $j + 1;
                }

                break;
        }

        //comments and signature
        $i++;
        $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(27.75);
        $this->excel->getActiveSheet()->setCellValue("B$i", "Comments:");
        $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue("C$i", $comments);
        $this->excel->getActiveSheet()->mergeCells("C$i:Z$i");

        $i++;
        $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(27.75);
        $this->excel->getActiveSheet()->setCellValue("B$i", "Signature:");
        $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
        $this->excel->getActiveSheet()->mergeCells("C$i:Z$i");

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        
        if($save=='yes')
        {
            
         $objWriter->save($path."/".$user_id."-".date('Y-m-d',$date).".xlsx");
         
         return TRUE;
        }
         else
        {
            
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
        header('Content-Disposition: attachment;filename="report.xlsx"'); //tell browser what's the file name
        $objWriter->save('php://output');
        die;
        }
        
    }
    
    public function download_all()
    { 

        $this->load->library('zip');
        
        $this->load->helper(array('download','path'));
        
       // $searchkeywords = $this->session->userdata('search_keywords');
            
        $this->zip->clear_data();
            
        $role = get_user_role();
        
        $downloadall = $_POST['download'];
       
        $totalrows   = count($downloadall);

        if(empty($downloadall)){

            $this->service_message->set_flash_message('custom_message_error','Please select atleast one record to download!');
            redirect('upload/list_view');
        }            
        
        //$upload_path = $_SERVER['DOCUMENT_ROOT'] . "data_upload/assets/excel/download";
        $upload_path = "././assets/excel/download";
        
       //set_realpath($upload_path); die;
        
        $this->delete_files($upload_path);
         
        if (!file_exists("$upload_path") && !is_dir("$upload_path")) 
        {
            mkdir("$upload_path", 0755, true);
        }
        for($i=0; $i<$totalrows; $i++)
        {         
         
         $user_data = explode('/',$downloadall[$i]);

         $result = $this->user_model->get_by_id($user_data[0]);
         $this->data = array();
         $this->current_user_id = $result['id'];
         $this->current_user_role = $result['role'];
            
         $this->view($user_data[0],$user_data[1],'excel',$flag=FALSE,$upload_path,'yes',$i);
         $user_data="";
        }
       
        $this->zip->read_dir($upload_path."/", FALSE); 
        
        $this->zip->archive($upload_path."/"."download.zip");
        
        header('Location:'.site_url('assets/excel/download/download.zip'));
        
        die;
        
    }
    
    public function delete_files($target) 
    {
        if(is_dir($target))
        {
            $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
            
            foreach( $files as $file )
            {
                $this->delete_files( $file );      
            }
          
            rmdir( $target );
        } 
        elseif(is_file($target)) 
        {
            unlink( $target );  
        }
    }

}