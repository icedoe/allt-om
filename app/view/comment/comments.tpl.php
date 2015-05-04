<hr>
<h2>Kommentarer</h2>

<?php if (is_array($comments)) : ?>
<div class='mess'>
<table>
<?php foreach ($comments as $id => $comment) : ?>
		<th>
			<form method='post'>
				<input type='hidden' name='commenter' value='<?=$commenter ?>'/>
				<input type='hidden' name="redirect" value="<?=$redirect?>"/>
				<input type='hidden' name='id' value='<?=$comment->id?>'/>
				<input class='commentHead' type='submit' name='commentId' value="Kommentar #<?=$id?>" />
			</form>
		</th>
		<tr>
		<td>
			<p class='clear'><?=$comment->content?></p>
		</td>
		<td>
			<p><?=$comment->name?></p>
			<p><?=$comment->email?></p>
			<p><?=$comment->web?></p>
		</td>
		</tr>

	</div>
<?php endforeach; ?>
</table>

<?php foreach ($links as $link) : ?>
<li><a href="<?=$link['href']?>"><?=$link['text']?></a></li>
<?php endforeach; ?>

</div>
<?php endif; ?>