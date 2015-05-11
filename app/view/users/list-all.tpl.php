<?php if(isset($title)) : ?>
	<h1><?=$title?></h1>
<?php endif; ?>

<?php foreach($users as $usergroup): ?>
	<div class='userTableRow'>

	<?php foreach($usergroup as $user): ?>
		<div class='left userTable'>
			<a href='<?php echo $this->di->url->create('users/id/'.$user->id) ?>'>
			<table>
				<tr>
					<td><img src='<?=$user->image.'?s=40'?>'></td>
					<td colspan="2">
						<h4><?=$user->acronym?></h4>
						<hr />
						<h6><?=$user->type?></h6>
					</td>
					<td>
						Postat: <?=$user->posted?>
						<br /><br />
						Poäng: <?=$user->points?>
					</td>
				</tr>
				<tr>
					<td colspan="4"><p><?=$user->shortdesc?></p></td>
				</tr>
			</table>
			</a>
		</div>
	<?php endforeach; ?>

 	</div>
<?php endforeach; ?>