	<?php $i=0 ?>
	<tbody>
	<?php if(is_array($results)) : ?>
	<?php foreach ($results as $result): ?>
		<tr class="<?= ($i%2 != 0 ) ? 'alt ' : "";?>">
			<td><input type="button" value="Send Friend Request" id="f_<?= $result['id'] ?>" class="request_sub" /></td>
			<td><?= $result['username'] ?></td>
			<td><?= $result['email'] ?></td>
		</tr>
		<?php $i++ ?>
	<?php endforeach; ?>
	<?php endif ?>
	</tbody>
