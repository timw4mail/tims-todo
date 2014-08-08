
<section id="task_comment_list">
<?php $i=0; ?>
<?php if(!empty($comments)) : ?>
<?php foreach($comments as $c) : ?>
<dl id="comment_<?= $c['id']?>"<?= ($i%2 != 0 ) ? 'class="alt"' : "";?>>
	<dt ><?= $c['email']?><br /><?= date('D M d, Y g:iA T', $c['time_posted']);?><br />(<?= $c['status'] ?>)</dt>
	<dd>
		<?php if($c['user_id'] == $this->session->userdata('uid')): ?>
		<span class="editComment">
			<a href="#" class="delete comment_del">Delete</a>
		</span>
		<?php endif ?>
		<div class="comment_text"><?= $c['comment'] ?></div>
		<div class="clearB"></div>
	</dd>
	<?php $i++ ?>
</dl>
<?php endforeach ?>
<?php endif; ?>
</section>
