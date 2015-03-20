<div class="container gridco">

<div class="logo">
  <img src="img/log.png" alt="" /> 
  </div>

<h1>DAILY DEMAND DATA SHEET FOR DISPATCH DAY</h1>
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

<h2 style="text-align:center;">Market Coordinator</h2>
<table  border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
  <tr>
    <td width="24%">Name:</td>
    <td width="33%"><?=$user_details['name']?></td>
    <td width="21%">Designation: </td>
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
    <td>Date:</td>
    <td><?=(isset($dispatch_data[0]['date']))?str2USDate($dispatch_data[0]['date']):""?></td>
  </tr>
</table>

<table  border="0" cellspacing="0" cellpadding="0" width="100%"  class="delivery-area">
  <tr>
    <td width="50%" colspan="3">Recipient of Power Supply and Amount of Power (MW) </td>
    <td width="3%"></td>
    <td width="39%"></td>
    <td>&nbsp;</td>
    <td width="8%"></td>
  </tr>
  
  <tr>
    <td width="15%"></td>
    <td width="3%" class="count">i</td>
    <td class="bottom-line"><?=(isset($demand_details[0]['source']))?trim($demand_details[0]['source'],'_'):""?></td>
    <td>&nbsp;</td>
    <td class="bottom-line"><?=(isset($demand_details[0]['power']))?trim($demand_details[0]['power'],'_'):""?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="3%" class="count">ii</td>
    <td class="bottom-line"><?=(isset($demand_details[1]['source']))?trim($demand_details[1]['source'],'_'):""?></td>
    <td>&nbsp;</td>
    <td class="bottom-line"><?=(isset($demand_details[1]['power']))?trim($demand_details[1]['power'],'_'):""?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="3%" class="count">iii</td>
    <td class="bottom-line"><?=(isset($demand_details[2]['source']))?trim($demand_details[2]['source'],'_'):""?></td>
    <td>&nbsp;</td>
    <td class="bottom-line"><?=(isset($demand_details[2]['power']))?trim($demand_details[2]['power'],'_'):""?></td>
  </tr>
</table>
<h2 style="text-align:center;">Generation Details</h2>
<?php 
$head_td_str = "";
for($i =1; $i<=24; $i++){
        $head_td_str.= "<td>".$i."</td>";
} 
     
for($i = 0; $i< count($dispatch_data); $i++) { ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
<tr>
<td>Unit No:</td>
<td colspan="23"><?=(isset($dispatch_data[$i]['unit_no']))?$dispatch_data[$i]['unit_no']:""?></td>
</tr>
  <tr>
    <td>Dispatch Period</td>
    <?=$head_td_str?>
  </tr>
  <tr>
    <td>MW</td>
  <?php for($j =1; $j<=24; $j++){
        echo "<td>";
        echo (isset($dispatch_data[$i]['field_'.$j]))?$dispatch_data[$i]['field_'.$j]:"";
         echo "</td>";
    } $i =$i+1; ?>
  </tr>
  
  <tr>
    <td>MV Ar</td>
  <?php for($j =1; $j<=24; $j++){
        echo "<td>";
        echo (isset($dispatch_data[$i]['field_'.$j]))?$dispatch_data[$i]['field_'.$j]:"";
         echo "</td>";
    } ?>
  </tr>
  </table>
  <?php }  ?>


<h3>Comments:</h3>
<h3>Signature:</h3>

<a href="<?=site_url('upload/view'.'/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.'excel')?>" class="btn"><i class="icon-arrow-down"></i> Download</a>
</div>