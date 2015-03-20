<div class="container ">
<h1><a class="btn btn-small pull_right" href="<?php echo site_url('user'); ?>" ><i class="icon-backward"></i> Back to list</a></h1>

<div class="container m_top_30">
	<?php if ($message = $this->service_message->render()): ?>
		<?php echo $message; ?>
    <?php endif; ?>
    
	<?php if (validation_errors()): ?>
		<div class="alert m_top_20 m_bot_0">
	    	<button type="button" class="close" data-dismiss="alert">&times;</button>
	    	<strong>Warning!</strong>  <?php echo validation_errors(); ?>
	    </div>
    <?php endif; ?>
    
	<div class="row" style="text-align: center;">
	
		<h1>Gate Openings</h1>
		<div class="span8 login_box m_auto" style="margin: 0px auto; float: none; display: inline-block;">
		<form id="gate_openings" class="form-horizontal" name="gate_openings" method="POST" action="">		
				
			<div class="control-group">
            	<label class="control-label" style="width:200px !important">&nbsp;</label>
				<div class="controls btn-group ">
					<div class="span2" style="float:left"><label>Opening(HH:MM)</label>
					</div>
					<div class="span2" style="float:left"><label>Closing(HH:MM)</label>
					</div>
				</div>
			</div>
						
            <div class="control-group">
            	<label class="control-label" style="width:200px !important">First Gate </label>
				<div class="controls btn-group input-prepend">	 	

				<?php $first_gate_strt = explode(":",$gate_results['first_gate_opening']); ?>

					<div class="span2" style="float:left"> 			
						<select name="first_gate_start_hour" id="first_gate_start_hour" style="width:80px;" >
							<?php for($i=0;$i<=22;$i++){ ?>
	                        	<option value="<?php echo sprintf("%02s", $i);?>" <?php echo (sprintf("%02s", $i) == set_value('first_gate_start_hour',$first_gate_strt[0]))?'selected':'';?> ><?php echo sprintf("%02s", $i);?></option>
	                        <?php } ?>
	                    </select>    

	                    <select name="first_gate_start_min" id="first_gate_start_min" class="m_left_10" style="width:80px;" >
							<?php for($j=0;$j<=55;$j+=5){ ?>
	                        	<option value="<?php echo sprintf("%02s", $j);?>" <?php echo (sprintf("%02s", $j)==set_value('first_gate_start_min',$first_gate_strt[1]))?'selected':'';?> ><?php echo sprintf("%02s", $j);?></option>
	                        <?php } ?>	
	                    </select>
                    </div>   

                    <?php $first_gate_end = explode(":",$gate_results['first_gate_closing']); ?>

                    <div class="span2" style="float:left">
						<select name="first_gate_end_hour" id="first_gate_end_hour" class="m_left_10" style="width:80px;" >
							<?php for($i=1;$i<=23;$i++){ ?>
	                        	<option value="<?php echo sprintf("%02s", $i);?>" <?php echo (sprintf("%02s", $i) == set_value('first_gate_end_hour',$first_gate_end[0]))?'selected':'';?> ><?php echo sprintf("%02s", $i);?></option>
	                        <?php } ?>
	                    </select>    

	                    <select name="first_gate_end_min" id="first_gate_end_min" class="m_left_10" style="width:80px;" >
							<?php for($j=0;$j<=55;$j+=5){ ?>
	                        	<option value="<?php echo sprintf("%02s", $j);?>" <?php echo (sprintf("%02s", $j)==set_value('first_gate_end_min',$first_gate_end[1]))?'selected':'';?> ><?php echo sprintf("%02s", $j);?></option>
	                        <?php } ?>
	                    </select>  
	                </div>      
				</div>
			</div>

			<div class="control-group">
            	<label class="control-label" style="width:200px !important">Second Gate </label>
				<div class="controls btn-group input-prepend">	

					<?php $second_gate_strt = explode(":",$gate_results['second_gate_opening']); ?>

					<div class="span2" style="float:left"> 				
						<select name="second_gate_start_hour" id="second_gate_start_hour" style="width:80px;" >
							<?php for($i=0;$i<=22;$i++){ ?>
	                        	<option value="<?php echo sprintf("%02s", $i);?>" <?php echo (sprintf("%02s", $i) == set_value('second_gate_start_hour',$second_gate_strt[0]))?'selected':'';?> ><?php echo sprintf("%02s", $i);?></option>
	                        <?php } ?>
	                    </select>    

	                    <select name="second_gate_start_min" id="second_gate_start_min" class="m_left_10" style="width:80px;" >
							<?php for($j=0;$j<=55;$j+=5){ ?>
	                        	<option value="<?php echo sprintf("%02s", $j);?>" <?php echo (sprintf("%02s", $j)==set_value('second_gate_start_min',$second_gate_strt[1]))?'selected':'';?> ><?php echo sprintf("%02s", $j);?></option>
	                        <?php } ?>
	                    </select> 
                    </div>
                
                	<?php $second_gate_end = explode(":",$gate_results['second_gate_closing']); ?>

                    <div class="span2" style="float:left">  
						<select name="second_gate_end_hour" id="second_gate_end_hour" class="m_left_10" style="width:80px;" >
							<?php for($i=1;$i<=23;$i++){ ?>
	                        	<option value="<?php echo sprintf("%02s", $i);?>" <?php echo (sprintf("%02s", $i) == set_value('second_gate_end_hour',$second_gate_end[0]))?'selected':'';?> ><?php echo sprintf("%02s", $i);?></option>
	                        <?php } ?>
	                    </select>    

	                    <select name="second_gate_end_min" id="second_gate_end_min" class="m_left_10" style="width:80px;" >
							<?php for($j=0;$j<=55;$j+=5){ ?>
	                        	<option value="<?php echo sprintf("%02s", $j);?>" <?php echo (sprintf("%02s", $j)==set_value('second_gate_end_min',$second_gate_end[1]))?'selected':'';?> ><?php echo sprintf("%02s", $j);?></option>
	                        <?php } ?>
	                    </select>
                    </div>    
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<input type="submit" value="Submit" class="btn btn-primary btn-large" name="submit">					
				</div>
			</div>
			</form>
		
		</div>

	</div>

</div>
</div>