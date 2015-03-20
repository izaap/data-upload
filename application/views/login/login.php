<div class="container login_sec m_top_30">
<div class="row bigtitle text-center">
  User Login
 </div>

	<?php if ($message = $this->service_message->render()) :?>
		<?php echo $message;?>
    <?php endif; ?>
    
	<?php if (validation_errors()) :?>
		<div class="alert m_top_20 m_bot_0">
	    	<button type="button" class="close" data-dismiss="alert">&times;</button>
	    	<strong>Warning!</strong>  <?php echo validation_errors();?>
	    </div>
    <?php endif; ?>
	<div class="row">
		<div class="span4"></div>
		<div class="span4 login_box m_auto m_top_30">
			<form class="form-horizontal" action="<?php site_url('login')?>" method="POST">
				<div class="control-group">
					<div class="controls btn-group input-prepend">
						<span class="add-on"> 
							<i class="icon_username"></i>
						</span> 
						<input class="span3" type="text" name="email" id="inputEmail" placeholder="LoginID or Email">
					</div>
				</div>
				<div class="control-group">
					<div class="controls btn-group input-prepend">
						<span class="add-on"><i class="icon_password"></i> </span> 
						<input class="span3" type="password" name="password" id="inputPassword"	placeholder="Password">
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary btn-large">Login</button>
						<a class="pull-right" href="<?php echo site_url("login/forgot_password");?>">Forgot Password?</a>
					</div>
				</div>
			</form>
		</div>
		<div class="span4"></div>
	</div>

</div>
