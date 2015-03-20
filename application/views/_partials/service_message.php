	<div id="div_service_message" class="alert alert-<?php echo $service_message['status'];?> m_top_20 m_bot_0">
		<button type="button" class="close" data-dismiss="alert">&times;</button>	
		<strong><?php echo ucfirst($service_message['status']);?>:&nbsp;</strong>
		<?php echo $service_message['message'];?>
	</div>
	
