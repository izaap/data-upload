<link href="<?php echo base_url();?>assets/css/jquery.ui.css" media="screen" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.filter.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.datepicker.js"></script>
<?php $role=get_user_role(); ?>

<?php if($role != 1) { ?>
<style type="text/css">
    span #uploads_range_from_0
    {
        width:150px !important;
        
    }
    span #uploads_range_to_0
    {
        width:150px !important;
    }
</style>
<?php } ?>

<div class="container">
    <?php if ($message = $this->service_message->render()) :?>
		<?php echo $message;?>
    <?php endif; ?>
   
    <h1>Uploads


    <?php
    $current_user_id = get_current_user_id();
    if($role == 1)
    {
        $action_url = site_url('upload_type');
    }
    else
    {
        $action_url = site_url('upload_form/'.$current_user_id);
    }
    ?>
     
    <button class="m_left_10 btn btn-primary pull-right" id="download_all">DOWNLOAD ALL</button>
    
    <a class="btn btn-primary pull-right" href="<?php echo $action_url; ?>">
    <i class="icon-plus icon-white"></i> Upload Data</a>

    </h1> 
    
    <div class="row">
        <div class="span12" id="data-grid">

            <form id="dwal" method="POST" id="downldall" name="downldall" action="<?php echo site_url('download_all'); ?>">
               
                <table id="uploads" class="table table-striped table-bordered">                
                    <thead>
                        <tr>                            
                            <th id="dispatchdate"><div id="date_range"> </div></th>
                            <th>Name</th>
                            <th>Company Name</th> <!--Email-->
                            <th>Type of Participant</th>
                            <th>Plant Name(s)</th> <!--for previously phone-->
                            <th>Submit Date</th>
                            <th>Modify</th>
                            <th>Select All&nbsp;<input type="checkbox" id="selecctall"/></th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>                
            </form>
        </div>
    </div>

<script type="text/javascript">

    var options = {
        
            "aoColumns": [
					        { "sWidth": "19%"},
                            { "sWidth": "8%"<?php echo (! is_admin())?' ,"bVisible":false, "bSearchable":false':'';?> },
					        { "sWidth": "10%"<?php echo (! is_admin())?' ,"bVisible":false, "bSearchable":false':'';?> },
					        { "sWidth": "10%"<?php echo (! is_admin())?' ,"bVisible":false, "bSearchable":false':'';?> },
					        { "sWidth": "9%"<?php echo (! is_admin())?' ,"bVisible":false, "bSearchable":false':'';?> },
                            { "sWidth": "7%"},
                            { "sWidth": "11%", "sClass": "center", "bSortable": false },
                            { "sWidth": "7%" , "sClass": "center"}
                         ],
   
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod":"POST",
            "sAjaxSource": base_url+"upload/list_view",
            "sPaginationType": "full_numbers",
            "aoColumnDefs": [
            {
            "bSortable": false,
            "aTargets":  [ -1 ]
            },
            ],
            "bSortCellsTop": true,
            "aaSorting": [[ 0, "desc" ]],
            };
    $(document).ready( function () {
      
      $.datepicker.regional[""].dateFormat = 'dd-mm-yy';
      $.datepicker.setDefaults($.datepicker.regional['']);
              
        var table = $('#uploads').dataTable(options).columnFilter({ 
                    sPlaceHolder: "head:before",
					aoColumns: [{ type: "date-range" ,sDefaultContent:"Dispatch Date"},
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null      							      ]
		});
    });
    
 $("#download_all").click(function(){

    document.downldall.submit();

 });  

 $(document).ready(function() {
    $('#selecctall').click(function(event) {  //on click
        if(this.checked) { // check select status
            $('.checkdownloads').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkdownloads"              
            });
        }else{
            $('.checkdownloads').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkdownloads"                      
            });        
        }
    });
   
}); 
    
</script>           

        