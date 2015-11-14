<div class="upload">
<?php echo $this->Session->flash('upload'); ?>
<?php
	echo $this->Form->create('Seller', isset($sellers) ? array('type'=> 'file') : array('type'=> 'file', 'class'=> 'required'));
	echo $this->Form->label('file', '出品者 CSV');
	echo $this->Form->file('file', array('style'=> 'display: inline;'));
	$priceOpt = array('label' => '金額設定', 'div'=> false, 'onkeypress'=>"return event.charCode === 0 || event.charCode === 13 || /\d/.test(String.fromCharCode(event.charCode));");
	echo $this->Form->input('price_min', $priceOpt);
	?> 〜 <?php
	$priceOpt = array('label' => false, 'div'=> false, 'onkeypress'=>"return event.charCode === 0 || event.charCode === 13 || /\d/.test(String.fromCharCode(event.charCode));");
	echo $this->Form->input('price_max', $priceOpt);
	echo $this->Form->button('商品検索', array('type' => 'submit', 'div'=> false,'class'=> "btn btn-primary"));
	?><a id="btn-pricecheck" href="sellers/price" class="btn btn-info" disabled='disabled' onclick="return false;">プライスチェックへ</a>
<?php echo $this->Form->end(); ?>

<table class="sellers table table-bordered table-hover fixed">
	<col width="6%"/><col width="24%"/><col width="50%"/><col width="20%"/>
<thead>
	<tr>
		<th>No.</th>
		<th>出品者ID</th>
		<th>出品者名</th>
		<th>スタテース</th>
	</tr>
</thead>
<tbody>
<?php if (isset($sellers)): $counter = 0; ?>
<?php foreach ($sellers as $me => $seller) : $counter ++; ?>
	<tr>
		<td><?php echo $counter; ?></td>
		<td><?php echo $me; ?></td>
		<td><?php echo $seller['name']; ?></td>
		<td class="load" id="<?php echo $me; ?>" status="<?php echo $seller['status']; ?>" name="<?php echo $seller['name']; ?>" style="text-align: center;"><?php echo $status[$seller['status']] ?></td>
	</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>

<script type="text/javascript">
var counter
var max

function done () {
	var pricecheck = $('#btn-pricecheck')
	pricecheck.removeAttr('disabled')
	pricecheck.removeAttr('onclick')
}

function getProducts (ele) {
	ele.attr('status', 1)
	ele.text("<?php echo $status[1]; ?>")
	$.ajax({
		url: '/products/product',
		type: 'POST',
		data: {
			id: ele.attr('id'),
			name: ele.attr('name')
		},
		success: function(data) {
			if (data == -1) {
				ele.attr('status', 3)
				ele.text("<?php echo $status[3]; ?>")
			} else {
				ele.attr('status', 2)
				ele.text("<?php echo $status[2]; ?>（" + data + "件）")
			}

			counter ++
			if (counter >= max) done()
		},
		error: function(err) {
			ele.attr('status', 3)
			ele.text("<?php echo $status[3]; ?>")

			counter ++
			if (counter >= max) done()
		}
	})
}

$(document).ready(function() {
	var sellers = $('table.sellers td.load')
	max = sellers.length
	counter = 0
	sellers.each(function(index) {
		getProducts($(this))
	})
})
</script>