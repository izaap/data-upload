<div class="container ">

  <h1>
    <a class="btn btn-small pull_left" href="<?php echo site_url('upload/list_view');?>" ><i class="icon-backward"></i> Back to list</a>

    <?php $role=get_user_role(); if($role === 1 ){?>
    <a class="btn btn-primary pull-right" href="<?PHP echo site_url();?>assets/templates/Data forms Bulk Customers - ready.xlsx"><i class="icon-plus icon-white"></i> Download Bulk Template </a> &nbsp;&nbsp;
    <a class="btn btn-primary pull-right" href="<?php echo site_url();?>assets/templates/Data forms Wholesale Suppliers - ready.xlsx"><i class="icon-plus icon-white"></i> Download Wholesale Template </a>
    <?php }?>
</h1>

<div class="container m_top_30">
	<?php if ($message = $this->service_message->render()) :?>
		<?php echo $message;?>
    <?php endif; ?>
    
    
    
	<?php if (validation_errors()) :?>
		<div class="alert m_top_20 m_bot_0" style="margin-left:300px;width:350px;">
	    	<button type="button" class="close" data-dismiss="alert">&times;</button>
	    	<strong>Warning!</strong>  <?php echo validation_errors();?>
	    </div>
        <br />
        <br />
    <?php endif; ?>
    <div class="alert m_top_20 m_bot_0" style="margin-left:260px;width:350px;display:none;" id="script_validation_errors"></div>
    <!--<a data-toggle="modal" href="" class="btn btn-primary">Launch modal</a>-->

<?php if(get_user_role() === 1){?>
    <div class="row" style="text-align: center;">
		<h1>Upload Type</h1>
	
		<div class="span6 login_box m_auto" style="margin: 0px auto; float: none; display: inline-block;">
		<form id="upload_type" class="form-horizontal" name="upload_type" method="POST" action="" enctype="multipart/form-data">		
					
   		
            <div class='control-group'>
                <label class="control-label">User</label>
                <div class="controls btn-group input-prepend">
                    <span class="add-on"> <i class="icon_username"></i></span>
                    <select name="user_id" id="user_id">
                        <option value="">Select User</option>
                        <?php if(!empty($userlist)){
                          foreach($userlist as $row){
                            $sel = set_select("userlist",$row['id']);
                            echo "<option ".$sel." value='{$row['id']}'/>".$row['name']."</option>";
                          }
                        }?>
                    </select>
                    </div>
                </div>           
            <div class='control-group'>
                <label class="control-label">Type</label>
                <div class="controls btn-group input-prepend">
                    <span class="add-on"> <i class="icon_username"></i></span>
                    <select name="type" id="type" >
                        <option value="web">Web Form</option> 
                        <option value="file">File Upload</option> 
                    </select>                       
                </div>
            </div>

			<div class="control-group">
				
					<input type="submit" id="file_upload" value="submit" class="btn btn-primary btn-large" name="submit">					
			</div>

     </form>       
    </div>
      
    
    </div>
    
<?php }else{ ?>

  <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_name;?>" readonly >                    

      
<?php } ?>


</div>

</div>


<div class="modal hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    
    <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h1 class="modal-title">File Upload</h1>
    </div>

  <form id="upload_form" class="form-horizontal" name="upload_form" method="POST" action="<?php echo site_url('upload');?>" enctype="multipart/form-data" >     
    
    <div class="modal-body" style="overflow:auto !important;">

        <div class="alert m_top_20 m_bot_0" style="margin-left:120px;margin-bottom:50px;width:250px;display:none;" id="upload_validation_errors"> &nbsp;</div>
          
          <div class="span5 login_box m_auto" style="margin: 0px auto; float: none; display: inline-block;">
      					
         		 <?php if(get_user_role() === 1):?>
                  
                  <input type="hidden" name="userlist" id="userlist" value="<?php echo $this->session->userdata('upload_user');?>" readonly >                    

              <?php endif; ?>

              <div class='control-group m_top_15'>
                  <div class="span4 btn-group input-prepend">
                      <span class="add-on"> <i class="icon_username"></i></span>
                      <input type='text' class="span3" placeholder="Date" name="uploaddate" id="uploaddate" value="<?php echo set_value('uploaddate');?>" />
                  </div>
              </div>
                      
              <div class="control-group">
        				<div class="span4 btn-group input-prepend">
        					<input type="file"  class="span3"  name="userexcel" id="userexcel">				
                </div>
      				</div>       
			   </div>	
       
    </div>

    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
      <input type="submit" class="btn btn-primary" id="excel_upload" name="excel_upload" value="Submit">
    </div> 

  </form> 
</div>

<script type="text/javascript">

 $(document).ready(function () {
      
      $('#uploaddate').datepicker({
          format: "yyyy-mm-dd"
      });  
  
  });

$("#file_upload").click( function(){
    
    var user = $("#user_id").val();
    var type = $("#type").val();

    if(user=='')
    {
       $("#script_validation_errors").css("display","block");
       $("#script_validation_errors").html("User field is required");
       $("#script_validation_errors").focus();
       return false; 
    }
    else if(type=='')
    {
       $("#script_validation_errors").css("display","block");
       $("#script_validation_errors").html("Type field is required");
       $("#script_validation_errors").focus();
       return false; 
    }
    else if(type=='file')
    {
        $("#userlist").val(user);
        $("#myModal").modal('show');
        return false;
    }
    return true;
});

$("#excel_upload").click( function(){
    
    var date     = $("#uploaddate").val();
    var userlist = $("#userlist").val();
    var file     = $("#userexcel").val();

    if(userlist=='')
    {
       $("#upload_validation_errors").css("display","block");
       $("#upload_validation_errors").html("User field is required");
       $("#upload_validation_errors").focus();
       return false; 
    }    
    else if(date=='')
    {
       $("#upload_validation_errors").css("display","block");
       $("#upload_validation_errors").html("Upload Date field is required");
       $("#upload_validation_errors").focus();
       return false; 
    }
    else if(file=='')
    {
       $("#upload_validation_errors").css("display","block");
       $("#upload_validation_errors").html("File is required");
       $("#upload_validation_errors").focus();
       return false; 
    }
    
    return true;
});

</script>