<div class="col-md-2 left_nav_bar">
	<div class="right_menu_bar">
		<p>MENU</p>
		<ul class="inner_menu_01">
			<?php 
			
			/* $sum_menu_array = array('Account Settings'=>array('redirection'=>array('controller'=>'users','action'=>'dashboard'),'title'=>'Account Settings'),'Channel Manager'=>array('redirection'=>array('controller'=>'channels','action'=>'channel_manager'),'title'=>'Channel Manager'),'Streaming List'=>array('redirection'=>array('controller'=>'streams','action'=>'index'),'title'=>'Streaming List'),'Messages'=>array('redirection'=>array('controller'=>'users','action'=>'messages'),'title'=>'Messages'),'Statistics'=>array('redirection'=>array('controller'=>'users','action'=>'statistics'),'title'=>'Statistics')); */
			
			$sum_menu_array = array('Account Settings'=>array('redirection'=>array('controller'=>'users','action'=>'dashboard'),'title'=>'Account Settings'),'Channel Manager'=>array('redirection'=>array('controller'=>'channels','action'=>'channel_manager'),'title'=>'Channel Manager'),'My Streams List'=>array('redirection'=>array('controller'=>'streams','action'=>'index'),'title'=>'Streams List')/* ,'Add Stream'=>array('redirection'=>array('controller'=>'streams','action'=>'add'),'title'=>'Add Stream') */);
			foreach($sum_menu_array as $menu_name=>$menu_redirection)
			{
				///pr($menu_redirection['redirection']);
				/* echo $menu_redirection['redirection']['controller'];
				echo $menu_redirection['redirection']['action']; */
				$class = '';
				if($this->request->params['controller'] == $menu_redirection['redirection']['controller'] && $this->request->params['action'] ==$menu_redirection['redirection']['action'])
				{
					$class = 'active';
				}
				/* if($this->request->params['controller'] == $menu_redirection['redirection']['controller'] && $this->request->params['redirection']['action'] == $menu_redirection['action'])
				{
					$class = 'active';
				} */
				echo '<li class="'.$class.'">'.$this->Html->link($menu_name,$menu_redirection['redirection'],array('escape'=>false,'title'=>$menu_redirection['title'],'alt'=>$menu_redirection['title'])).'</li>';
			}
			?>
			<?php /*<li><a href="#">FAQ </a></li>
			<li><a href="#">Security & Privacy</a></li> */ ?>
			<?php /*<li><a href="<?php $this->Html->url(array('controller'=>'streams','action'=>'settings')) ?>">Streaming Settings</a></li> */ ?>
		</ul>
	</div>
</div>