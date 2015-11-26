<div class="users form">
<?php echo $this->Session->flash(); ?>
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo $title_for_layout; ?></legend>
	<?php
		echo $this->Form->input('username', array('label' => 'ユーザー名', 'style' => 'ime-mode: disabled;'));
		echo $this->Form->input('name', array('label' => '　　　氏名'));
		if ($authUser['role'] == 999) {
			echo $this->Form->input('role', array('options'=> array('0'=> '一般', '999'=> '管理'),
			'label'=> '権限レベル', 'style'=> 'font-size: 140%;'));
		}
	?>
	</fieldset>
<?php echo $this->Form->end(__('更新する')); ?>
</div>
<script type="text/javascript">
$(function () {
	$('#UserUsername').focus()
})
</script>