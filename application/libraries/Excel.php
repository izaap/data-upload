<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 *  ======================================= 
 *  Author     : Muhammad Surya Ikhsanudin 
 *  License    : Protected 
 *  Email      : mutofiyah@gmail.com 
 *   
 *  Dilarang merubah, mengganti dan mendistribusikan 
 *  ulang tanpa sepengetahuan Author 
 *  ======================================= 
 */  
require_once "PHPExcel.php"; 
 
class Excel extends PHPExcel {

	public $head_columns = array();

    public function __construct() 
    { 
        parent::__construct(); 
    }

    function set_title( $title = '', $row_no = 1, $tpl = 1 )
    {
    	switch ($tpl) 
    	{
    		case 1:
    			$this->setActiveSheetIndex(0);
	            $this->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	            $this->getDefaultStyle()->getFont()->setName('Arial');
	            $this->getDefaultStyle()->getFont()->setSize(14);
	            $this->getActiveSheet()->setCellValue('A1', $title);

	            //change the font size
	            $this->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
	            //make the font become bold
	            $this->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);	            
	            //merge cell A1 until D1
	            $this->getActiveSheet()->mergeCells(chr(65).$row_no.':'.chr(65+count($this->head_columns)).$row_no);

    			break;
    		
    		default:
    			# code...
    			break;
    	}
    }

    function set_header( $headers = array(), $row_no = 2, $tpl = 1 )
    {
    	switch ($tpl) 
    	{
    		case 1:
    			$this->getDefaultStyle()->getFont()->setName('Calibri');
            	$this->getDefaultStyle()->getFont()->setSize(11);
            	
            	foreach ($headers as $k => $value) 
            	{
            		$this->getActiveSheet()->setCellValue(chr(65+$k).$row_no, $value);
            		$this->getActiveSheet()->getStyle( chr(65+$k).$row_no )->getFont()->setBold(true);
            	}
            	
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    }

    function set_data( $data = array(), $row_no = 3, $tpl = 1 )
    {
    	switch ($tpl) 
    	{
    		case 1:
    			foreach ($data as $k => $value) 
            	{
            		$this->getActiveSheet()->setCellValue(chr(65+$k).$row_no, $value);
            	}
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    }

    function set_footer( $message = '', $row_no = 2, $tpl = 1  )
    {
    	switch ($tpl) 
    	{
    		case 1:
    			$this->getActiveSheet()->setCellValue('A'.$row_no, $message);
    			//change the font size
		        $this->getActiveSheet()->getStyle('A'.$row_no)->getFont()->setSize(14);
		        //make the font become bold
		        $this->getActiveSheet()->getStyle('A'.$row_no)->getFont()->setBold(true);
		        
		        //merge cell A1 until D1
		        $this->getActiveSheet()->mergeCells(chr(65).$row_no.':'.chr(65+count($this->head_columns)).$row_no);
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    }

    function generate( $filename = 'report.xls', $action = 'download' )
    {
    	$objWriter = PHPExcel_IOFactory::createWriter($this, 'Excel5');  
        if( $action == 'download' )
        {
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache
            $objWriter->save('php://output');
        }
        else
        {
            $path = BASEPATH_HTDOCS.'images/temp/'.'report'.time().'.xls';
            
            $objWriter->save($path);
            return $path;
            exit;
        }
    }

}