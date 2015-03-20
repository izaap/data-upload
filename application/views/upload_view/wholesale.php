<div class="container gridco">
<h1><a class="btn btn-small pull_right" href="<?php echo site_url('upload/list_view'); ?>" ><i class="icon-backward"></i> Back to list</a></h1>
  <div class="logo">
  <img src="<?php echo site_url('assets/images/logo.png');?>" alt="" /> 
  </div>

<div class="row-fluid">
  
    <h1>DAILY AVAILABILITY DECLARATION FOR DISPATCH DAY <?php echo date('m/d/Y',$date);?></h1>
  
</div>


<h2>MAIN ADDRESS</h2>
<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">
  <tr>
    <td width="24%">Name of Organisation: </td>
    <td colspan="3"><?=(isset($address['organization']))?$address['organization']:""?></td>
  </tr>
  <tr>
    <td>Mailing Address: </td>
    <td colspan="3"><?=(isset($address['mailing_address']))?$address['mailing_address']:""?></td>
  </tr>
  <tr>
    <td>Location: </td>
    <td colspan="3"><?=(isset($address['location']))?$address['location']:""?></td>
  </tr>
  <tr>
    <td>City:</td>
    <td width="33%"><?=(isset($address['city']))?$address['city']:""?></td>
    <td width="14%">Telephone: <?=(isset($address['phone']))?$address['phone']:""?></td>
    <td width="29%"></td>
  </tr>
  <tr>
  </tr>
</table>

<h2>Market Coordinator</h2>
<table  border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
  <tr>
    <td width="24%">Name:</td>
    <td width="33%"><?=$user_details['name']?></td>
    <td width="21%">Type of Participant: </td>
    <td width="22%"><?=user_role_name($user_details['id'])?></td>
  </tr>
  <tr>
    <td>Cell Phone:</td>
    <td><?=$user_details['cellphone']?></td>
    <td>Other Phone:</td>
    <td><?=$user_details['otherphone']?></td>
  </tr>
  <tr>
    <td>Email:</td>
    <td><?=$user_details['email']?></td>
    <td>Fax Number:	</td>
    <td><?=$user_details['fax']?></td>
  </tr>
  <tr>
    <td>Special Unique ID</td>
    <td><?=$user_details['id']?></td>
    <td>Date of Submission:</td>
    <td><?=str2USDate($submit_date)?></td>
  </tr>
</table>

<table  border="0" cellspacing="0" cellpadding="0" width="100%"  class="delivery-area">
  <tr>
    <td width="18%">Connection Point:</td>
    <td width="3%"></td>
    <td width="39%">
      <div class="point-box" style=" padding: 15px 0 0 25px;"><?php echo (isset($address['deliverypoint']))?$address['deliverypoint']:""; ?></div>
    </td>
  </tr>
</table>

<h2 >Generation Details</h2>
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
    <td>Unit No:</td>
    <td colspan="23"><?=(isset($plants[$v]['no_units']))?$plants[$v]['no_units']:""?> <?=(isset($plants[$v]['plant_name']))?'('.$plants[$v]['plant_name'].')':""?></td>
  </tr>
  <tr>
    <td>Dispatch Period</td>
    <?=$head_td_str?>
  </tr>
  <tr>
    <td>MW</td>
  <?php for($j =1; $j<=24; $j++){
        echo "<td>";
        echo $temp = (isset($dp_data[$v]['MW']['field_'.$j]))?$dp_data[$v]['MW']['field_'.$j]:"";
        echo "</td>";
    } $i =$i+1; ?>
  </tr>
  <tr>
    <td>MV Ar</td>
  <?php for($j =1; $j<=24; $j++){
        echo "<td>";
        echo $temp = (isset($dp_data[$v]['MVAR']['field_'.$j]))?$dp_data[$v]['MVAR']['field_'.$j]:"";
        echo "</td>";
    } ?>
  </tr>
 </table>

 <h4 style="color:#1F50A6">Supply Details</h4>
<table  border="0" cellspacing="0" cellpadding="0" width="100%"  class="delivery-area">
  <tr>
    <td width="15%"></td>
    <td width="3%"></td>
    <td width="39%">Recipient of Power Supply</td>
    <td>&nbsp;</td>
    <td width="38%">Amount of Power (MW) </td>
  </tr>

  <?php 
    $source_det = explode(",",$plants[$v]['source']);
    $power_det = explode(",",$plants[$v]['power']);
   foreach ($source_det as $p=>$row) : ?>
    <tr>
      <td width="15%"></td>
      <td width="3%" class="count"><?php echo integerToRoman($p+1);?></td>
      <td class="bottom-line" ><?=(isset($row))?trim($row,'_'):""?> </td>
      <td>&nbsp;</td>
      <td class="bottom-line"><?=(isset($power_det[$p]))?trim($power_det[$p],'_'):""?></td>
    </tr>
  <?php endforeach;?>
  </table>

<?php endforeach;?>


<h3>Comments:</h3>
<div>
<?php echo html_entity_decode($comments);?>
</div>


</div>