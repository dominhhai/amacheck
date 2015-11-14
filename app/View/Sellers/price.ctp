<div class="upload price">
<?php echo $this->Session->flash('upload'); ?>
<?php echo $this->Form->create('Product'); ?>
<div class="center">
	<a href="../sellers" class="btn btn-warning" style="visibility: visible;">戻る</a>
	<?php echo $this->Form->button('CSV出力', 
		array('type' => 'submit', 'div'=> false,'class'=> "btn btn-primary"));
	?>
</div>
<table class="table table-bordered table-hover">
<thead>
	<tr>
		<th></th>
		<th>出品者ID</th>
		<th>出品者名</th>
		<th>スタテース</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>1</td>
		<td>aaaaaaaa</td>
		<td>あああああああ</td>
		<td>ロード中</td>
	</tr>
	<tr>
		<td>1</td>
		<td>aaaaaaaa</td>
		<td>あああああああ</td>
		<td>ロード中</td>
	</tr>
	<tr>
		<td>1</td>
		<td>aaaaaaaa</td>
		<td>あああああああ</td>
		<td>ロード中</td>
	</tr>
	<tr>
		<td>1</td>
		<td>aaaaaaaa</td>
		<td>あああああああ</td>
		<td>ロード中</td>
	</tr>
	<tr>
		<td>1</td>
		<td>aaaaaaaa</td>
		<td>あああああああ</td>
		<td>ロード中</td>
	</tr>
</tbody>
</table>
<?php echo $this->Form->end(); ?>
</div>