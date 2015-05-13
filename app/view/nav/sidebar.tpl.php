<h6>Användarmeny</h6>
<?php if(isset($user)): ?>
	<div>
		<?=$user['acronym'] ?>
	</div>
<?php endif; ?>
<?php if(isset($form)): ?>
	<div>
		<?=$form?>
	</div>
<?php endif; ?>
<?php if(isset($menu)): ?>
	<div>
		<ul class='fa-ul'>
		<?php foreach($menu as $key => $item): ?>
			<li><a href='<?=$item['url']?>'><?=$item['icon'].$key?></a></li>
		<?php endforeach; ?>
		</ul>
	</div>
<?php endif;?>
