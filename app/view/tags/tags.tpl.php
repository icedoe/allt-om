<?php if(isset($title)) : ?>
	<h1><?=$title?></h1>
<?php endif; ?>

<?php foreach($tags as $key => $tag) : ?>
	<a class='tag' href='<?=$urls[$key]?>'><?=$tag?></a>
<?php endforeach; ?>