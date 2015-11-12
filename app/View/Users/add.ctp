<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo $title_for_layout; ?></legend>
	<?php
		echo $this->Form->input('username', array('label' => 'ユーザー名'));
		echo $this->Form->input('password', array('label' => 'パスワード'));
		echo $this->Form->input('name', array('label' => '氏名'));
		echo $this->Form->select('role', array('0'=> '一般', '999'=> '管理'), array('label' => '氏名'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('追加')); ?>
</div>