
<li>
	<input type="checkbox" name="checklist[]" value="<?= $id ?>" id="check_<?= $id?>" <?= ($is_checked == 1) ? 'checked="checked"' : '' ?> />
	&nbsp;&nbsp;<label for="check_<?= $id ?>"><?= $desc ?></label>
</li>