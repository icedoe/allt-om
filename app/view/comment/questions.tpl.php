<hr>
<h2>Frågor</h2>

<?php if (is_array($comments)) : ?>
<div class='mess'>
<table>
<?php foreach ($comments as $id => $comment) : ?>
	<tr>
		<th>
			#<?=$comment->id?>
		</th>
		<th>
			<?=$comment->title?>
		</th>
	</tr>
	<tr>
		<td>
			<img src='<?=$comment->image?>' alt='<?=$comment->author?>'><br/>
			<?=$comment->author?><br/>
			<?=$comment->authortype?>
		</td>
		<td>
			<p class='clear'><?=$comment->content?></p>
		</td>
		<td>
			Kommentarer: <?=$comment->commentcount?><br/>
			Svar: <?=$comment->answercount?><br/>
			<a href ='<?php echo $this->di->url->create("comment/view/$comment->id") ?>'>Visa</a>
		</td>
	</tr>
	

<?php endforeach; ?>
</table>

</div>
<?php endif; ?>