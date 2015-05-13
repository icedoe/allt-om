<div class='user'>
	<h1>Profil #<?=$user['id']?></h1>
	<img src='<?=$user['image']?>' alt="Profilbild för <?=$user['acronym']?>">
	<h2><?= $user['acronym'] ?></h2>
	<span class='shortdesc'><?=$user['shortdesc']?></span>
	<?=$user['description']?>
	<div class='infoRow'>
		<p>
			Inlägg: <?= $user['posted'] ?>&nbsp;
			Medlem sedan: <?= $user['created'] ?>&nbsp;
			Email: <?=$user['email']?>
		</p>
	</div>
</div>