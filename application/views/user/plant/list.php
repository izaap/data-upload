<div class="container">
<?php if ($message = $this->service_message->render()) :?>
		<?php echo $message;?>
    <?php endif; ?>
<?php if(get_user_role() === 1):?>    
<h1>
    <a class="btn btn-primary pull-right" href="javascript:;" onclick="add_plant('add',0)"><i class="icon-plus icon-white"></i>Add New plant</a> <br/>
</h1>
<?php endif;?>
    

<div class="row">
    <div class="span12" id="data-grid">
        
            <table id="plants" class="table table-striped table-bordered">
                <thead>
                    <tr>    
                        <th>Plant Name</th>
                        <th>Location</th>
                        <th>Telephone</th>                            
                        <th>No.of units</th>    
                        <th>Action</th>                        
                    </tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
        
    </div>
</div>

<div id="plant_detail_model" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Plant Details</h3>
    </div>
    <div id="plant_datails_form">
        
    </div>
</div>

<script type="text/javascript">


    var options = {
            "aoColumns": [
                            { "sWidth": "15%" },
                            { "sWidth": "15%" },
                            { "sWidth": "13%" },
                            { "sWidth": "10%" },
                            { "sWidth": "21%", "sClass": "left", "bSortable": false },
                         ],
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod":"POST",
            "sAjaxSource": base_url+"plant/index",
            "sPaginationType": "full_numbers"
           /* "aoColumnDefs": [
            {
            "bSortable": false,
            "aTargets":  [ -1 ]
            }
            ]*/
            };
    <?php if(get_user_role() !== 1): ?>
        options.bFilter = false;
        options.bLengthChange = false;
        options.bPaginate = false;
        options.bInfo = false;
    <?php endif; ?>

    $(document).ready( function () {

        var table = $('#plants').DataTable( options );
    } );


    function add_plant(action_type,edit_id){

    data = {};
    if(action_type == 'process')
    data = $("#plant_form").serialize();

    $.ajax( {
        url:base_url+'plant/add/'+edit_id,
        type: "POST",
        data: data,
        dataType:"json",
        success : function(data){

          if(data.status == 'warning')
          {
            $("#plant_datails_form").html(data.content);
            $("#plant_detail_model").modal();
          }
          else if(data.status == 'success' && action_type == 'process')
          {            
            alert(data.message);
            
            $("#plant_detail_model").modal('hide');            
            location.reload(); 
          } 
          else
          {
            alert(data.message);
          }

        },
        error : function(data) {
            alert("Please add atleast one plant!");
        }
    });        

    }

</script>    