<hr>
<h2>Kommentarer</h2>

<?php if (is_array($comments)) : ?>
<div class='mess'>
<table>
<?php foreach ($comments as $id => $com) : ?>
	<?php foreach($com as $comment) : ?>
	<tr>
		<th>
			#<?=$comment->id?>
		<th>
			<?=$comment->title?><br/>
			<?php foreach($comment->tags as $tag) : ?>
				<a class='tag' href='<?php echo $this->di->url->create("comment/tag/$tag")?>'><?=$tag?></a>
			<?php endforeach; ?>
		</th>
	</tr>
	<tr>
		<td>
			<img src='<?=$comment->image?>' alt='<?=$comment->author?>'>
			<?=$comment->author?><br/>
			<?=$comment->authortype?>
		<td>
			<p class='clear'><?=$comment->content?></p>
		</td>
		<td>
			<?php if($this->di->session->has('user') && $comment->type != 'comment') : ?>
				<a href='<?php echo $this->di->url->create("comment/edit/comment/$comment->id")?>'>Kommentera</a><br/>
				<a href='<?php echo $this->di->url->create("comment/edit/answer/$comment->id")?>'>Besvara</a>
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
<?php endforeach; ?>
</table>

</div>
<?php endif; ?>