<!DOCTYPE html>
<html lang="ja">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		AMC
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
	<?php if(isset($load_graph) && $load_graph): ?>
	<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1.0','packages':['corechart']}]}"></script>
	<?php endif; ?>
</head>
<body>
	<div id="container">
		<?php if($authUser): ?>
		<nav class="navbar navbar-inverse" style="margin-bottom:0;">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="/sellers">AmaCheck</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<?php if($authUser['role'] == 999): ?>
						<li><a href="/users/index">ユーザマスタ</a></li>
						<?php endif; ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $authUser['name']; ?><span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="/users/detail?id=<?php echo $authUser['id']; ?>">プロファイル</a></li>
								<li><a href="/users/change_pass">パスワード変更</a></li>
								<li role="separator" class="divider"></li>
								<li><a href="/users/logout">ログアウト</a></li>
							</ul>
						</li>
						<li><a href="/users/logout">ログアウト</a></li>
					</ul>
				</div>
			</div>
		</nav>
		<?php endif; ?>
		<div id="content">
			<?php echo $this->Flash->render(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<a href="http://haposoft.com/" target="_blank">連絡先 HapOsoft.com 2015</a>
		</div>
	</div>
</body>
</html>
