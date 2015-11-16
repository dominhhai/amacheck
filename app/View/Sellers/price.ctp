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
<col width="3%"/><col width="13%"/><col width="24%"/><col width="10%"/><col width="5%"/><col width="5%"/><col width="40%"/>
<thead>
	<tr>
		<th>選</th>
		<th>出品者名</th>
		<th>タイトル</th>
		<th>ASINコード</th>
		<th>商品URL</th>
		<th>プライスチェックURL</th>
		<th>ランキング変動グラフ</th>
	</tr>
</thead>
<tbody>
<?php if (isset($products)): ?>
<?php foreach ($products as $product): ?>
	<tr>
		<td style="text-align: center;"><?php echo $this->Form->checkbox($product['Product']['id'], array('hiddenField'=> false, 'class'=> "checkbox")); ?></td>
		<td><?php echo $product['Seller']['name']; ?></td>
		<td><?php echo $product['Product']['name']; ?></td>
		<td><?php echo $product['Product']['id']; ?></td>
		<td style="text-align: center;"><a target="_blank" href="http://www.amazon.co.jp/dp/<?php echo $product['Product']['id']; ?>">確認</a></td>
		<td style="text-align: center;"><a target="_blank" href="http://so-bank.jp/detail/?code=<?php echo $product['Product']['id']; ?>">確認</a></td>
		<td class="load" id="<?php echo $product['Product']['id']; ?>" status="0" style="text-align: center; padding: 0;" name="<?php echo $product['Product']['name']; ?>">
			<div id="g-<?php echo $product['Product']['id']; ?>" style="width: 500px; height: 120px; margin: 0; padding: 0;">
				<?php echo $status[0] ?>
			</div>
		</td>
	</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<?php echo $this->Form->end(); ?>
</div>

<script type="text/javascript">
function drawGraph (id, data) {
	var graphData = []
	for (var date in data) {
		var dateTmp = date.split(',')
		graphData.push([new Date(parseInt(dateTmp[0]), parseInt(dateTmp[1]), parseInt(dateTmp[2])), parseInt(data[date])])
	}
	var dataTable = new google.visualization.DataTable()
	dataTable.addColumn('date', 'Date')
	dataTable.addColumn('number', 'ranking')
	dataTable.addRows(graphData)
	var options = {
		width: 500, height: 120,
		strictFirstColumnType: true,
		legend: 'none',
		pointSize: 5,
		vAxis: {direction: -1},
		hAxis: {format: 'M/d'},
		backgroundColor: {strokeWidth: 1 },
		chartArea: {left: 70, top: 7, width: '80%', height: '81%'},
		colors: ['#4bb2c5'],
		seriesType: "line",
		series: {1: {type: "bars", targetAxisIndex: 1, color: 'pink'}}
	}
	var chart = new google.visualization.LineChart(document.getElementById(id))
	chart.draw(dataTable, options)
}

function getPrice (ele, div) {
	ele.attr('status', 1)
	div.text("<?php echo $status[1]; ?>")
	$.ajax({
		url: '/products/price',
		type: 'POST',
		data: {
			id: ele.attr('id'),
			name: ele.attr('name')
		},
		success: function(data) {
			if (data == -1) {
				ele.attr('status', 3)
				div.text("<?php echo $status[3]; ?>")
			} else {
				ele.attr('status', 2)
				try {
					drawGraph(div.attr('id'), JSON.parse(data))
				} catch (ex) {
					div.text("<?php echo $status[2]; ?>")
				}
			}
		},
		error: function(err) {
			console.log(err)
			ele.attr('status', 3)
			div.text("<?php echo $status[3]; ?>")
		}
	})
}

google.setOnLoadCallback(function () {
	$('table.products td.load').each(function(index) {
		getPrice($(this), $('table.products td.load div#g-'+ $(this).attr('id')))
	})
})

$(function() {
	$('.checkbox').change(function() {
		$(this).parent().parent().css('background-color', $(this).is(':checked') ? 'yellow' : '');
	})
})
</script>