<?php

/**
 * HTTP Headers
 */
if(
	( isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/' )
	|| ( isset($_GET['page']) && $_GET['page'] == 'transactions' )
	|| ( isset($_GET['page']) && $_GET['page'] == 'charts' )
	|| ( isset($_GET['page']) && $_GET['page'] == 'trends' )
) {
	$valid = true;
} else {
	$valid = false;
}
if( !$valid ) {
	if( isset($_SERVER["SERVER_PROTOCOL"]) && $_SERVER["SERVER_PROTOCOL"] ) {
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
	} else {
		header('HTTP/1.0 404 Not Found', true, 404);
	}
}

/**
 * Configuration
 */

require_once('config.php');

/**
 * Functions
 */

require_once('functions.php');

/**
 * Procedures
 */

require_once('procedures.php');

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Transactions</title>
	<link rel="stylesheet" href="/css/style.min.css">
</head>
<body>

	<div id="content">

		<nav id="nav" class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
			<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/">Transactions</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li<?php if( isPage('home') || isPage('transactions') ) echo ' class="active"'; ?>><a href="/">View All <span class="sr-only">(current)</span></a></li>
						<li<?php echo isPage('charts'); ?>><a href="/charts/">Charts</a></li>
						<li<?php echo isPage('trends'); ?>><a href="/trends/">Trends</a></li>
					</ul>
				</div>
			</div>
		</nav>

		<?php
			if( !$valid ) {
				?>
				<div id="main-container" class="error container">
					<div class="row">
						<div class="col-md-12">
							<h2>Page Not Found</h2>
							<p>Sorry, that page could not be found.</p>
						</div>
					</div>
				</div>
				<?php
			}
		?>

		<?php if(
			( isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/' )
			|| ( isset($_GET['page']) && $_GET['page'] == 'transactions' )
		) {
			// TRANSACTIONS PAGE
			include('includes/transactions.inc.php');
		} else if( isset($_GET['page']) && $_GET['page'] == 'charts' ) {
			// CHARTS PAGE
			include('includes/charts.inc.php');
		} else if( isset($_GET['page']) && $_GET['page'] == 'trends' ) {
			// TRENDS PAGE
			include('includes/trends.inc.php');
		}
		?>

	</div>

	<div id="footer">

		<div class="container">
			<div class="row">
				<div class="col-md-12">
				&copy; <script>document.write(new Date().getFullYear())</script> <a href="http://websightdesigns.com/" target="_blank">webSIGHTdesigns</a>
				</div>
			</div>
		</div>

	</div>

	<script src="/js/script.min.js"></script>
	<?php require_once('scripts.php'); ?>

</body>
</html>