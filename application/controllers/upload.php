<?php

class upload extends CI_controller
{
    public $data = array();
    public $error_message = '';
    public $role;
    function __construct()
    {

        parent::__construct();

        if (!is_logged_in()) 
        {
            redirect('login');
        }

        $this->load->library('excel');
        $this->load->library('upload');
        $this->load->library('form_validation');
        $this->load->helper('form');
        
        $this->load->model(array(
            'user_model',
            'uploads_view_model',
            'wholesale_dispath_model',
            'wholesale_demand_model',
            'bulk_dispath_model',
            'bulk_demand_model'));

        $this->role = get_user_role();
    }

    function index()
    {
        try
        {
            $this->data['userlist'] = array();
            $error_message = '';
            
            $access = ($this->role==1)?TRUE:check_time_of("add");
                
            if(!$access)
            {
               $this->service_message->set_flash_message('web_form_add_error'); 
               redirect('upload_type'); 
            }

            //set form rules
            $this->form_validation->set_rules('uploaddate', 'Date', 'required');
            if($this->role == 1)
            {
                $this->form_validation->set_rules('userlist', 'Select User', 'required');
                $this->data['userlist'] = $this->user_model->get_users(array("role !=" =>
                    1));
            }
            
            if ($this->form_validation->run() === FALSE)
                throw new Exception('validation_error');
                
            
            $upload_path = "././assets/excel/";


            //get user role
            $role = $this->role;
            $current_userId = get_current_user_id();
            
            if ($this->role == 1) 
            {
                $current_userId = $this->input->post('userlist');

                $role = $this->user_model->get_by_id($current_userId);

                if(!count($role))
                    throw new Exception('Invalid User ID.');
                        
                $role = $role['role'];
            } 
            
            
            //Check if directory exists or not for current user
            if (!file_exists("$upload_path/$current_userId") && !is_dir("$upload_path/$current_userId")) 
            {
                mkdir("$upload_path/$current_userId", 0755, true);

            }

            $upload_path .= $current_userId . "/";
            
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'xlsx|xls';

            //Initialize upload library
            $this->upload->initialize($config);
            
            $ins_data = array();

            $ins_data['date'] = $this->input->post('uploaddate');


            if (!$this->upload->do_upload('userexcel'))
                throw new Exception($this->upload->display_errors());
                
           
            $data['upload_data'] = $this->upload->data();

            $inputFileType = 'Excel2007';
            $inputFileName = $data['upload_data']['full_path'];
            $sheetname = 'Sheet1';
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setLoadSheetsOnly($sheetname);
            $objPHPExcel = $objReader->load($inputFileName);
            
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            
            if( !$this->process($sheetData, $role, $current_userId, $ins_data) )
                throw new Exception($this->error_message);
               
            $this->service_message->set_flash_message('file_upload_success');
             
        }
        catch(Exception $e)
        {
            $error_message = $e->getMessage();
            if( strcmp($error_message, 'validation_error') === 0 )
                $error_message = '';
            else
                $this->service_message->set_flash_message('custom_message_error', $error_message);
                
           
        }   
        
        $this->data['error_message'] = $error_message;
        redirect('upload_type');//$this->layout->view('upload_type');
    }



    function process( $sheetData = array(), $role = 1, $current_userId, $ins_data )
    {
        try
        {
            $unitno = isset($sheetData[7]['B'])?$sheetData[7]['B']:'';
            
            $temp = strtolower(substr($unitno, 0, 4));
                        
            if( strcmp($temp, 'unit') === 0 && $role != 3)
                throw new Exception('The uploaded template is incorrect!.');
                
            if( strcmp($temp, 'unit') !== 0 && $role == 3)
                throw new Exception('The uploaded template is incorrect!.');
                
            
            
            switch($role)
            {
                case '2':
                
                    $this->currentuser_upload_check($current_userId, $ins_data['date'],
                        "bulk_demand_details");

                    for ($i = 3; $i <= 5; $i++) 
                    {

                        if (!empty($sheetData[$i]['B']) && !empty($sheetData[$i]['H'])) 
                        {
                            $ins_data['source'] = strip_tags($sheetData[$i]['B']);
                            $ins_data['power'] = strip_tags($sheetData[$i]['H']);
                            $ins_data['user_id'] = $current_userId;
                            $ins_data['created_id'] = $current_userId;
                            $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));

                            $demand_insert_id = $this->bulk_demand_model->add($ins_data);

                        }
                    }
                    
                    if (isset($sheetData[8])) 
                    {
                        for ($i = 0; $i <= 23; $i++) 
                        {
                            $ins_dispatch_data['field_' . ($i + 1)] = $sheetData[8][chr(66 + $i)];
                        }

                        $ins_dispatch_data['date'] = $this->input->post('uploaddate');

                        $ins_dispatch_data['user_id'] = $current_userId;

                        $ins_dispatch_data['created_id'] = $current_userId;

                        $ins_dispatch_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));

                        $this->currentuser_upload_check($ins_dispatch_data['user_id'], $ins_dispatch_data['date'],
                            "bulk_dispath_period_data");

                        $insert_id = $this->bulk_dispath_model->add($ins_dispatch_data);
                    }
                
                
                break;
                
                case '3':
                
                        $ins_wholesaledemand_data['date']       = $this->input->post('uploaddate');
                        $ins_wholesaledemand_data['user_id']    = $current_userId;
                        $ins_wholesaledemand_data['created_id'] = $current_userId;

                        $ins_wholesaledemand_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));

                        $this->currentuser_upload_check($ins_wholesaledemand_data['user_id'], $ins_wholesaledemand_data['date'],
                            "wholesale_demand_details");

                        for ($i = 3; $i <= 5; $i++) 
                        {

                            if (!empty($sheetData[$i]['C']) && !empty($sheetData[$i]['L'])) {
                                $ins_wholesaledemand_data['source'] = $sheetData[$i]['C'];
                                $ins_wholesaledemand_data['power'] = $sheetData[$i]['L'];

                                $demand_insert_id = $this->wholesale_demand_model->add($ins_wholesaledemand_data);

                            }
                        }
                        $unitno = $sheetData[7]['C'];

                        if (isset($sheetData[9])) 
                        {
                            $this->currentuser_upload_check($current_userId, $this->input->post('uploaddate'),
                                "wholesale_dispath_period_data");

                            $ins_wholesaledispatch_data = $this->get_fields(9, $sheetData, $current_userId,
                                $unitno);
                        }
                        if (isset($sheetData[10])) 
                        {
                            $this->get_fields(10, $sheetData, $current_userId, $unitno);
                        }
                        $unitno = $sheetData[12]['C'];

                        if (isset($sheetData[14])) 
                        {

                            $this->get_fields(14, $sheetData, $current_userId, $unitno);

                        }
                        if (isset($sheetData[15])) 
                        {
                            $ins_wholesaledispatch_data = $this->get_fields(15, $sheetData, $current_userId,
                                $unitno);
                        }
                break;
            }
            return true;    
        }
        catch(Exception $e)
        {
            $this->error_message = $e->getMessage();
            return false;
        }
    }
 
    function list_view()
    {
        //$this->session->set_userdata("search_keywords", "");
        if ($this->input->is_ajax_request()) 
        {
            $this->session->set_userdata("search_keywords",$this->input->post('sSearch'));
            $this->uploads_view_model->getUploadsRecords();
        }
        
        $this->layout->view('uploads/list', $this->data);
    }

    function view($type = "", $user_id = "", $date = "", $action = "", $save="", $path="")
    {

        //validation
        if (!$type || !$user_id || !$date) {

            $this->service_message->set_flash_message('custom_message_error',
                'Error Occurred!!');
            redirect('upload/list_view');
        } else
            if (!is_admin() && get_logged_user_id() != $user_id) {

                $this->service_message->set_flash_message('custom_message_error',
                    'Accees Denied!!');
                redirect('upload/list_view');
            }

        switch ($type) {
            case 'bulk_demand':

                $demand_model = "bulk_demand_model";
                $dispatch_data_model = "bulk_dispath_model";
                $view = "bulk_demand_view";

                break;
            case 'whole_sale_demand':

                $demand_model = "wholesale_demand_model";
                $dispatch_data_model = "wholesale_dispath_model";
                $view = "whole_sale_demand_view";

                break;
            default:
                $this->service_message->set_flash_message('custom_message_error',
                    'Unknown upload type!!');
                redirect('upload/list_view');
                break;
        }

        $demand_details = $this->{$demand_model}->get_where(array('date' => $date,
                'user_id' => $user_id))->result_array();

        $dispatch_data = $this->{$dispatch_data_model}->get_where(array('date' => $date,
                'user_id' => $user_id))->result_array();


        if (empty($demand_details)) {
            $this->service_message->set_flash_message('custom_message_error',
                'Record Not found!!');
            redirect('upload/list_view');
        }
        //load address model
        $this->load->model('address_model');
        $address = $this->address_model->get_address($user_id);

        $this->data['demand_details'] = $demand_details;
        $this->data['dispatch_data'] = $dispatch_data;
        $this->data['address'] = $address;
        $this->data['user_details'] = $this->user_model->get_by_id($user_id);
        $this->data['type'] = $type;
        $this->data['save'] = $save;
        $this->data['path'] = $path;
        $this->data['date'] = $date;
        $this->data['user_id'] = $user_id;

        if ($action == "excel")
            $this->download();
        if($save!='yes')
            $this->layout->view("uploads/$view", $this->data);

    }

    function download()
    {
        $data = $this->data;
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
        for ($i = 1; $i <= 35; $i++) {
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
        $this->excel->getActiveSheet()->getRowDimension('3')->setRowHeight(24);
        $this->excel->getActiveSheet()->mergeCells('B3:P3');
        $this->excel->getActiveSheet()->setCellValue('B3',
            'DAILY AVAILABILITY DECLARATION SHEET FOR DISPATCH DAY');
        $this->excel->getActiveSheet()->setCellValue('Q3', 'DATE');
        $this->excel->getActiveSheet()->mergeCells('R3:U3');

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
        $this->excel->getActiveSheet()->mergeCells('R4:U4');
        $this->excel->getActiveSheet()->setCellValue('R4', 'DD/MM/YYYY');
        $this->excel->getActiveSheet()->getStyle("R4")->getFont()->setSize();
        $this->excel->getActiveSheet()->getStyle("R4")->getFont()->setItalic(true);

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
        $this->excel->getActiveSheet()->setCellValue('C8', ((isset($address['city'])) ?
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

        //10th row
        $this->excel->getActiveSheet()->getRowDimension('10')->setRowHeight(22.5);

        //11th row
        $this->excel->getActiveSheet()->getRowDimension('11')->setRowHeight(24);
        $this->excel->getActiveSheet()->setCellValue('B11', "Market Coordinator");
        $this->excel->getActiveSheet()->getStyle("B11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
            HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->mergeCells('B11:U11');
        $this->excel->getActiveSheet()->getStyle("B11")->getFont()->setBold(true);

        //12 - 15row
        $market_co_ord = array(
            "12" => array(
                'field1' => 'name',
                'label1' => 'Name',
                'field2' => 'role',
                'label2' => 'Designation'),
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
                'label2' => 'Date'));
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
            } else
                if ($market_co_ord[$i]['field2'] == "date") {
                    $field2 = (isset($dispatch_data[0]['date'])) ? str2USDate($dispatch_data[0]['date']) :
                        "";
                    $this->excel->getActiveSheet()->getStyle("L$i")->getNumberFormat()->
                        setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14);
                } else {
                    $field2 = $user_details[$market_co_ord[$i]['field2']];
                }

                $this->excel->getActiveSheet()->setCellValue("N$i", $field2);
        }

        //16th row
        $this->excel->getActiveSheet()->getRowDimension("16")->setRowHeight(19.5);
        $this->excel->getActiveSheet()->mergeCells("N16:Q16");
        $this->excel->getActiveSheet()->setCellValue("N16", "DD/MM/YYYY");
        $this->excel->getActiveSheet()->getStyle("N16")->getFont()->setSize();
        $this->excel->getActiveSheet()->getStyle("N16")->getFont()->setItalic(true);

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

        switch ($type) {

            case 'whole_sale_demand':
                //17th row
                $this->excel->getActiveSheet()->getRowDimension("17")->setRowHeight(29.25);
                $this->excel->getActiveSheet()->mergeCells("B17:K17");
                $this->excel->getActiveSheet()->setCellValue("B17",
                    "Recipient of Power Supply and Amount of Power (MW)");
                $this->excel->getActiveSheet()->getStyle("B17")->getFont()->setBold(true);


                //18th row
                $this->excel->getActiveSheet()->getRowDimension("18")->setRowHeight(29.25);
                $this->excel->getActiveSheet()->setCellValue("B18", "i");
                $this->excel->getActiveSheet()->getStyle("B18")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->mergeCells("C18:U18");
                $this->excel->getActiveSheet()->setCellValue("C18", ((isset($demand_details[0]['source'])) ?
                    trim($demand_details[0]['source'], '_') : "") . ((isset($demand_details[0]['power'])) ?
                    " - " . trim($demand_details[0]['power'], '_') : ""));


                //19th row
                $this->excel->getActiveSheet()->getRowDimension("19")->setRowHeight(29.25);
                $this->excel->getActiveSheet()->setCellValue("B19", "ii");
                $this->excel->getActiveSheet()->getStyle("B19")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->mergeCells("C19:U19");
                $this->excel->getActiveSheet()->setCellValue("C19", ((isset($demand_details[1]['source'])) ?
                    trim($demand_details[1]['source'], '_') : "") . ((isset($demand_details[1]['power'])) ?
                    " - " . trim($demand_details[1]['power'], '_') : ""));

                //20th row
                $this->excel->getActiveSheet()->getRowDimension("20")->setRowHeight(29.25);
                $this->excel->getActiveSheet()->setCellValue("B20", "iii");
                $this->excel->getActiveSheet()->getStyle("B20")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->mergeCells("C20:U20");
                $this->excel->getActiveSheet()->setCellValue("C20", ((isset($demand_details[2]['source'])) ?
                    trim($demand_details[2]['source'], '_') : "") . ((isset($demand_details[2]['power'])) ?
                    " - " . trim($demand_details[2]['power'], '_') : ""));

                //21th row
                $this->excel->getActiveSheet()->getRowDimension("21")->setRowHeight(29.25);
                $this->excel->getActiveSheet()->mergeCells("C21:Z21");
                $this->excel->getActiveSheet()->setCellValue("C21", "Generation Details ");
                $this->excel->getActiveSheet()->getStyle("C21")->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle("C21")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_CENTER);

                //generation details
                $i = 22;

                for ($k = 0; $k < count($dispatch_data); $k++) {

                    $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);
                    $this->excel->getActiveSheet()->setCellValue("B$i", "Unit No.:");
                    $this->excel->getActiveSheet()->setCellValue("C$i", ((isset($dispatch_data[$k]['unit_no'])) ?
                        $dispatch_data[$k]['unit_no'] : ""));
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
                    $i = $i + 1;
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
                    $i = $i + 1;
                    $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);
                    $this->excel->getActiveSheet()->setCellValue("B$i", "MW:");
                    $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                    $this->excel->getActiveSheet()->getStyle("B$i")->getBorders()->getAllBorders()->
                        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


                    $j = 1;
                    foreach ($column as $val) {
                        $this->excel->getActiveSheet()->setCellValue($val . "$i", ((isset($dispatch_data[$k]['field_' .
                            $j])) ? $dispatch_data[$k]['field_' . $j] : ""));
                        $this->excel->getActiveSheet()->getStyle($val . "$i")->getBorders()->
                            getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                        $j = $j + 1;
                    }

                    $k = $k + 1;
                    //mVAr row
                    $i = $i + 1;
                    $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);
                    $this->excel->getActiveSheet()->setCellValue("B$i", "MVAr:");
                    $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                    $this->excel->getActiveSheet()->getStyle("B$i")->getBorders()->getAllBorders()->
                        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    $j = 1;
                    foreach ($column as $val) {
                        $this->excel->getActiveSheet()->setCellValue($val . "$i", ((isset($dispatch_data[$k]['field_' .
                            $j])) ? $dispatch_data[$k]['field_' . $j] : ""));
                        $this->excel->getActiveSheet()->getStyle($val . "$i")->getBorders()->
                            getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                        $j = $j + 1;
                    }

                    $i = $i + 1;
                    $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);

                    $i = $i + 1;

                }
                break;
            case 'bulk_demand':


                //17th row
                $this->excel->getActiveSheet()->getRowDimension("17")->setRowHeight(24);
                $this->excel->getActiveSheet()->mergeCells("B17:U17");
                $this->excel->getActiveSheet()->setCellValue("B17", "Demand Details");
                $this->excel->getActiveSheet()->getStyle("B17")->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle("B17")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_CENTER);

                //18th row
                $this->excel->getActiveSheet()->getRowDimension("18")->setRowHeight(24);
                $this->excel->getActiveSheet()->setCellValue("B18", "Delivery Point:");
                $this->excel->getActiveSheet()->setCellValue("C18", "");
                $this->excel->getActiveSheet()->getStyle("C18")->getBorders()->getAllBorders()->
                    setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                //19th row
                $this->excel->getActiveSheet()->getRowDimension("19")->setRowHeight(24);
                $this->excel->getActiveSheet()->setCellValue("B19",
                    "Source (s) of Power Supply ");
                $this->excel->getActiveSheet()->mergeCells("B19:H19");
                $this->excel->getActiveSheet()->setCellValue("J19", "Amount of Power (MW) ");
                $this->excel->getActiveSheet()->mergeCells("J19:R19");

                //20th row
                $this->excel->getActiveSheet()->getRowDimension("20")->setRowHeight(29.25);
                $this->excel->getActiveSheet()->setCellValue("B20", "i");
                $this->excel->getActiveSheet()->getStyle("B20")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->mergeCells("C20:H20");
                $this->excel->getActiveSheet()->setCellValue("C20", ((isset($demand_details[0]['source'])) ?
                    trim($demand_details[0]['source'], '_') : ""));
                $this->excel->getActiveSheet()->mergeCells("J20:R20");
                $this->excel->getActiveSheet()->setCellValue("J20", ((isset($demand_details[0]['power'])) ?
                    trim($demand_details[0]['power'], '_') : ""));


                //21st row
                $this->excel->getActiveSheet()->getRowDimension("21")->setRowHeight(29.25);
                $this->excel->getActiveSheet()->setCellValue("B21", "ii");
                $this->excel->getActiveSheet()->getStyle("B21")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->mergeCells("C21:H21");
                $this->excel->getActiveSheet()->setCellValue("C21", ((isset($demand_details[1]['source'])) ?
                    trim($demand_details[1]['source'], '_') : ""));
                $this->excel->getActiveSheet()->mergeCells("J21:R21");
                $this->excel->getActiveSheet()->setCellValue("J21", ((isset($demand_details[1]['power'])) ?
                    trim($demand_details[1]['power'], '_') : ""));


                //22nd row
                $this->excel->getActiveSheet()->getRowDimension("22")->setRowHeight(29.25);
                $this->excel->getActiveSheet()->setCellValue("B22", "iii");
                $this->excel->getActiveSheet()->getStyle("B22")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::
                    HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->mergeCells("C22:H22");
                $this->excel->getActiveSheet()->setCellValue("C22", ((isset($demand_details[2]['source'])) ?
                    trim($demand_details[2]['source'], '_') : ""));
                $this->excel->getActiveSheet()->mergeCells("J22:R22");
                $this->excel->getActiveSheet()->setCellValue("J22", ((isset($demand_details[2]['power'])) ?
                    trim($demand_details[2]['power'], '_') : ""));

                //23rd row
                $this->excel->getActiveSheet()->getRowDimension("23")->setRowHeight(29.25);

                //24th row
                //header column
                $i = 24;
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
                $i = $i + 1;
                $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(22.5);
                $this->excel->getActiveSheet()->setCellValue("B$i", "Demand (MW):");
                $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle("B$i")->getBorders()->getAllBorders()->
                    setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


                $j = 1;
                $k = 0;
                foreach ($column as $val) {
                    $this->excel->getActiveSheet()->setCellValue($val . "$i", ((isset($dispatch_data[$k]['field_' .
                        $j])) ? $dispatch_data[$k]['field_' . $j] : ""));
                    $this->excel->getActiveSheet()->getStyle($val . "$i")->getBorders()->
                        getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $j = $j + 1;
                }

                $i = $i + 1;
                break;
        }

        //comments and signature
        $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(27.75);
        $this->excel->getActiveSheet()->setCellValue("B$i", "Comments:");
        $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
        $this->excel->getActiveSheet()->mergeCells("C$i:Z$i");

        $i = $i + 1;
        $this->excel->getActiveSheet()->getRowDimension("$i")->setRowHeight(27.75);
        $this->excel->getActiveSheet()->setCellValue("B$i", "Signature:");
        $this->excel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
        $this->excel->getActiveSheet()->mergeCells("C$i:Z$i");

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        
        if($save=='yes')
        {
            
            $objWriter->save($path."/".$type."-".$date.".xlsx");
            
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
    
    function currentuser_upload_check($user_id = '', $date = '', $table = '')
    {
        $check_userupload = $this->bulk_demand_model->check_upload($user_id, $date, $table);

        if (count($check_userupload) > 0) {
            $where = array("user_id" => $user_id, "date" => $date);
            $deletedemand = $this->bulk_demand_model->deleteexcel($where, $table);
        }
    }

    function get_fields($index, $sheetData, $current_userId, $unitno)
    {
        $ins_wholesaledispatch_data['date'] = $this->input->post('uploaddate');
        $ins_wholesaledispatch_data['user_id'] = $current_userId;
        $ins_wholesaledispatch_data['created_id'] = get_current_user_id();
        $ins_wholesaledispatch_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));
        $ins_wholesaledispatch_data['type'] = $sheetData[$index]['B'];
        $ins_wholesaledispatch_data['unit_no'] = $unitno;
        for ($i = 0; $i <= 23; $i++) {
            $ins_wholesaledispatch_data['field_' . ($i + 1)] = $sheetData[$index][chr(67 + $i)];
        }

        $insert_id = $this->wholesale_dispath_model->add($ins_wholesaledispatch_data);

    }
    
    public function download_all()
    { 
        $this->load->library('zip');
        
        $this->load->helper(array('download','path'));
        
        $searchkeywords = $this->session->userdata('search_keywords');
            
        $this->zip->clear_data();
            
        $role = get_user_role();
        
        $downloadall = $this->uploads_view_model->download_all_excel($searchkeywords);
      
        $totalrows   = count($downloadall);
        
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
         $this->view($type=$downloadall[$i]['type'], $user_id=$downloadall[$i]['user_id'], $date=$downloadall[$i]['date'], $action='excel', $save='yes', $upload_path);
        }
       
        $this->zip->read_dir($upload_path."/", FALSE); 
        
        $this->zip->archive($upload_path."/"."download.zip");
        
        header('Location:'.site_url('assets/excel/download/download.zip'));
        
        die;
        
    }
    public function download_selected_records()
    { 
        
        $this->load->library('zip');
        
        $this->load->helper(array('download','path'));
        
       // $searchkeywords = $this->session->userdata('search_keywords');
            
        $this->zip->clear_data();
            
        $role = get_user_role();
        
        $downloadall = $_POST['download'];
       
        $totalrows   = count($downloadall);
        
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
        
         $this->view($type=$user_data[2], $user_id=$user_data[0], $date=$user_data[1], $action='excel', $save='yes', $upload_path);
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
?>