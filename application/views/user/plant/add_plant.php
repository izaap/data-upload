<div class="row" style="text-align: center;">

    <form id="plant_form" class="form-horizontal" name="plant_form" method="POST"> 

            <div class="control-group m-top-10" style="margin-top:15px;">
                <label class="control-label">Plant Name:</label>
                <div class=" btn-group input-prepend">                   
                    <input type="text" value="<?php echo set_value('plant_name',$form_data['plant_name']); ?>"  class="span3"  name="plant_name" />           
                </div>
                  <?php echo form_error('plant_name', '<div style="color:red;margin-left: 75px;">', '</div>'); ?>

            </div>

            <div class="control-group">
                <label class="control-label">Location:</label>
                <div class="btn-group input-prepend">
                    <input type="text" value="<?php echo set_value('location',$form_data['location']); ?>"  class="span3"  name="location" />                              
                </div>
                <?php echo form_error('location', '<div style="color:red;margin-left: 75px;">', '</div>'); ?>
            </div>
            <div class="control-group">
                <label class="control-label">Telephone:</label>
                <div class=" btn-group input-prepend">
                   
                    <input type="text" value="<?php echo set_value('telephone',$form_data['telephone']); ?>"  class="span3"  name="telephone" />           
                </div>
                <?php echo form_error('telephone', '<div style="color:red;margin-left: 75px;">', '</div>'); ?>
            </div>
            <div class="control-group">
                <label class="control-label">No.of Units:</label>
                <div class=" btn-group input-prepend">                   
                    <input type="text" value="<?php echo set_value('no_units',$form_data['no_units']); ?>"  class="span3" onkeypress='return numbersonly(event)'  name="no_units" />           
                </div>
                <?php echo form_error('no_units', '<div style="color:red;margin-left: 75px;">', '</div>'); ?>
            </div>

      </form>
</div>
<div class="modal-footer">
      <input type="hidden" value="<?php echo $edit_id; ?>"  class="span3"  name="edit_id" />           
      <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
      <button class="btn btn-primary" onclick="add_plant('process','<?php echo $edit_id;?>')" id="InfroTextSubmit">Submit</button>
</div>

<script>

function numbersonly(e) {
  var unicode=e.charCode? e.charCode : e.keyCode
  //alert(unicode)
  if (unicode!=8 && unicode != 46){ //if the key isn't the backspace key (which we should allow)
  if (unicode<48||unicode>57) //if not a number
    {
      if(unicode==8 || unicode==46 || unicode == 37 || unicode == 39)//To  enable tab index in firefox and mac.(TAB, Backspace and DEL from the keyboard)
      return true
        else
      return false //disable key press
    }
  }
}

</script>