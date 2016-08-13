<?php if( isset($_GET['page']) && $_GET['page'] == 'charts' ) { ?>
	<script type="text/javascript">
		var ctx = $("#categoriesChart");
		var myChart = new Chart(ctx, {
			type: 'horizontalBar',
			responsive: true,
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero: true
						}
					}]
				},
				legend: {
					display: false
				}
			},
			data: {
				labels: [<?php echo $labels; ?>],
				datasets: [{
					data: [<?php echo $debits; ?>],
					backgroundColor: [
						'rgba(255, 99, 132, 0.2)',
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)',
						'rgba(255, 99, 132, 0.2)',
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)',
						'rgba(255, 99, 132, 0.2)',
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)',
						'rgba(255, 99, 132, 0.2)',
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)',
						'rgba(255, 99, 132, 0.2)',
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)',
						'rgba(255, 99, 132, 0.2)',
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)'
					],
					borderColor: [
						'rgba(255,99,132,1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)',
						'rgba(255,99,132,1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)',
						'rgba(255,99,132,1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)',
						'rgba(255,99,132,1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)',
						'rgba(255,99,132,1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)',
						'rgba(255,99,132,1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)'
					],
					borderWidth: 1,
				}]
			}
		});
	</script>
<?php } ?>

<?php if( isset($_GET['page']) && $_GET['page'] == 'trends' ) { ?>
	<script type="text/javascript">
		var canvas = document.getElementById("trendsChart");
		var ctx = canvas.getContext("2d");

		var horizonalLinePlugin = {
			afterDraw: function(chartInstance) {
				var yScale = chartInstance.scales["y-axis-0"];
				var canvas = chartInstance.chart;
				var ctx = canvas.ctx;
				var index;
				var line;
				var style;

				if (chartInstance.options.horizontalLine) {
					for (index = 0; index < chartInstance.options.horizontalLine.length; index++) {
						line = chartInstance.options.horizontalLine[index];

						if (!line.style) {
							style = "rgba(169,169,169, .6)";
						} else {
							style = line.style;
						}

						if (line.y) {
							yValue = yScale.getPixelForValue(line.y);
						} else {
							yValue = 0;
						}

						ctx.lineWidth = 3;

						if (yValue) {
							ctx.beginPath();
							ctx.moveTo(0, yValue);
							ctx.lineTo(canvas.width, yValue);
							ctx.strokeStyle = style;
							ctx.stroke();
						}

						if (line.text) {
							ctx.fillStyle = style;
							ctx.fillText(line.text, 0, yValue + ctx.lineWidth);
						}
					}
					return;
				};
			}
		};
		Chart.pluginService.register(horizonalLinePlugin);

		var data = {
			labels: [<?php echo $labels; ?>],
			datasets: [{
				label: false,
				fill: false,
				lineTension: 0,
				backgroundColor: "rgba(75,192,192,0.4)",
				borderColor: "rgba(75,192,192,1)",
				borderCapStyle: 'butt',
				borderDash: [],
				borderDashOffset: 0.0,
				borderJoinStyle: 'bevel',
				pointBorderColor: "rgba(75,192,192,1)",
				pointBackgroundColor: "#fff",
				pointBorderWidth: 1,
				pointHoverRadius: 4,
				pointHoverBackgroundColor: "rgba(75,192,192,1)",
				pointHoverBorderColor: "rgba(220,220,220,1)",
				pointHoverBorderWidth: 2,
				pointRadius: 4,
				pointHitRadius: 0,
				data: [<?php echo $debits; ?>],
			}]
		};

		var myChart = new Chart(ctx, {
			type: 'line',
			data: data,
			options: {
				legend: {
					display: false
				},
				"horizontalLine": [{
					"y": 3000,
					"style": "rgba(255, 0, 0, .4)",
					// "text": "max",
				}]
			}
		});
	</script>
<?php } ?>