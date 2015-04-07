
<div class="container gridco">
<h1><a class="btn btn-small pull_right" href="<?php echo site_url('upload/list_view'); ?>" ><i class="icon-backward"></i> Back to list</a></h1>

<div class="logo">
  <img src="<?php echo site_url('assets/images/logo.png');?>" alt="" /> 
</div>

<div class="row-fluid">
  <h1>DAILY DEMAND SHEET FOR DISPATCH DAY <?php echo date('m/d/Y',$date);?></h1>
</div>


<h2>MAIN ADDRESS</h2>


<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="table table-bordered">
  <tr>
    <td>Name of Organisation: </td>
    <td colspan="3"><?=(isset($address['organization']))?$address['organization']:""?></td>
  </tr>
  <tr>
    <td>Mailing Address: </td>
    <td colspan="3"><?=(isset($address['mailing_address']))?$address['mailing_address']:""?>	</td></td>
  </tr>
  <tr>
    <td>Location: </td>
    <td colspan="3"><?=(isset($address['location']))?$address['location']:""?></td>
  </tr>
  <tr>
    <td>City: </td>
    <td><?=(isset($address['city']))?$address['city']:""?></td>
    <td width="14%">Telephone: <?=(isset($address['phone']))?$address['phone']:""?></td>
    <td width="29%"></td>
  </tr>
  	
   	
</table>




<h2 >Market Coordinator</h2>
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
    <td>Fax Number:</td>
    <td><?=$user_details['fax']?></td>
  </tr>
  <tr>
    <td> Unique ID</td>
    <td><?=$user_details['id']?></td>
    <td>Date of Submission:</td>
    <td><?=(!empty($submit_date))?str2USDate($submit_date):""?></td>
  </tr>
</table>


<h2>Demand Details</h2>
<table  border="0" cellspacing="0" cellpadding="0" width="100%"  class="delivery-area">
  <tr>
    <td width="18%">Withdrawal Point:</td>
    <td width="3%"></td>
    <td width="39%">
    <div class="point-box" style=" padding: 15px 0 0 25px;"><?php echo (isset($address['deliverypoint']))?$address['deliverypoint']:""; ?></div>
    </td>
  </tr>
  <tr>
    <td width="18%"></td>
    <td width="3%"></td>
    <td width="39%">Source (s) of Power Supply </td>
    <td>&nbsp;</td>
    <td width="38%">Contracted Power (MW) </td>
  </tr>
  <?php foreach ($demand_details as $k=>$row) : ?>
  <tr>
    <td width="18%"></td>
    <td width="2%" class="count"><?php echo integerToRoman($k+1);?></td>
    <td class="bottom-line" width="3%"><?=(isset($row['source']))?trim($row['source'],'_'):""?></td>
    <td>&nbsp;</td>
    <td class="bottom-line"><?=(isset($row['power']))?trim($row['power'],'_'):""?></td>
  </tr>
  <?php endforeach;?>
  
</table>


<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
  <tr>
    <td>Dispatch Period</td>
 <?php for($i =1; $i<=24; $i++){
        echo "<td>".$i."</td>";
    }?>
  </tr>
  <tr>
    <td>Demand (MW)</td>
    <?php for($i =1; $i<=24; $i++){
        echo "<td>";
        echo $temp = (isset($dp_data['field_'.$i]))?$dp_data['field_'.$i]:"";
        echo "</td>";
    } ?>
  </tr>
</table>

<h3>Comments:</h3>
<div>
<?php echo html_entity_decode($comments);?>
</div>

</div>