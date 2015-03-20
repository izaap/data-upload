<div class="container ">
<h1><a class="btn btn-small pull_right" href="<?php echo site_url('user'); ?>" ><i class="icon-backward"></i> Back to list</a></h1>

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
	
		<h1><?= ($form_data['id']) ? ("Edit") : ("Add") ?> Market Co-ordinator </h1>
		<div class="span6 login_box m_auto" style="margin: 0px auto; float: none; display: inline-block;">
		<form id="User" class="form-horizontal" name="User" method="POST" action="">		
					
            <div class="control-group">
            	<label class="control-label">Full Name: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"> <i class="icon_username"></i>
					</span>
					<input type="text" value="<?php echo set_value('name', $form_data['name']); ?>"  class="span3" required="required" name="name" />			
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">Cellphone: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="text" value="<?php echo set_value('cellphone', $form_data['cellphone']); ?>"  class="span3"  name="cellphone">				
				</div>
			</div>

            <div class="control-group">
            	<label class="control-label">Other Phone No: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="text" value="<?php echo set_value('otherphone', $form_data['otherphone']); ?>"  class="span3"  name="otherphone">				
				</div>
			</div>

	       <div class="control-group">
	       		<label class="control-label">Email: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"> <i class="icon_username"></i>
					</span>
					<input type="text" value="<?php echo set_value('email', $form_data['email']); ?>"  class="span3" required="required" name="email" />				
				</div>
			</div>

            <div class="control-group">
            	<label class="control-label">Fax Number: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"> <i class="icon_username"></i>
					</span>
					<input type="text" value="<?php echo set_value('fax', $form_data['fax']); ?>"  class="span3"  name="fax" />				
				</div>
			</div>

			<div class="control-group">
            	<label class="control-label">Username: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"> <i class="icon_username"></i>
					</span>
					<input type="text" value="<?php echo set_value('login_id', $form_data['login_id']); ?>"  class="span3" required="required" name="login_id">		
				</div>
			</div>
                    
			<div class="control-group">
				<label class="control-label">Password: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="password" value=""  class="span3"  name="password">				
                </div>
			</div>
			
			<div class="control-group">
				<label class="control-label">Confirm Password: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="password" value=""  class="span3"  name="conf_password">				
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">Type of Participant: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_username"></i> </span>
                        <select name="user_role" id="user_role" class="span3" onchange="get_rolewise_company(this.value)">
                        	<option value="">Select type</option>
                        	<?php if (!empty($roles)) {
							    foreach ($roles as $rkeys => $rvalues) { ?>
							                            
							        <option <?php if (isset($rvalues) && isset($form_data['role'])) {
							            if ($rvalues['id'] == $form_data['role']) {
							                echo "selected='selected'";
							            }
									        } ?> value="<?php echo
									$rvalues['id']; ?>"><?php echo
									$rvalues['role_name']; ?></option>
						  <?php }

							} ?>
                        
                        </select>
				</div>
			</div>


			<div class="control-group">
				<label class="control-label">Company: </label>
				<div id="rolewisecomp" class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_username"></i> </span>
                        <select name="user_company" id="user_company" class="span3">                  
                           <option value="">Select Company</option>            	                       

                        	<?php if (!empty($company_list)) {
                                foreach ($company_list as $key => $comp) { 
                                $sel='';
                                    if ($comp['id'] == set_value('user_company',$form_data['address_id'])) {
                                            $sel = "selected";
                                    } ?>

                        			<option value="<?php echo $comp['id'];?>" <?php echo $sel;?> ><?php echo $comp['organization'];?></option>  
                            <?php }

                            }  ?>

                        </select>
				</div>
			</div>

			
            <div class="control-group">
            	<label class="control-label">Designation: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_username"></i> </span>
					<input type="text" value="<?php echo set_value('designation', $form_data['designation']); ?>" value=""  class="span3"  name="designation">				
				</div>
			</div>

			<div id="wholesale_plant" class="control-group <?php echo (set_value('user_role',$form_data['role'])==3)?'':'hide';?>" >
            	<label class="control-label">Plant: </label>
				<div id="wholesale_select" class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_username"></i> </span>
                        <select name="plant" id="plant" class="span3">
                        	<option value="">Select Plant</option>              	                       
                        </select>					
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
                <input type="hidden" value="<?= $form_data['id'] ?>" name="edit_id" />
					<input type="submit" value="Save" class="btn btn-primary btn-large" name="submit">					
				</div>
			</div>
			</form>
		
		</div>

	</div>

</div>
</div>

<script>

$(document).ready(function(){

	var rolesel = <?php echo (set_value('user_role',$form_data['role']))?set_value('user_role',$form_data['role']):'0'; ?>;
	//alert(rolesel);
	get_rolewise_company(rolesel);
});

function get_rolewise_company(role_id){

	var sel = <?php echo (set_value('user_company',$form_data['address_id']))?set_value('user_company',$form_data['address_id']):'0'; ?>;

	$('#rolewisecomp').html('<img src="<?php echo site_url('assets/images/loader.gif');?>" alt="Loading" />');

	$.post(base_url+'user/get_rolewise_company/'+role_id+'/'+sel,'', function(data){
    //do your operation here. response is what you get from data.php. 'json' spicifies that the response is json type
    	$("#rolewisecomp").html(data);

	});

	wholsaleplant(sel);
	
	
}

function wholsaleplant(address_id){

	var role = $("#user_role").val();

	if(role==3){

		$('#wholesale_plant').show();

		var sel = <?php echo (set_value('plant',$form_data['plant']))?set_value('plant',$form_data['plant']):'0'; ?>;

		$('#wholesale_select').html('<img src="<?php echo site_url('assets/images/loader.gif');?>" alt="Loading" />');

		$.post(base_url+'user/get_companywise_plant/'+address_id+'/'+sel,'', function(data){
	    //do your operation here. response is what you get from data.php. 'json' spicifies that the response is json type
	    	$("#wholesale_select").html(data);

		});


	}else{
		$('#wholesale_plant').hide();
	}
}
</script>