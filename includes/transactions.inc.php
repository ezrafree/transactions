<div id="main-container" class="home container-fluid">
	<div class="thead sticky container">
		<div class="row filters">
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" id="statement_filter" data-toggle="dropdown" aria-haspopup="true">
						<span class="text" data-default="View All">
							<?php
								if( isset($_GET['page']) && $_GET['page'] == 'transactions' ) {
									echo str_replace('-', '/', $_GET['id']);
								} else {
									echo 'View All';
								}
							?>
						</span>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" aria-labelledby="statement_filter">
						<li<?php $hasId = isset($_GET['id']); if( !$hasId ) echo ' class="active"'; ?>><a href="#transactions">View All</a></li>
						<?php
							foreach( $transactions AS $statement_date => $transactions_array ) {
								?>
								<li<?php if( isset($_GET['id']) && $_GET['id'] == $statement_date ) echo ' class="active"'; ?>>
									<a href="#<?= $statement_date ?>"><?= str_replace('-', '/', $statement_date) ?></a>
								</li>
								<?php
							}
						?>
					</ul>
					<input type="hidden" name="statement_filter" value="">
				</div>
			</div>
			<div class="col-md-6 hidden-sm hidden-xs"></div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="total">
					<?php
						if( isset($_GET['page']) && $_GET['page'] == 'transactions' ) {
							$statement_date = $_GET['id'];
							$subtotal = 0;
							foreach($transactions[$statement_date] AS $transaction) {
								$subtotal += $transaction['debit'];
							}
							echo 'Statement Total <strong>$' . number_format($subtotal, 2) . '</strong>';
						} else {
							echo 'Year-To-Date Total <strong>$' . number_format($total, 2) . '</strong>';
						}
					?>
				</div>
			</div>
		</div>
		<div class="row titles">
			<div class="col-sm-3 col-md-3">Date</div>
			<div class="col-sm-3 col-md-3">Description</div>
			<div class="col-sm-3 col-md-3">Category</div>
			<div class="col-sm-3 col-md-3 price">Amount</div>
		</div>
	</div>
	<div class="container">
		<?php
			if( isset($_GET['page']) && $_GET['page'] == 'transactions' ) {
				$statement_date = $_GET['id'];
				foreach($transactions[$statement_date] AS $transaction) {
					?>
					<div class="row">
						<div class="col-sm-3 col-md-3"><?= $transaction['date'] ?></div>
						<div class="col-sm-3 col-md-3"><?= $transaction['description'] ?></div>
						<div class="col-sm-3 col-md-3"><?= $transaction['category'] ?></div>
						<div class="col-sm-3 col-md-3 price">$<?= number_format($transaction['debit'], 2) ?></div>
					</div>
					<?php
				}
			} else {
				foreach($transactions AS $statement) {
					foreach( $statement AS $transaction ) {
						?>
						<div class="row">
							<div class="col-sm-3 col-md-3"><?= $transaction['date'] ?></div>
							<div class="col-sm-3 col-md-3"><?= $transaction['description'] ?></div>
							<div class="col-sm-3 col-md-3"><?= $transaction['category'] ?></div>
							<div class="col-sm-3 col-md-3 price">$<?= number_format($transaction['debit'], 2) ?></div>
						</div>
						<?php
					}
				}
			}
		?>
	</div>
</div>