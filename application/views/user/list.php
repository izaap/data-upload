<div class="container">

<?php if ($message = $this->service_message->render()) :?>
		<?php echo $message;?>
    <?php endif; ?>
    
    <?php if(get_user_role() === 1):?>
    <h1>Market Co-ordinators
        <a class="btn btn-primary pull-right" href="<?php echo site_url('user');?>/add"><i class="icon-plus icon-white"></i>Add a New Market Co-ordinator</a><br/>
    </h1>
    <?php endif; ?>
    

    
<?php if(get_user_role() === 1){?>
    <div class="row">
        <div class="span12" id="data-grid">
            
                <table id="users" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Login ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type of Participant</th>
                            <th>Company Name</th>
                            <th>Designation</th>
                            <th>Phone</th>
                            <th>Modify</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            
        </div>
    </div>

<?php }else{ foreach($userdata as $ukey=>$uvalue){ //print_r($userdata);?>
<div class="container-fluid span6">
	<div class="row-fluid">
        <div class="span2" >
		    
        </div>
        
        <div class="span8">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="address">
        <tr style="margin-bottom: 2px;;">
            <td><h5>Name  </h5></td><td><?php echo $uvalue['name']; ?></td>
        </tr>
          <tr>  
            <td><h5>Cell Phone  </h5></td>
            <td><?php echo $uvalue['cellphone']; ?></td>
          </tr>
          <tr>
                <td><h5>Other Phone </h5></td>
                <td><?php echo $uvalue['otherphone']; ?></td>
          </tr>
            <tr>
                <td> <h5>Fax </h5></td>
                <td><?php echo $uvalue['fax']; ?></td>
            </tr>
            <tr>
                <td><h5>Email</h5></td>
                <td><?php echo $uvalue['email']; ?></td>
            </tr>
            <tr>
                <td> <h5>Type of Participant </h5></td>
                <td><?php echo $uvalue['role_name']; ?></td>
            </tr>

            <tr>
                <td> <h5>Designation </h5></td>
                <td><?php echo $uvalue['designation']; ?></td>
            </tr>
            <?php if($uvalue['role']==3){ ?>
            <tr>
                <td> <h5>Assigned Plant</h5></td>
                <td>
                <?php 
                   echo (!empty($uvalue['plant']))? get_plant_name($uvalue['plant']):'';

                ?></td>
            </tr>
            <?php } ?>
            
        </table>
        </div>

</div>

</div>
<?php }}?>

<script type="text/javascript">


    var options = {
            "aoColumns": [
					        { "sWidth": "10%" },
                            { "sWidth": "10%" },
					        { "sWidth": "15%" },
                            { "sWidth": "15%" },
					        { "sWidth": "15%" },
					        { "sWidth": "10%" },
                            { "sWidth": "10%" },
                            { "sWidth": "25%",  "bSortable": false },
                         ],
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod":"POST",
            "sAjaxSource": base_url+"user/index",
            "sPaginationType": "full_numbers",
            "aoColumnDefs": [
            {
            "bSortable": false,
            "aTargets":  [ -1 ]
            }
            ]
            };
    <?php if(get_user_role() !== 1): ?>
        options.bFilter = false;
        options.bLengthChange = false;
        options.bPaginate = false;
        options.bInfo = false;
    <?php endif; ?>

    $(document).ready( function () {

        var table = $('#users').DataTable( options );
    } );
</script>           

       