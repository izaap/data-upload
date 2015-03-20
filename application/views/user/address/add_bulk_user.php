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
		
		<h1> <?=($form_data['id'])?("Edit"):("Add")?> Bulk Customer (Company Details) </h1>

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
            	<label class="control-label">Withdrawal Point: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="text" value="<?php echo set_value('deliverypoint', $form_data['deliverypoint']);?>"  class="span3" name="deliverypoint">				
                </div>
			</div>


			<div id="Demandsgroup">
			<div><h3>Demand Details</h3></div>	
	      	<div class="control-group" style="padding:5px;">
				<div class="controls" style="float:left;font-weight:bold;">Source of power supply</div>
				<div class="controls" style="float:right;font-weight:bold;">Contracted Power (MW)</div>
			</div>
			<?php if($form_data['demand_details'] != 0){ 

				foreach($form_data['demand_details'] as $demands){ ?>

					<div class="control-group" id="TextBoxDiv">
						<div class="controls" style="float:left;">
							<input name="source_power[]" id="source_power" value="<?php echo $demands['source'];?>" style="width:150px" type="text">
						</div>
						<div class="controls" style="float:right;">
							<input name="amount_power[]" id="amount_power" value="<?php echo $demands['power'];?>" style="width:150px" type="text">
						</div>
					</div>

	  		<?php }

	  		 } else { ?>

	      	<div class="control-group" id="TextBoxDiv">
				<div class="controls" style="float:left;">
					<input name="source_power[]" id="source_power" style="width:150px" type="text">
				</div>
				<div class="controls" style="float:right;">
					<input name="amount_power[]" id="amount_power" style="width:150px" type="text">
				</div>
			</div>

	   		<?php } ?>		
	   		
		</div>	 

		<div class="" style="float:right;">
			<a class="btn btn-success" id='add_demand'>Add More</a>
			<a class="btn btn-danger" id='remove_demand'>Remove</a>
		</div>

		<div class="clear" style="padding-top:30px;"></div>
		<div class="control-group">
			<div class="controls">
	        <input type="hidden" value="<?=$form_data['id']?>" name="edit_id" />
				<button type="submit" class="btn btn-primary btn-large" name="submit" id="InfroTextSubmit">Save</button>					
			</div>
		</div>
		</form>
		
		</div>

	</div>

</div>
</div>


<script>

	$(document).ready(function(){			
		
		var n = $('input2').size()+ 2;

		$('#add_demand').click(function() {	

			if(n<=20){
				var htmlval2 = '<div class="control-group" id="TextBoxDiv"><div class="controls" style="float:left;"><input name="source_power[]" id="source_power" style="width:150px" type="text"></div><div class="controls" style="float:right;"><input name="amount_power[]" id="amount_power" style="width:150px" type="text"></div></div>';					
			
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




function no_address_alert(){
	BootstrapDialog.show({
        title: 'Information',
        message: 'Sorry, Please add your address and proceed to create Demands!'
    });
}

</script>

