<div class="container ">
<h1><a class="btn btn-small pull_right" href="<?php echo site_url('address');?>" ><i class="icon-backward"></i> Back to list</a>	
</h1>

<?php //echo"<pre>"; print_r($plants);?>

<?php if($type==3){ ?>

  <a href="<?=($form_data['id'])?'#supply_myModal':'#No_supply_alert'?>" role="button" class="btn btn-success btn-large" data-toggle="modal">Supply Details </a>

 <?php }elseif($type==2){ ?>

  <a href="<?=($form_data['id'])?'#demand_myModal':'#No_demand_alert'?>" role="button" class="btn btn-success btn-large" data-toggle="modal">Demand Details </a>

 <?php } ?>	



<div class="container m_top_30">
	<?php if ($message = $this->service_message->render()) :?>
		<?php echo $message;?>
    <?php endif; ?>
    
	<?php if (validation_errors()) :?>
		<div class="alert m_top_20 m_bot_0" style="width: 300px; margin-left:380px;">
	    	<button type="button" class="close" data-dismiss="alert">&times;</button>
	    	<strong>Warning!</strong>  <?php echo validation_errors();?>
	    </div>
    <?php endif; ?>
    
	<div class="row" style="text-align: center;">
		
		<h1> <?=($form_data['id'])?("Edit"):("Add")?> <?=($type==3)?"Wholesale Supplier":("Bulk Customer")?> (Company Details) </h1>

		<div class="span6 login_box m_auto" style="margin: 0px auto; float: none; display: inline-block;">
		<form id="Useraddress" class="form-horizontal" name="Useraddress" method="POST" action="">		
					
            <div class="control-group">
            	<label class="control-label">Name of organization: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"> <i class="icon_username"></i>
					</span>
					<input type="text" value="<?php echo set_value('organization', $form_data['organization']);?>"  class="span3" name="organization">		
                </div>
			</div>
			<!--
			<div class="control-group">
				<label class="control-label">Type of Participant: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_username"></i> </span>
                        <select name="user_role" id="user_role" class="span3" onchange="wholsaleplant(this.value)" <?php if(!empty($form_data['id'])){ echo"disabled"; } ?> >
                        	<option value="">Select type</option>
                        	<?php if (!empty($roles)) {
							    foreach ($roles as $rkeys => $rvalues) { 
							    	if($rvalues['id'] != 1){ ?>							     

							        <option <?php if (isset($rvalues) && isset($form_data['type_of_participant'])) {
							            if ($rvalues['id'] == $form_data['type_of_participant']) {
							                echo "selected='selected'";
							            }
									        } ?> value="<?php echo
									$rvalues['id']; ?>"><?php echo
									$rvalues['role_name']; ?></option>

						  <?php     }
								}

							} ?>
                        
                        </select>
				</div>
				<?php if(!empty($form_data['id'])){ ?>

				<?php } ?>
			</div> -->
         					<input type="hidden" value="<?php echo $type;?>"  id="user_role" name="user_role">		
        
            <div class="control-group">
            	<label class="control-label">Mailing Address: </label>
				<div class="controls btn-group input-prepend">
					
                    <textarea style="width:280px;" class="span3" name="mailing_address"  value="<?php echo set_value('mailing_address', $form_data['mailing_address']);?>"><?php if(isset($form_data['mailing_address'])){echo $form_data['mailing_address']; } ?></textarea>
				
                </div>
			</div>

			<div class="control-group">
				<label class="control-label">Location: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"> <i class="icon_password"></i>
					</span>
					<input type="text" value="<?php echo set_value('location', $form_data['location']);?>"  class="span3" name="location" />				
                </div>
			</div>

			<div class="control-group">
				<label class="control-label">City: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="text" value="<?php echo set_value('city', $form_data['city']);?>"  class="span3" name="city">				
                </div>
			</div>

            <div class="control-group">
            	<label class="control-label">Telephone: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="text" value="<?php echo set_value('telephone', $form_data['telephone']);?>"  class="span3" name="telephone">				
                </div>
			</div>

            <div class="control-group">
            	<label class="control-label"><?=($type==3)?"Connection":("Withdrawal")?> Point: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="text" value="<?php echo set_value('deliverypoint', $form_data['deliverypoint']);?>"  class="span3" name="deliverypoint">				
                </div>
			</div>

			<div class="control-group">
				<div class="controls">
                <input type="hidden" value="<?=$form_data['id']?>" name="edit_id" />
					<input type="submit" value="Save" class="btn btn-primary btn-large" name="submit">					
				</div>
			</div>
			</form>
		
		</div>

	</div>

</div>
</div>

<div id="demand_myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Add Demand</h3>
    </div>
  
    <div class="modal-body" style="overflow:auto !important;">  
      <form id="demand_form" name="demand_form" method="POST"> 

      	<input type="hidden" value="<?=$form_data['id']?>" name="address_id" id="address_id" />
      	<!--<input type="hidden" value="<?=$form_data['user_id']?>" name="user_id" id="user_id" /> -->
      	
      	<div id="Demandsgroup">

	      	<div class="control-group" style="padding:10px 0 25px 0;">
				<div class="controls" style="float:left;font-weight:bold;">Source of power supply</div>
				<div class="controls" style="float:right;font-weight:bold;">Contracted Power (MW)</div>
			</div>
			<?php if($form_data['demand_details'] != 0){ 

				foreach($form_data['demand_details'] as $demands){ ?>

					<div class="control-group" id="TextBoxDiv">
						<div class="controls" style="float:left;">
							<input name="source_power[]" id="source_power" value="<?php echo $demands['source'];?>" style="width:200px" type="text">
						</div>
						<div class="controls" style="float:right;">
							<input name="amount_power[]" id="amount_power" value="<?php echo $demands['power'];?>" style="width:200px" type="text">
						</div>
					</div>

	  		<?php }

	  		 } else { ?>

	      	<div class="control-group" id="TextBoxDiv">
				<div class="controls" style="float:left;">
					<input name="source_power[]" id="source_power" style="width:200px" type="text">
				</div>
				<div class="controls" style="float:right;">
					<input name="amount_power[]" id="amount_power" style="width:200px" type="text">
				</div>
			</div>

	   		<?php } ?>		

		</div>	 

		<div class="span5 row-fluid" style="float:right;">
			<a class="btn btn-success" id='add_demand'>Add More</a>
			<a class="btn btn-danger" id='remove_demand'>Remove</a>
		</div>

		<div class="span5" style="text-align: center;"><span class="hide" id="error_message" style="color:red;">Please fill all source of power fields</span> </div>
		
      </form>
    </div>

    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
      <button class="btn btn-primary" id="InfroTextSubmit">Save</button>
    </div>
</div>

<div id="supply_myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Add Supply</h3>
    </div>
  
    <div class="modal-body" style="overflow:auto !important;">  
      <form id="supply_form" name="supply_form" method="POST"> 

      	<input type="hidden" value="<?=$form_data['id']?>" name="address_id" id="address_id" />
      	<!--<input type="hidden" value="<?=$form_data['user_id']?>" name="user_id" id="user_id" /> -->
      	
      	<div id="Supplygroup">

	      	<div class="supply-head">
	      		<div class="controls" style="text-align: left;">Plant</div>
				<div class="controls" style="text-align: center;">Recipient of Power</div>
				<div class="controls" style="text-align: right;">Contracted Power (MW)</div>
			</div>
			<?php if($form_data['supply_details'] != 0){ 

				foreach($form_data['supply_details'] as $demands){ ?>

					<div class="control-group" id="TextBoxDiv1">
						<div class="controls" style="float:left;">
							<select name="plant_sel[]" id="plant_sel" class="span2">
			      				<option value="">Select</option>
			      				<?php foreach($plants as $plant){
			      					$sel="";
			      					if($plant['plant_id']==$demands['plant_id'])
			      						$sel="selected='selected'";
			      					?>
			      					<option value="<?php echo $plant['plant_id'];?>" <?php echo $sel;?> ><?php echo $plant['plant_name'];?></option>
			      				<?php } ?>
			      			</select>
						</div>
						<div class="controls" style="float:left; margin-left: 13px;">
							<input name="source_power1[]" id="source_power1" value="<?php echo $demands['source'];?>" style="width:150px" type="text">
						</div>
						<div class="controls" style="float:right;">
							<input name="amount_power1[]" id="amount_power1" value="<?php echo $demands['power'];?>" style="width:150px" type="text">
						</div>
					</div>

	  		<?php }

	  		 } else { ?>

	      	<div class="control-group" id="TextBoxDiv1">
	      		<div class="controls" style="float:left;">
	      			<select name="plant_sel[]" id="plant_sel" class="span2">
	      				<option value="">Select</option>
	      				<?php foreach($plants as $plant){ ?>
	      					<option value="<?php echo $plant['plant_id'];?>"><?php echo $plant['plant_name'];?></option>
	      				<?php } ?>
	      			</select>
				</div>

				<div class="controls" style="float:left; margin-left: 13px;">
					<input name="source_power1[]" id="source_power1" style="width:150px" type="text">
				</div>
				<div class="controls" style="float:right;">
					<input name="amount_power1[]" id="amount_power1" style="width:150px" type="text">
				</div>
			</div>

	   		<?php } ?>		

		</div>	 

		<div class="span5 row-fluid" style="float:right;">
			<a class="btn btn-success" id='add_supply'>Add More</a>
			<a class="btn btn-danger" id='remove_supply'>Remove</a>
		</div>

		<div class="span5" style="text-align: center;"><span class="hide" id="error_message1" style="color:red;">Please fill all source of power fields</span> </div>
		
      </form>
    </div>

    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
      <button class="btn btn-primary" id="SupplySubmit">Save</button>
    </div>
</div>

<div id="No_demand_alert" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Information</h3>
    </div>
  
    <div class="modal-body" > 
    	Sorry, Please add your address and proceed to create Demands!
    </div>
    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true">OK</button>
    </div>
</div>

<div id="No_supply_alert" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Information</h3>
    </div>
  
    <div class="modal-body" > 
    	Sorry, Please add your address and proceed to create Supply details!
    </div>
    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true">OK</button>
    </div>
</div>

<script>

	$(document).ready(function(){			
		
		var n = $('input2').size()+ 2;

		$('#add_demand').click(function() {	

			if(n<=20){
				var htmlval2 = '<div class="control-group" id="TextBoxDiv"><div class="controls" style="float:left;"><input name="source_power[]" id="source_power" style="width:200px" type="text"></div><div class="controls" style="float:right;"><input name="amount_power[]" id="amount_power" style="width:200px" type="text"></div></div>';					
			
				$(htmlval2).fadeIn('slow').appendTo('#Demandsgroup');				
			n++;

			}else{
				alert("You can't add more than 20 options");
			}
		});
			
		$('#remove_demand').click(function() {
			//if(n > 2) {
				$('#TextBoxDiv:last').remove();
				//n--;
			//}
		});
	
	});	


$('#InfroTextSubmit').click(function(){

		$('#error_message').hide();
	    
	    $("input[name^='source_power']").each(function(){

		    if(this.value === "")
		    {
		      $('#error_message').html('Please fill all source of power fields');
		      $('#error_message').show();
		      return false;
		    }
		});

		$("input[name^='amount_power']").each(function(){

		    if(this.value === "")
		    {
		      $('#error_message').html('please fill all amount of power fields');
		      $('#error_message').show();
		      return false;
		    }
		});
	    
		data = $("#demand_form").serialize();
    
  		$.ajax({
	        url:base_url+'address/add_demand/',
	        type: "POST",
	        data: data,
	        dataType:"json",
	        success : function(rdata){
          
          		if(rdata.status=='success'){
          			alert(rdata.msg);
          			location.reload();
          		}else{

          			alert(rdata.msg);
          		}
	        },
	        error : function(rdata) {
	         alert("Please add atleast one demand!");
	        }
  	});    
	      
});

function no_address_alert(){
	BootstrapDialog.show({
        title: 'Information',
        message: 'Sorry, Please add your address and proceed to create Demands!'
    });
}

</script>

<script>

	$(document).ready(function(){			
		
		var n = $('input2').size()+ 2;

		var plants = '<option value="">Select</option>'
		<?php foreach($plants as $plant){ ?>
			plants += '<option value="<?php echo $plant['plant_id'];?>"><?php echo $plant['plant_name'];?></option>';
		<?php } ?>

		$('#add_supply').click(function() {	

			if(n<=20){
				var htmlval2 = '<div class="control-group" id="TextBoxDiv1"><div class="controls" style="float:left;"><select name="plant_sel[]" id="plant_sel" class="span2">'+plants+'</select></div><div class="controls" style="float:left;margin-left: 13px;"><input name="source_power1[]" id="source_power1" style="width:150px" type="text"></div><div class="controls" style="float:right;"><input name="amount_power1[]" id="amount_power1" style="width:150px" type="text"></div></div>';					
			
				$(htmlval2).fadeIn('slow').appendTo('#Supplygroup');				
			n++;

			}else{
				alert("You can't add more than 20 options");
			}
		});
			
		$('#remove_supply').click(function() {
			//if(n > 2) {
				$('#TextBoxDiv1:last').remove();
				//n--;
			//}
		});
	
	});	


$('#SupplySubmit').click(function(){

		$('#error_message1').hide();
	    
	    $("input[name^='source_power1']").each(function(){
		    if(this.value === "")
		    {
		      $('#error_message1').html('Please fill all Recipient of Power fields');
		      $('#error_message1').show();
		      return false;
		    }
		});

		$("input[name^='amount_power1']").each(function(){

		    if(this.value === "")
		    {
		      $('#error_message1').html('please fill all Contracted Power fields');
		      $('#error_message1').show();
		      return false;
		    }
		});

		$("select[name^='plant_sel']").children('option:selected').each(function(){
  			if(this.value === "")
		    {
		      $('#error_message1').html('please select all Plant fields');
		      $('#error_message1').show();
		      return false;
		    }
		});
	    
		data = $("#supply_form").serialize();
    
  		$.ajax({
	        url:base_url+'address/add_supply/',
	        type: "POST",
	        data: data,
	        dataType:"json",
	        success : function(rdata){
          
          		if(rdata.status=='success'){
          			alert(rdata.msg);
          			location.reload();
          		}else{

          			alert(rdata.msg);
          		}
	        },
	        error : function(rdata) {
	         alert("Please add atleast one supply!");
	        }
  	});    
	      
});
</script>