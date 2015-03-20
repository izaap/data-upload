<div class="container ">
<h1><a class="btn btn-small pull_right" href="<?php echo site_url('address');?>" ><i class="icon-backward"></i> Back to list</a>	
</h1>

<?php //echo"<pre>"; print_r($plants);?>


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
		
		<h1> <?=($form_data['id'])?("Edit"):("Add")?> Wholesale Supplier (Company Details) </h1>

		<div class="span7 login_box m_auto" style="margin: 0px auto; float: none; display: inline-block;">
		<form id="Useraddress" class="form-horizontal" name="Useraddress" method="POST" action="">		
					
            <div class="control-group">
            	<label class="control-label">Name of organization: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"> <i class="icon_username"></i>
					</span>
					<input type="text" value="<?php echo set_value('organization', $form_data['organization']);?>"   class="span3" name="organization">		
                </div>
			</div>
			
         	<input type="hidden" value="3"  id="user_role" name="user_role">		
        
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
            	<label class="control-label">Connection Point: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="text" value="<?php echo set_value('deliverypoint', $form_data['deliverypoint']);?>"  class="span3" name="deliverypoint">				
                </div>
			</div>

			<div id="Supplygroup">
			<div><h3>Supply Details</h3></div>
		      	<div class="supply-head">
		      		<div class="controls" style="text-align: left;">Plant</div>
					<div class="controls" style="text-align: center;">Recipient of Power</div>
					<div class="controls" style="text-align: right;">Contracted Power (MW)</div>
				</div>
				<?php 

				if($form_data['supply_details'] != 0){ 

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
							<div class="controls" style="float:left; margin-left: 50px;">
								<input name="source_power1[]" id="source_power1" value="<?php echo $demands['source'];?>"  required="required" style="width:150px" type="text">
							</div>
							<div class="controls" style="float:right;">
								<input name="amount_power1[]" id="amount_power1" value="<?php echo $demands['power'];?>" required="required" style="width:150px" type="text">
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

					<div class="controls" style="float:left; margin-left: 50px;">
						<input name="source_power1[]" id="source_power1" style="width:150px" type="text">
					</div>
					<div class="controls" style="float:right;">
						<input name="amount_power1[]" id="amount_power1" style="width:150px" type="text">
					</div>
				</div>

		   		<?php } ?>		

			</div>	 

			<div class="" style="float:right;">
				<a class="btn btn-success" id='add_supply'>Add More</a>
				<a class="btn btn-danger" id='remove_supply'>Remove</a>
			</div>
			<div class="clear" style="padding-top:20px;"></div>
			<div class="control-group row-fluid">
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


<script>

	$(document).ready(function(){			
		
		var n = $('input2').size()+ 2;

		var plants = '<option value="">Select</option>'
		<?php foreach($plants as $plant){ ?>
			plants += '<option value="<?php echo $plant['plant_id'];?>"><?php echo $plant['plant_name'];?></option>';
		<?php } ?>

		$('#add_supply').click(function() {	

			if(n<=20){
				var htmlval2 = '<div class="control-group" id="TextBoxDiv1"><div class="controls" style="float:left;"><select name="plant_sel[]" id="plant_sel" class="span2">'+plants+'</select></div><div class="controls" style="float:left;margin-left: 50px;"><input name="source_power1[]" id="source_power1" style="width:150px" type="text"></div><div class="controls" style="float:right;"><input name="amount_power1[]" id="amount_power1" style="width:150px" type="text"></div></div>';					
			
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

</script>