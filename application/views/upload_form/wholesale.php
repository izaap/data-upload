<script type="text/javascript">
    var current_user_id = <?php echo $current_user_id;?>;
    $(function() {
        $('#web_form_date,#web_icon_date').datepicker({
            format: 'dd/mm/yyyy',
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
    <h1>DAILY AVAILABILITY DECLARATION FOR DISPATCH DAY</h1>
  </div>
  <div data-date-format="dd-mm-yyyy" data-date="<?php echo date('d-m-Y', $date);?>" class="span4 input-append date m_top_15" >
    <input type="text" readonly="" name="date" id="web_form_date" value="<?php echo date('d-m-Y',$date);?>" size="16" style="width:110px;">
    <span class="add-on" id="web_icon_date"><i class="icon-calendar"></i></span>
  </div>
</div>


<h2>MAIN ADDRESS</h2>
<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered bulk_wholesale_text">
  <tr>
    <td width="24%">Name of Organisation: </td>
    <td colspan="3"><input type="text" readonly value="<?=(isset($address['organization']))?$address['organization']:""?>" ></td>
  </tr>
  <tr>
    <td>Mailing Address: </td>
    <td colspan="3"><input type="text" readonly value="<?=(isset($address['mailing_address']))?$address['mailing_address']:""?>" ></td>
  </tr>
  <tr>
    <td>Location: </td>
    <td colspan="3"><input type="text" readonly value="<?=(isset($address['location']))?$address['location']:""?>" ></td>
  </tr>
  <tr>
    <td>City:</td>
    <td width="33%"><input type="text" readonly value="<?=(isset($address['city']))?$address['city']:""?>" ></td>
    <td width="14%">Telephone: </td>
    <td width="29%"><input type="text" readonly value="<?=(isset($address['phone']))?$address['phone']:""?>" ></td>
  </tr>
  <tr>
  </tr>
</table>

<h2 >Market Coordinator</h2>
<table  border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
  <tr>
    <td width="24%">Name:</td>
    <td width="33%"><input type="text" readonly value="<?=$user_details['name']?>" ></td>
    <td width="21%">Type of Participant: </td>
    <td width="22%"><input type="text" readonly value="<?=user_role_name($user_details['id'])?>" ></td>
  </tr>
  <tr>
    <td>Cell Phone:</td>
    <td><input type="text" readonly value="<?=$user_details['cellphone']?>" ></td>
    <td>Other Phone:</td>
    <td><input type="text" readonly value="<?=$user_details['otherphone']?>" ></td>
  </tr>
  <tr>
    <td>Email:</td>
    <td><input type="text" readonly value="<?=$user_details['email']?>" ></td>
    <td>Fax Number:	</td>
    <td><input type="text" readonly value="<?=$user_details['fax']?>" ></td>
  </tr>
  <tr>
    <td>Special Unique ID</td>
    <td><input type="text" readonly value="<?=$user_details['id']?>" ></td>
    <td>Date of Submission:</td>
    <td><input type="text" readonly value="<?php echo date('d/m/Y');?>" ></td>
  </tr>
</table>
  <table  border="0" cellspacing="0" cellpadding="0" width="100%"  class="delivery-area">
    <tr>
      <td width="15%">Connection Point:</td>
      <td width="3%"></td>
      <td width="39%">
        <input type="text" readonly value="<?php echo (isset($address['deliverypoint']))?$address['deliverypoint']:""; ?>">
      </td>
    </tr>
  </table>

<h2>Generation Details</h2>
<?php 
$head_td_str = "";
for($i =1; $i<=24; $i++)
{
        $head_td_str.= "<td>".$i."</td>";
} 
?>

<?php //echo '<pre>';print_r($plants);print_r($units);?>
<?php foreach ($units as $k=>$v) : $l = $k+1;?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
  <tr>
    <td style='padding:8px 1px 8px 1px !important;'>Unit No:</td>
    <td colspan="23"><?=(isset($plants[$v]['no_units']))?$plants[$v]['no_units']:""?> <?=(isset($plants[$v]['plant_name']))?'('.$plants[$v]['plant_name'].')':""?></td>
  </tr>
  <tr>
    <td style='padding:8px 1px 8px 1px !important;'>Dispatch Period</td>
    <?=$head_td_str?>
  </tr>
  <tr>
    <td>MW</td>
  <?php for($j =1; $j<=24; $j++){
        echo "<td style='padding:8px 1px 8px 1px !important;'>";
        $temp = (isset($dp_data[$v]['MW']['field_'.$j]))?$dp_data[$v]['MW']['field_'.$j]:"";
        $o = $j-1;
        $cls = form_error('dp{$l}_mw{$v}[$o]')?"class='error_outline'":'';
        echo $cls."<input name='dp{$l}_mw{$v}[$o]' id='dp{$l}_mw{$v}' value='".((set_value("dp{$l}_mw{$v}[$o]", $temp))?set_value("dp{$l}_mw{$v}[$o]", $temp):'')."' onkeypress='return numbersonly(event)' style='width:40px;height:25px;'  $cls maxlength='5'/>";
        echo "</td>";
    } $i =$i+1; ?>
  </tr>
  <tr>
    <td>MV Ar</td>
  <?php for($j =1; $j<=24; $j++){
        echo "<td style='padding:8px 1px 8px 1px !important;'>";
        $temp = (isset($dp_data[$v]['MVAR']['field_'.$j]))?$dp_data[$v]['MVAR']['field_'.$j]:"";
        $o = $j-1;
        $cls = form_error('dp{$l}_mw{$v}[$o]')?"class='error_outline'":'';
        echo "<input name='dp{$l}_mv{$v}[$o]' id='dp{$l}_mv{$v}' value='".((set_value("dp{$l}_mv{$v}[$o]", $temp))?set_value("dp{$l}_mv{$v}[$o]", $temp):'')."' onkeypress='return numbersonly(event)' style='width:40px;height:25px;' $cls maxlength='5' />";
        echo "</td>";
    } ?>
  </tr>
 </table>

<h2 style="color:#1F50A6">Supply Details</h2>
<table  border="0" cellspacing="0" cellpadding="0" width="100%"  class="delivery-area">
  <tr>
    <td width="15%"></td>
    <td width="3%"></td>
    <td width="39%">Recipient of Power Supply</td>
    <td>&nbsp;</td>
    <td width="38%">Contracted Power (MW) </td>
  </tr>

  <?php 
    $source_det = explode(",",$plants[$v]['source']);
    $power_det = explode(",",$plants[$v]['power']);
  foreach ($source_det as $p=>$row) : ?>
    <tr>
      <td width="15%"></td>
      <td width="3%" class="count"><?php echo integerToRoman($p+1);?></td>
      <td class="bottom-line" ><input type="text" readonly value="<?=(isset($row))?trim($row,'_'):""?>" ></td>
      <td>&nbsp;</td>
      <td class="bottom-line"><input type="text" readonly value="<?=(isset($power_det[$p]))?trim($power_det[$p],'_'):""?>" ></td>
    </tr>
  <?php endforeach;?>

</table>

<?php endforeach;?>


<h3>Comments:</h3><textarea name="comments" id="comments" col="100" rows="4" style="width:400px;"><?php echo $comments;?></textarea>

<input type="submit" value="submit" class="btn btn-success btn-large" /> &nbsp;&nbsp;
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

</script>