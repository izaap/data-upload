<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>New Data Uplooad</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Le styles -->
        <link href="<?php echo base_url();?>/assets/css/bootstrap.css" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo base_url();?>/assets/css/bootstrap-responsive.css" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo base_url();?>/assets/css/style.css" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo base_url();?>/assets/css/jquery.dataTables.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url();?>/assets/css/bootstrap-datetimepicker.min.css" media="screen" rel="stylesheet" type="text/css">

        <!-- Scripts -->
        <script type="text/javascript">
			var base_url = "<?php echo site_url();?>";
        </script>
        <!--[if lt IE 9]><script type="text/javascript" src="<?php echo base_url();?>/assets/js/html5shiv.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo base_url();?>/assets/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>/assets/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>/assets/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>/assets/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>/assets/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
    $(function() {
        $('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-0d'
        });
    });
</script>
    </head>
    <body>
    	<!-- header start here -->

		<header>
			<div class="container">
				<div class="row pull-left">
					<h1><img src="<?php echo site_url('assets/images/logo.png');?>" alt="" /></h1>
				</div>
                <div class="toplinks pull-right">
                <?php if(is_logged_in()): ?>
                    <?php 
                        $user_data = get_user_data(); 
                        $user_name = rtrim($user_data['name']);

                    ?>
                    <span>Welcome <?php echo ($user_data['role']==1)?$user_name:$user_data['company'].' ('.$user_name.')'; ?><br></span>
                    <a href="<?php echo site_url("login/logout");?>">logout</a>

                <?php endif; ?>
                </div>
			</div>
			
			<div class="navbar m_top_10">
				 <div class="navbar-inner">
					<div class="container">
						<a class="btn btn-navbar" data-toggle="collapse"
							data-target=".nav-collapse"> <span class="icon-bar"></span> <span
							class="icon-bar"></span> <span class="icon-bar"></span>
						</a>
						<div class="nav-collapse collapse">
							<ul class="nav navbar-nav">
                           <?php if(is_logged_in()): ?> 
                        <li <?php echo ($this->uri->segment(1) == 'upload')?"class='active'":'';?>>
                            <a href="<?php echo site_url('upload/list_view');?>">Upload List</a>
                        </li>
                        <li <?php echo ($this->uri->segment(1) == 'user'  && ($this->uri->segment(2) != 'change_password' && $this->uri->segment(2) != 'gate_openings'))?"class='active'":'';?>>
                            <a href="<?php echo site_url('user');?>"><?php echo (is_admin())?"Market Co-ordinators":"Profile";?></a>
                        </li>
                        <li <?php echo ($this->uri->segment(1) == 'address')?"class='active'":'';?>>
                            <a href="<?php echo site_url('address');?>"><?php echo (is_admin())?"Company Management":"Company";?></a>
                        </li>

                        <?php if(is_admin()) { ?>
                        <li <?php echo ($this->uri->segment(1) == 'plant')?"class='active'":'';?>>
                            <a href="<?php echo site_url('plant');?>">Plant Details</a>
                        </li>
                        <?php } ?>

                        <li <?php echo ($this->uri->segment(2) == 'change_password')?"class='active'":'';?>>
                            <a href="<?php echo site_url('user/change_password');?>">Change Password</a>
                        </li>
                        
                        <?php if(is_admin()) { ?>
                        <li <?php echo ($this->uri->segment(2) == 'gate_openings')?"class='active'":'';?>>
                            <a href="<?php echo site_url('user/gate_openings');?>">Gate Openings</a>
                        </li>
                        <?php } ?>

                         <?php endif; ?>
                    </ul>						
						</div>
						<!--/.nav-collapse -->
					</div>
				</div> 
			</div>
			
		</header>

		        
        <!-- body content start here -->
        <section class="body_container">