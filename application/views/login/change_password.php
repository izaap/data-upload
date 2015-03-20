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
	
		<h1>Change Password</h1>
		<div class="span6 login_box m_auto" style="margin: 0px auto; float: none; display: inline-block;">
		<form id="change_password" class="form-horizontal" name="change_password" method="POST" action="">		
					
            <div class="control-group">
            	<label class="control-label">Old Password: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"> <i class="icon_password"></i>
					</span>
					<input type="password" value="<?php echo set_value('old_password'); ?>"  class="span3" name="old_password" />			
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">New Password: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="password" value="<?php echo set_value('password'); ?>"  class="span3"  name="password">				
                </div>
			</div>
			
			<div class="control-group">
				<label class="control-label">Confirm Password: </label>
				<div class="controls btn-group input-prepend">
					<span class="add-on"><i class="icon_password"></i> </span>
					<input type="password" value="<?php echo set_value('conf_password'); ?>"  class="span3"  name="conf_password">				
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<input type="submit" value="Submit" class="btn btn-primary btn-large" name="submit">					
				</div>
			</div>
			</form>
		
		</div>

	</div>

</div>
</div>