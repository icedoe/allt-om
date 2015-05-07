<h4>Nåt grejs här</h4>
<?php if(isset($user)): ?>
	<div>
		<?=$user['name'] ?>
	</div>
<?php endif; ?>
<?php if(isset($form)): ?>
	<div>
		<?=$form?>
	</div>
<?php endif; ?>
<?php if(isset($menu)): ?>
	<div>
		<ul>
		<?php foreach($menu as $key => $item): ?>
			<li><a href='<?=$item?>'><?=$key?></a></li>
		<?php endforeach; ?>
		</ul>
	</div>
<?php endif;?>
