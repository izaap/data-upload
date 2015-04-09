<script type="text/javascript">
    var current_user_id = <?php echo $current_user_id;?>;
    $(function() {
        $('#web_form_date,#web_icon_date').datepicker({
            format: 'dd-mm-yyyy',
            startDate: '-0d'
        }).on('changeDate', function (ev) {
          var temp = new Date(ev.date);
          location.href = base_url+'upload_form/'+current_user_id+'/'+temp.getFullYear()+'-'+(temp.getMonth()+1)+'-'+temp.getDate();
        });
    });
</script>
<div class="container gridco">

<form method="POST">

<div class="logo">
  <img src="<?php echo site_url('assets/images/logo.png');?>" alt="" /> 
</div>

<div class="row">
  <div class="span7">
    <h1>DAILY DEMAND SHEET FOR DISPATCH DAY</h1>
  </div>
  <div data-date-format="dd-mm-yyyy" data-date="<?php echo date('d-m-Y',$date);?>" class="span4 input-append date m_top_15" >
    <input type="text" readonly="" name="date" id="web_form_date" value="<?php echo date('d-m-Y', $date);?>" size="16" style="width:110px;">
    <span class="add-on" id="web_icon_date"><i class="icon-calendar"></i></span>
  </div>
</div>


<h2>MAIN ADDRESS</h2>


<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="table table-bordered bulk_wholesale_text">
  <tr>
    <td>Name of Organisation: </td>
    <td colspan="3"><input type="text" readonly value="<?=(isset($address['organization']))?$address['organization']:""?>"></td>
  </tr>
  <tr>
    <td>Mailing Address: </td>
    <td colspan="3"><input type="text" readonly value="<?=(isset($address['mailing_address']))?$address['mailing_address']:""?>">	</td></td>
  </tr>
  <tr>
    <td>Location: </td>
    <td colspan="3"><input type="text" readonly value="<?=(isset($address['location']))?$address['location']:""?>"> </td>
  </tr>
  <tr>
    <td>City: </td>
    <td><input type="text" readonly value="<?=(isset($address['city']))?$address['city']:""?>"></td>
    <td width="14%">Telephone: </td>
    <td width="29%"><input type="text" readonly value="<?=(isset($address['phone']))?$address['phone']:""?>"></td>
  </tr>
  	
   	
</table>




<h2>Market Coordinator</h2>
<table  border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
  <tr>
    <td width="24%">Name:</td>
    <td width="33%"><input type="text" readonly value="<?=$user_details['name']?>"></td>
    <td width="21%">Type of Participant: </td>
    <td width="22%"><input type="text" readonly value="<?=user_role_name($user_details['id'])?>"></td>
  </tr>
  <tr>
    <td>Cell Phone:</td>
    <td><input type="text" readonly value="<?=$user_details['cellphone']?>"></td>
    <td>Other Phone:</td>
    <td><input type="text" readonly value="<?=$user_details['otherphone']?>"></td>
  </tr>
  <tr>
    <td>Email:</td>
    <td><input type="text" readonly value="<?=$user_details['email']?>"></td>
    <td>Fax Number:</td>
    <td><input type="text" readonly value="<?=$user_details['fax']?>"></td>
  </tr>
  <tr>
    <td> Unique ID</td>
    <td><input type="text" readonly value="<?=$user_details['id']?>"></td>
    <td>Date of Submission:</td>
    <td><input type="text" readonly value="<?=date('d/m/Y')?>"></td>
  </tr>
</table>


<h2>Demand Details</h2>
<table  border="0" cellspacing="0" cellpadding="0" width="100%"  class="delivery-area">
  <tr>
    <td width="18%">Withdrawal Point:</td>
    <td width="3%"></td>
    <td width="39%">
      <input type="text" readonly value="<?php echo (isset($address['deliverypoint']))?$address['deliverypoint']:""; ?>">
    </td>
  </tr>
  <tr>
    <td width="18%"> </td>
    <td width="3%"></td>
    <td width="39%">Source (s) of Power Supply</td>
    <td>&nbsp;</td>
    <td width="38%">Contracted Power (MW) </td>
  </tr>
  <?php 
  $total_contract_power = 0;
  foreach ($demand_details as $k=>$row) : ?>
  <tr>
    <td width="18%"></td>
    <td width="2%" class="count"><?php echo integerToRoman($k+1);?></td>
    <td class="bottom-line" width="3%"><input type="text" readonly value="<?=(isset($row['source']))?trim($row['source'],'_'):""?>"></td>
    <td>&nbsp;</td>
    <td class="bottom-line"><input type="text" readonly value="<?=(isset($row['power']))?trim($row['power'],'_'):""?>"></td>
  </tr>
  <?php
      $total_contract_power += (isset($row['power']))?trim($row['power'],'_'):0;
   endforeach;?>
  
</table>
 
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
  <tr>
    <td style='padding:8px 1px 8px 1px !important;'>Dispatch Period</td>
 <?php for($i =1; $i<=24; $i++){
        echo "<td style='padding:8px !important;'>".$i."</td>";
    }?>
  </tr>
  <tr>
    <td style='padding:8px 1px 8px 1px !important;'>Demand (MW)</td>
    <?php for($i =1; $i<=24; $i++){
        echo "<td style='padding:8px 1px 8px 1px !important;'>";
        $temp = (isset($dp_data['field_'.$i]))?$dp_data['field_'.$i]:"";
        $cls = form_error("dp[$i]")?"class='error_outline'":'';
        echo "<input name='dp[$i]' id='dp_$i' value='".set_value("dp[$i]", $temp)."' onkeypress='return numbersonly(event)' $cls style='width:40px;height:25px;' />";
        echo "</td>";
    } ?>
  </tr>
</table>

<h3>Comments:</h3><textarea name="comments" id="comments" col="100" rows="4" style="width:400px;"><?php echo $comments;?></textarea>

<input type="submit" value="Submit" role="button" onclick="return demand_check('<?=$total_contract_power?>');" class="btn btn-success btn-large" />
<a class="btn btn-danger btn-large" style="margin-right:20px;" href="<?php echo site_url('upload/list_view'); ?>" ><i class="icon-backward"></i> Cancel</a>

</form>
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

function demand_check(total_contract) {

   var j=0;
   for (i = 1; i <=24; i++) { 

      var demand_val = ($("#dp_"+i).val() !='')?$("#dp_"+i).val():0;

      if(parseInt(demand_val) > parseInt(total_contract)){
         j=1; 
         $("#dp_"+i).addClass('error_outline');
      }

   }

   if(j==1){
      alert("The Demand(mw) is not greater than for total contracted power");
      return false;
   }
   else
   {
      return true;
   } 
}

</script>