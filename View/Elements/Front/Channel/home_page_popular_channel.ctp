
<div class="home-contant popular_channels_ele_box">
      <div class="left-video"><?php 
		$streamPath		=	IMAGE_PATH_FOR_TIM_THUMB.CHANNEL_IMAGE_FULL_DIR."/";
		$stream_image		=	$popular_channels_detail['Channel']['image'];
		
		 echo $this->Html->image(SITE_URL.'/timthumb.php?src='.$streamPath.$stream_image.'&w=570&h=320&a=t',array('class'=>'','alt'=>$popular_channels_detail['Channel']['name'],'title'=>$popular_channels_detail['Channel']['name'],'class'=>'imgClass')) 
		
		/* echo  $this->Html->image($streamPath.$stream_image,array('style'=>'width:320;height:350px','class'=>'','alt'=>$popular_channels_detail['Channel']['name'],'title'=>$popular_channels_detail['Channel']['name'],'class'=>'imgClass')) */
	  
	  //echo ($this->Html->image('Front/slide_img.jpg')); ?>
	  </div>
      <div class="right-text">
        <h2><?php echo $popular_channels_detail['Channel']['name']?></h2>
        
        <?php /* <h6> 2h 34m  /   <?php echo date('d-M-Y',strtotime($popular_channels_detail['Channel']['created'])); ?> <?php //echo ($this->Html->image('Front/like.png')); ?></h6>  */?>
        <p><?php echo $popular_channels_detail['Channel']['bio']?>
          <br />		  
		<p style="text-align:justify">
			<?php
				echo $this->Html->link('Click here',array('controller'=>'channels','action'=>'channel_detail',$popular_channels_detail['Channel']['id']),array('style'=>'color:#4b96aa;','escape'=>false,'alt'=>$popular_channels_detail['Channel']['name'],'title'=>$popular_channels_detail['Channel']['name']));
			?>			
		</p>

		<?php 
	/* 	if($popular_channels_detail['ChannelFollower']['is_follow'] == 0){
			$relValWall = 'Follow';
			$heart_img = 'Front/heart_icn.png';
		}else{
			$relValWall = 'Following';
			$heart_img = 'Front/heart-icn-green.png';
		}  */
		?>

	   
      </div>
</div>
	

	
