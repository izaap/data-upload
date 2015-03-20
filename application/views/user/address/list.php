
<div class="container">
<?php if ($message = $this->service_message->render()) :?>
		<?php echo $message;?>
    <?php endif; ?>
<?php if(get_user_role() === 1):?>    
<h1 style="float:right;">
    <a class="btn btn-primary " href="<?php echo site_url('address');?>/add_bulk_user"><i class="icon-plus icon-white"></i>Add Bulk Customer</a> 
    <a class="btn btn-primary " href="<?php echo site_url('address');?>/add_wholesale"><i class="icon-plus icon-white"></i>Add Wholesale Supplier</a> 

</h1>
<?php endif;?>
    
    
<?php if(get_user_role() === 1){?>
    <div class="row">
        <div class="span12" id="data-grid">
            
                <table id="shops" class="table table-striped table-bordered">
                    <thead>
                        <tr>    
                            <th>Organization</th>
                            <th>Type of participant</th>
                            <th>Mailing Address</th>                            
                            <th>Location</th>
                            <th>City</th>
                            <th>Connection/ Withdrawal Points</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            
        </div>
    </div>

<?php }else{ if(isset($useraddress)){  //print_r($useraddress);?>
<div class="container-fluid span6">
	<div class="row-fluid">
        <div class="span2" >
		    
        </div>
        
        <div class="span8">
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="address">
                <tr style="margin-bottom: 2px;;">
                    <td><h5>Name  </h5></td>
                    <td><?= (isset($useraddress['organization']))?$useraddress['organization']:"" ?></td>
                </tr>
                <tr style="margin-bottom: 2px;;">
                    <td><h5>Mailing Address  </h5></td>
                    <td><?= (isset($useraddress['mailing_address']))?$useraddress['mailing_address']:"" ?></td>
                </tr>
               
                <tr style="margin-bottom: 2px;;">
                    <td><h5>Location  </h5></td>
                    <td><?= (isset($useraddress['location']))?$useraddress['location']:"" ?></td>
                </tr>
                <tr style="margin-bottom: 2px;;">
                    <td><h5>City  </h5></td>
                    <td><?= (isset($useraddress['city']))?$useraddress['city']:"" ?></td>
                </tr>
                <tr style="margin-bottom: 2px;;">
                    <td><h5>Telephone  </h5></td>
                    <td><?= (isset($useraddress['telephone']))?$useraddress['telephone']:"" ?></td>
                </tr>
                <tr style="margin-bottom: 2px;;">
                    <td><h5> <?php echo ($useraddress['role_id']==2)?'Withdrawal':'Connection';?> Point  </h5></td>
                    <td><?= (isset($useraddress['deliverypoint']))?$useraddress['deliverypoint']:"" ?></td>
                </tr>
            </table>
        </div>
        

        <div class="span2">
            <div class="btn-group">
                 <?php if($useraddress['role_id']==3){ ?>     
                                     
                    <a class="btn dropdown-toggle btn-info" href="<?php echo ($useraddress['id']!=0 && $useraddress['id']!="")?"#plant_myModal":"#No_address_alert"; ?>" role="button" data-toggle="modal"><i class="icon-edit"></i> Plant Details</a>
                    
                    <br/><br/><a class="btn dropdown-toggle btn-info" href="<?php echo ($useraddress['id']!=0 && $useraddress['id']!="")?"#supply_myModal1":"#No_address_alert"; ?>" role="button" data-toggle="modal"><i class="icon-edit"></i>Supply Details</a>

                <?php }else{ ?>    

                    <a class="btn dropdown-toggle btn-info" href="<?php echo ($useraddress['id']!=0 && $useraddress['id']!="")?"#myModal1":"#No_address_alert"; ?>" role="button" data-toggle="modal"><i class="icon-edit"></i> Demand Details</a>
                
                <?php } ?>
            </div>
        </div>

        <!-- get supply && demand details -->

        <?php 
            $getdemand_details = "";
            $getsupply_details = "";
            if(!empty($useraddress['id'])){
                $getdemand_details = $this->demand_model->get_address_demands(array('address_id' => $useraddress['id']));  
                $getsupply_details = $this->supply_model->get_address_supply(array('s.address_id' => $useraddress['id']));  
            }  ?>

        <!-- Supply Details -->    
        <div id="myModal1" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 id="myModalLabel"><?php if($useraddress['role_id']==3){ echo "Supply"; }else{ echo "Demand"; } ?> Details</h3>
            </div>
          
            <div class="modal-body" style="overflow:auto !important;">  
              <form id="demand_form" name="demand_form" method="POST"> 

                <div id="Demandsgroup">
                    <?php if(!empty($getdemand_details)){ ?>

                    <div class="control-group" style="padding:10px 0 25px 0;">
                        <div class="controls" style="float:left;font-weight:bold;">Source of power supply Details</div>
                        <div class="controls" style="float:right;font-weight:bold;">Contracted Power (MW)</div>
                    </div>
                    

                <?php   foreach($getdemand_details as $demands){ ?>

                            <div class="control-group" id="TextBoxDiv">
                                <div class="controls span3" style="float:left;">
                                    <?php echo $demands['source'];?>                                
                                </div>
                                <div class="controls span3" style="float:right;">
                                    <?php echo $demands['power'];?>                                
                                </div>
                            </div><br/><br/>

                    <?php }

                     } else { ?>

                        <div class="control-group" id="TextBoxDiv" style="color:red;font-weight:bold;">
                            No demand records.
                        </div>

                    <?php } ?>      

                </div>   

              </form>
            </div>

            <div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
              <!--<button class="btn btn-primary" id="InfroTextSubmit">Save</button>-->
            </div>
        </div>

        <!-- Supply Details -->
        <div id="supply_myModal1" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 id="myModalLabel">Supply Details</h3>
            </div>
          
            <div class="modal-body" style="overflow:auto !important;">  
              <form id="demand_form" name="demand_form" method="POST"> 

                <div id="Demandsgroup">
                    <?php if(!empty($getsupply_details)){ ?>

                    <div class="control-group" style="padding:10px 0 25px 0;">
                        <div class="controls" style="float:left;font-weight:bold;">plant</div>
                        <div class="controls" style="float:left;font-weight:bold; margin-left: 95px;">Recipient of Power Details</div>
                        <div class="controls" style="float:right;font-weight:bold;">Contracted Power (MW)</div>
                    </div>
                    

                <?php   foreach($getsupply_details as $demands){ ?>

                            <div class="control-group" id="TextBoxDiv">

                                <div class="controls span3" style="float:left;">
                                    <?php echo $demands['plant_name'];?>                                
                                </div>

                                <div class="controls span3" style="float:left;">
                                    <?php echo $demands['source'];?>                                
                                </div>
                                <div class="controls span3" style="float:right;">
                                    <?php echo $demands['power'];?>                                
                                </div>
                            </div><br/><br/>

                    <?php }

                     } else { ?>

                        <div class="control-group" id="TextBoxDiv" style="color:red;font-weight:bold;">
                            No supply records.
                        </div>

                    <?php } ?>      

                </div>   

              </form>
            </div>

            <div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
              <!--<button class="btn btn-primary" id="InfroTextSubmit">Save</button>-->
            </div>
        </div>


    <div id="plant_myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel">Plant Details</h3>
        </div>
      
        <div class="modal-body" style="overflow:auto !important;">     

            <div id="PlantsGroup">  

                <?php 
                $plant_data="";
                if(!empty($useraddress['id'])){
                    //$where = array('address_id' => $useraddress['id']);
                    $plant_data = $this->plant_model->get_plants($useraddress['id']);
                }
                    
                if(!empty($plant_data)){

                    $i=1;
                    foreach($plant_data as $plants){ ?>

                        <div id="TextBoxDiv"  class="login_box" style="float: none; display: inline-block;">
                            
                            <div class="control-group span4  m_top_10 m_left_10">
                                <b>Plant Name:</b> <?php echo $plants['plant_name'];?>                
                            </div>
                            <div class="control-group span4  m_top_10">
                                <b>Location:</b> <?php echo $plants['location'];?>                    
                            </div>
                            <div class="control-group span4">
                                <b>Telephone:</b> <?php echo $plants['telephone'];?>                    
                            </div>
                            <div class="control-group span4">
                                <b>No.of Units:</b> <?php echo $plants['no_units'];?>                  
                            </div>
                        </div>  
              <?php $i++; 
                    } 

                }else{  ?>  

                    <div  class="span9 " style="float: none; display: inline-block;color:red;font-weight:bold;">
                          No Plant records.  
                    </div>  
              <?php } ?>        

            </div>  

        </div>
        <div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
              <!--<button class="btn btn-primary" id="InfroTextSubmit">Save</button>-->
        </div>
    </div>    


        <div id="No_address_alert" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 id="myModalLabel">Information</h3>
            </div>
          
            <div class="modal-body" > 
                Sorry, Your address is empty, please contact Administrator!!
            </div>
            <div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">OK</button>
            </div>
        </div>
        
</div>

</div>
<?php } } ?>

<script type="text/javascript">


    var options = {
            "aoColumns": [
					        { "sWidth": "12%" },
                            { "sWidth": "12%" },
                            { "sWidth": "13%" },
					        { "sWidth": "10%" },
					        { "sWidth": "10%" },
                            { "sWidth": "15%" },
                            { "sWidth": "21%", "sClass": "left", "bSortable": false },
                         ],
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod":"POST",
            "sAjaxSource": base_url+"address/index",
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

        var table = $('#shops').DataTable( options );
    } );
</script>    


<script>

function no_address_alert(){
    BootstrapDialog.show({
        title: 'Information',
        message: 'Sorry, Please add your address and proceed to create Demands!'
    });
}

</script>       

        