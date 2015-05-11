<h1><?=$title?></h1>

<?php foreach($tags as $key => $tag) : ?>
	<a class='tag' href='<?=$urls[$key]?>'><?=$tag?></a>
<?php endforeach; ?>