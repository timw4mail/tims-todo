
<section id="task_checklist">
<ul id="checklist">
<?php $i=0; ?>
<?php if(!empty($checklist)) : ?>

<?php foreach($checklist as $c) : ?>
	<li>
		<input type="checkbox" name="checklist[]" value="<?= $c['id'] ?>" id="check_<?= $c['id']?>" <?= ($c['is_checked']== 1) ? 'checked="checked"' : '' ?> <?= ($user_perms <= PERM_CHECKLIST_ACCESS) ? 'disabled="disabled"' : '' ?> />
		&nbsp;&nbsp;<label for="check_<?= $c['id']?>"><?= $c['desc'] ?></label>
	</li>
<?php endforeach ?>
<?php endif ?>
</ul>
</section>