<div class="users">
<?php echo $this->Session->flash('pass'); ?>
<?php echo $this->Form->create('User'); ?>
<fieldset>
	<legend><?php echo __('パスワード変更'); ?></legend>
	<?php
		echo $this->Form->input('passwd', array('label' => '現在のパスワード'));
		echo $this->Form->input('password', array('label' => '新しいパスワード'));
		echo $this->Form->input('psword', array('label' => '新パスワード確認'));
	?>
</fieldset>
<?php echo $this->Form->end(__('変更する')); ?>
</div>
<script type="text/javascript">
$(function () {
	$('#UserPasswd').focus()
})
</script>