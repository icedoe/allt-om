<hr>

<?php if (is_array($comments)) : ?>
<div class='messages'>
<?php foreach ($comments as $id => $com) : ?>
	<?php foreach($com as $comment) : ?>
		<?php $imgsize= $comment->type == 'comment' ? '?s=40' : ''; ?>
		<div class='mess <?=$comment->type?>'>
			
			<aside class='user'>
				<img src='<?=$comment->image.$imgsize?>' alt='<?=$comment->author?>'>
				<p>
					<?=$comment->author?><br/>
					<?=$comment->authortype?>
				</p>
			</aside>

			<aside class='options'>
				<?php if($this->di->session->has('user') && $comment->type != 'comment') : ?>
					<a href='<?php echo $this->di->url->create("comment/edit/comment/$comment->id")?>'>Kommentera</a><br/>
					<?php if($comment->type == 'question') : ?>
						<a href='<?php echo $this->di->url->create("comment/edit/answer/$comment->id")?>'>Besvara</a>
				<?php endif; ?>
				<?php endif; ?>
			</aside>

			<div class='content'>
				<?php if($comment->type == 'question') : ?>
					<h1><?=$comment->title?></h1>
				<?php endif; ?>
				<?php if($comment->tags) : ?>
					<?php foreach($comment->tags as $tag) : ?>
						<a class='tag' href='<?php echo $this->di->url->create("comment/tag/$tag")?>'><?=$tag?></a>
					<?php endforeach; ?>
					<hr class='indiv' />
				<?php endif; ?>
				<br/>
				<?=$comment->content?>
			</div>

			<br class='clear' />
		</div>
	<?php endforeach; ?>
<?php endforeach; ?>


</div>
<?php endif; ?>