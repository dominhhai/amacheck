<?php echo $this->Session->flash(); ?>
<div  class="users">
<?php echo $this->Form->create('User', array('name'=> 'UserIndexForm')); ?>
<?php echo $this->Form->input('user_id', array('type'=> 'hidden')); ?>
<fieldset>
	<legend><a href="/users/add" class="btn btn-primary btn-large">新規登録</a></legend>
	<table class="sellers table table-bordered table-hover fixed">
		<col width="6%"/><col width="25%"/><col width="35%"/><col width="10%"/><col width="12%"/><col width="12%"/>
	<thead>
		<tr>
			<th>No.</th>
			<th>ユーザ名</th>
			<th>氏名</th>
			<th>権限</th>
			<th colspan="2">アクション</th>
		</tr>
	</thead>
	<tbody>
	<?php if (!empty($users)): $counter = 0; ?>
	<?php foreach ($users as $user) : $counter ++; $user = $user['User']; ?>
		<tr>
			<td style="text-align: center;"><?php echo $counter; ?></td>
			<td><?php echo $user['username']; ?></td>
			<td><?php echo $user['name']; ?></td>
			<td style="text-align: center;">
				<?php echo $user['role'] == 999 ? '管理者' : '一般者'; ?>
			</td>
			<td style="text-align: center;">
				<a href="/users/detail?id=<?php echo $user['id']; ?>" class="btn btn-primary">変更</a>
			</td>
			<td style="text-align: center;">
				<a id="<?php echo $user['id']; ?>" href="#"  class="btn btn-danger btn-del" onclick="return false;">削除</a>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
	</table>
</fieldset>
<?php echo $this->Form->end(); ?>
</div>
<script type="text/javascript">
$(function () {
	$('.btn-del').on('click', function () {
		if (window.confirm('本当に削除しますか？')) {
			var form = document.forms['UserIndexForm']
			form['data[User][user_id]'].value = $(this).attr('id');
			form.submit();
		}
	})
})
</script>