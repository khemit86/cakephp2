<div class="right-contant" style="min-height:500px;text-align:center;">
	
		
		
		<div class="loading_bank" id="loading_bank_img" style="padding-top:250px;">
		<?php //echo $this->Html->image('ajax-loader.gif', array('id'=>'loading-bank-image')) ?>
		Processing please wait......
		</div>
		
		
		<?php
		$paypal_url='https://www.sandbox.paypal.com/cgi-bin/webscr'; // Test Paypal API URL
		$paypal_id='manish.shrm1@gmail.com'; // Business email ID
		?>	
		
		<form action="<?php echo $paypal_url ?>" method="post" name="frmPayPal1" style="display:none;">
			<?php /* <input type="hidden" name="business" value="<?php echo $channelData['User']['UserDetail']['paypal_email'] ?>"> */ ?>
			<input type="hidden" name="business" value="<?php echo $paypal_id; ?>">
			<input type="hidden" name="cmd" value="_xclick">
			<input type="hidden" name="item_name" value="<?php echo $channelData['Channel']['name'];?>">
			<input type="hidden" name="item_number" value="<?php echo $channelData['Channel']['id']; ?>">
			<input type="hidden" name="rm" value="2">
		   
			
			<!--<b>Amount:</b> <input type="text" id="amount" name="amount" value="1">-->
			<?php /* <b>Amount:</b> <input type="text" id="amount" name="amount" value="1"> */ ?>
			
			<input type="hidden" name="no_shipping" value="1">
			<input type="hidden" name="currency_code" value="USD">
			<input type="hidden" name="handling" value="0">
			<input type="hidden" name="cancel_return" value="<?php echo Configure::read('App.SiteUrl')."channels/paypal_cancel/".$channelData['Channel']['id'] ?>" >
			<input type="hidden" name="return" value="<?php echo Configure::read('App.SiteUrl')."channels/paypal_return/".$channelData['Channel']['id'] ?>">
			<input type="hidden" name="notify_url" value="<?php echo Configure::read('App.SiteUrl')."channels/paypal_notify" ?>">
			<input type="hidden" name="custom" value="<?php echo $this->Session->read('Auth.User.id');?>">
			<br /> <br />
			<input type="button" onclick="submitform();" name='donate' class="test" value="Donate" id="button">
		</form>
		<script type="text/javascript">
			document.frmPayPal1.submit();
		</script>

		
	
 </div>
 <?php echo ($this->element('Front/footer'))?>