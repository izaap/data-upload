<div class="container ">
    <?php $role=get_user_role(); if($role === 2 || $role === 1 ){?>
    <a class="btn btn-primary pull-right" href="<?PHP echo site_url();?>assets/templates/Data forms Bulk Customers - ready.xlsx"><i class="icon-plus icon-white"></i> Download Bulk Template </a>
    <?php } if($role==3 || $role ==1) { ?>
    <a class="btn btn-primary pull-right" href="<?php echo site_url();?>assets/templates/Data forms Wholesale Suppliers - ready.xlsx"><i class="icon-plus icon-white"></i> Download Wholesale Template </a>
    <?php }?>
</div>
<div class="container ">
<h1>
    <a class="btn btn-small pull_right" href="<?php echo site_url('upload/list_view');?>" ><i class="icon-backward"></i> Back to list</a>
</h1>



<div class="container m_top_30">
	<?php if ($message = $this->service_message->render()) :?>
		<?php echo $message;?>
    <?php endif; ?>
    
    
    
	<?php if (validation_errors()) :?>
		<div class="alert m_top_20 m_bot_0" style="margin-left:380px;width:350px;">
	    	<button type="button" class="close" data-dismiss="alert">&times;</button>
	    	<strong>Warning!</strong>  <?php echo validation_errors();?>
	    </div>
        <br />
        <br />
    <?php endif; ?>
    <div class="row" style="text-align: center;">
		<h1>File Upload</h1>
	
        

		<div class="span4 login_box m_auto" style="margin: 0px auto; float: none; display: inline-block;">
		<form id="upload" class="form-horizontal" name="upload" method="POST" action="" enctype="multipart/form-data">		
					
   		<?php if(get_user_role() === 1):?>
            
            <input type="hidden" name="userlist" id="userlist" value="10" readonly >                    

        <?php endif; ?>
            <div class='control-group'>
                <div class="controls btn-group input-prepend">
                    <span class="add-on"> <i class="icon_username"></i></span>
                    <input type='text' class="span3" placeholder="Date" name="uploaddate" id="uploaddate" value="<?php echo set_value('uploaddate');?>" />
                    </div>
                </div>
                
        	<div class="control-group">
				<div class="controls btn-group input-prepend">
					<input type="file"  class="span3"  name="userexcel">				
                    </div>
				</div>
			<div class="control-group">
				
					<input type="submit" value="Save" class="btn btn-primary btn-large" name="submit">					
				</div>
			</div>
			</form>
		
		</div>

	</div>

</div>

</div>


<script type="text/javascript">
            // When the document is ready
            $(document).ready(function () {
                
                $('#uploaddate').datepicker({
                    format: "yyyy-mm-dd"
                });  
            
            });
        </script>
