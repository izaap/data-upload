<style>
.login_box input[type="text"], .login_box input[type="password"] {
    border: 1px solid #CCCCCC;
}

</style>

<div class="container ">
<h1><a class="btn btn-small pull_right" href="<?php echo site_url('address'); ?>" ><i class="icon-backward"></i> Back to list</a></h1>

<div class="container m_top_30">
	<?php if ($message = $this->service_message->render()): ?>
		<?php echo $message; ?>
    <?php endif; ?>
    
	<?php if (validation_errors()): ?>
		<div class="alert m_top_20 m_bot_0">
	    	<button type="button" class="close" data-dismiss="alert">&times;</button>
	    	<strong>Warning!</strong>  <?php echo validation_errors(); ?>
	    </div>
    <?php endif; ?>
    
	<div class="row" style="text-align: center;">
	
		<h1> Add Plants </h1>
		<form id="plant" class="form-horizontal" name="plant" method="POST" action="<?php echo site_url('plant/add');?>">		

		<div id="PlantsGroup">		

			<?php 
			if($plant_data!=0 && $plant_data!=""){

				$i=1;
				foreach($plant_data as $plants){ ?>

					<div id="<?php echo ($i>1)?'TextBoxDiv':'';?>"  class="span9 login_box m_auto" style="float: none; display: inline-block;">
						<div class="control-group span4  m_top_10">
							Plant Name: <input type="text" value="<?php echo $plants['plant_name'];?>" id="plant_name" class="plant_name" name="plant_name[]">					
						</div>
						<div class="control-group span4  m_top_10">
							Location: <input type="text" value="<?php echo $plants['location'];?>" id="location"  name="location[]">					
						</div>
						<div class="control-group span4">
							Telephone: <input type="text" value="<?php echo $plants['telephone'];?>" id="telephone"  name="telephone[]">					
						</div>
						<div class="control-group span4">
							No.of Units: <input type="text" value="<?php echo $plants['no_units'];?>" id="no_units" name="no_units[]">					
						</div>
					</div>	
		  <?php $i++; 
				} 

			}else{	?>	

				<div  class="span9 login_box m_auto" style="float: none; display: inline-block;">
						<div class="control-group span4  m_top_10">
							Plant Name: <input type="text" id="plant_name" class="plant_name" name="plant_name[]">					
						</div>
						<div class="control-group span4  m_top_10">
							Location: <input type="text" id="location"  name="location[]">					
						</div>
						<div class="control-group span4">
							Telephone: <input type="text" id="telephone"  name="telephone[]">					
						</div>
						<div class="control-group span4">
							No.of Units: <input type="text" id="no_units" name="no_units[]">					
						</div>
					</div>	
		  <?php } ?>		

		</div>	

		<div class="span5" style="float:right;">
			<a class="btn btn-success" id='add_demand'>Add More</a>
			<a class="btn btn-danger" id='remove_demand'>Remove</a>
		</div>

		<div class="span5" style="text-align: center;"><span class="hide" id="error_message" style="color:red;">Please fill all source of power fields</span> </div>
		
		<div class="span9">
			<div class="controls">
				<!--<input type="hidden" value="<?php //echo $user_id; ?>" name="user_id" />-->
            	<input type="hidden" value="<?php echo $address_id; ?>" name="address_id" />
				<input type="submit" value="Save" onclick="return validate();" class="btn btn-primary btn-large" name="submit">					
			</div>

		</div>

		</form>

	</div>

</div>
</div>

<script>

	$(document).ready(function(){			
		
		var n = $('input2').size()+ 2;

		$('#add_demand').click(function() {	

			if(n<=20){
				var htmlval2 = '<div  id="TextBoxDiv" class="span9 login_box m_auto" style="float: none; display: inline-block;"><div class="control-group span4  m_top_10">Plant Name: <input type="text" id="plant_name" name="plant_name[]"></div><div class="control-group span4  m_top_10">Location: <input type="text" id="location"  name="location[]"></div><div class="control-group span4">Telephone: <input type="text" id="telephone" name="telephone[]"></div><div class="control-group span4">No.of Units: <input type="text" id="no_units" name="no_units[]"></div></div>';					
			
				$(htmlval2).fadeIn('slow').appendTo('#PlantsGroup');				
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

	function validate(){

		var errors = new Array();

		var values = [];		
		$('[name*="plant_name"]').each(function() {

			if($(this).val()==""){
				errors[0] = "Please fill the plant name";
			}
			else if ( $.inArray(this.value, values) >= 0 ) 
			{
		        errors[0] = "Plant name should be unique.";
		    }
		    values.push( this.value );
		    
		});	

		$('[name*="location"]').each(function() {
			if($(this).val()=="")
				errors[1] = "Please fill the location";
		});

		$('[name*="telephone"]').each(function() {
			if($(this).val()==""){
				errors[2] = "Please fill the Telephone";
			}else if(isNaN($(this).val()) || $(this).val()==0){
				errors[2] = "The telephone field must contain only numbers.";
			}

		});

		$('[name*="no_units"]').each(function() {
			if($(this).val()==""){
				errors[3] = "Please fill Number of units";
			}else if(isNaN($(this).val()) || $(this).val()==0){
				errors[3] = "The No.of units field must contain only numbers.";
			}

		});
		

		if(errors.length > 0){

			alert(errors.join("\n"));
			return false;
		}else{
			return true;
		}	


	}

</script>