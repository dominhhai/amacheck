<div class="upload price">
<?php echo $this->Session->flash('upload'); ?>
<?php echo $this->Form->create('Product'); ?>
<div class="center">
	<a href="../sellers" class="btn btn-warning" style="visibility: visible;">戻る</a>
	<?php echo $this->Form->button('CSV出力', 
		array('type' => 'submit', 'div'=> false,'class'=> "btn btn-primary"));
	?>
</div>
<table class="products table table-bordered table-hover fixed">
<col width="3%"/><col width="15%"/><col width="32%"/><col width="10%"/><col width="40%"/>
<thead>
	<tr>
		<th>選</th>
		<th>出品者名</th>
		<th>タイトル</th>
		<th>ASINコード</th>
		<th>ランキング変動グラフ</th>
	</tr>
</thead>
<tbody>
<?php if (isset($products)): ?>
<?php foreach ($products as $product): ?>
	<tr>
		<td style="text-align: center;"><?php echo $this->Form->checkbox($product['Product']['id'], array('hiddenField' => false)); ?></td>
		<td><?php echo $product['Seller']['name']; ?></td>
		<td><?php echo $product['Product']['name']; ?></td>
		<td><?php echo $product['Product']['id']; ?></td>
		<td class="load" id="<?php echo $product['Product']['id']; ?>" status="0" style="text-align: center;"><?php echo $status[0] ?></td>
	</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<?php echo $this->Form->end(); ?>
</div>

<script type="text/javascript">
function getPrice (ele) {
	ele.attr('status', 1)
	ele.text("<?php echo $status[1]; ?>")
	$.ajax({
		url: '/products/price',
		type: 'POST',
		data: {
			id: ele.attr('id')
		},
		success: function(data) {
			console.log(data)
			// if (data == -1) {
			// 	ele.attr('status', 3)
			// 	ele.text("<?php echo $status[3]; ?>")
			// } else {
				ele.attr('status', 2)
				ele.text("<?php echo $status[2]; ?>")
			// }
		},
		error: function(err) {
			console.log(err)
			ele.attr('status', 3)
			ele.text("<?php echo $status[3]; ?>")
		}
	})
}

$(document).ready(function() {
	$('table.products td.load').each(function(index) {
		getPrice($(this))
	})
})
</script>