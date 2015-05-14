


<?php if (is_array($comments)) : ?>
<div class='messages'>

<?php foreach ($comments as $id => $comment) : ?>

	<div class='mess <?=$comment->type?>'>

		<aside class='user'>
			<img src='<?=$comment->image?>' alt='<?=$comment->author?>'>
			<p>
				<?=$comment->author?><br/>
				<?=$comment->authortype?>
			</p>
		</aside>

		<aside class='options'>
			Kommentarer: <?=$comment->commentcount?><br/>
			Svar: <?=$comment->answercount?><br/>
			<a href ='<?php echo $this->di->url->create("comment/view/$comment->id") ?>'>Visa</a>
		</aside>

		<div class='content'>
			<h2><?=$comment->title?></h2>
			<?php foreach($comment->tags as $tag) : ?>
				<a class='tag' href='<?php echo $this->di->url->create("comment/tag/$tag")?>'><?=$tag?></a>
			<?php endforeach; ?>
			<hr class='indiv' />
		<?php if($comment->type != 'comment') : ?>
		</div>
		<div class='clear'>
	<?php endif; ?>
			<?=$comment->content?>
		</div>

		<hr class='clear' />

	</div>
<?php endforeach; ?>

</div>
<?php endif; ?>