
			// let x = ["2007", "2008", "2009", "2010", "2011", "2012","2013", "2014", "2015", "2016", "2017"];
			// let y = [152, 222, 199, 287, 534, 709,1179, 1256, 1632, 1856, 1850];

			// let titlename = 'Events';

			// zhexiantu("#chart-composite-1",x,y,titlename);
			// bingzhuangtu("#chart-aggr",x,y)
			// xiaozhexian("#chart-trends",x,y);
			// zhuzhuangtu("#chart-events",x,y);
			


			function zhexiantu(element,x,y,titlename){
				let lineCompositeData = {
				  labels: x,
				  datasets: [{
				    "name": titlename,
				    "values": y
				  }]
				};

				let c1 = document.querySelector(element);

				let lineCompositeChart = new Chart (c1, {
					title: titlename,
					data: lineCompositeData,
					type: 'line',
					height: 190,
					colors: ['green'],
					isNavigable: 1,
					// valuesOverPoints: 1,

					lineOptions: {
						dotSize: 5
					},
					// yAxisMode: 'tick',
					// regionFill: 1
				});
			}

			function bingzhuangtu(element,x,y){
				let typeData = {
					labels: x,
					datasets: [
						{
							values: y,
						},
					]
				};
				let args = {
					data: typeData,
					type: 'pie',
					height: 250,
					colors: ['purple', 'magenta', 'light-blue'],

					maxLegendPoints: x.length,
					maxSlices: x.length,

					tooltipOptions: {
						formatTooltipX: d => (d + '').toUpperCase(),
						formatTooltipY: d => d + ' pts',
					}
				}
				// pie 三种状态
				//  percentage
				//  axis-mixed
				let newChart = new Chart(element, args);
			}



			function xiaozhexian(element,x,y,title){
// hideLine
// heatline
// regionFill
// hideDots
				let trends_data = {
					labels: x,
					datasets: [
						{
							values: y
						}
					]
				};

						let plotChartArgs = {
							title: title,
							data: trends_data,
							type: 'line',
							height: 250,
							colors: ['#238e38'],
							lineOptions: {
								hideDots: 1,
								heatline: 1,
							},
							axisOptions: {
								xAxisMode: 'tick',
								yAxisMode: 'span',
								xIsSeries: 1
							}
						};

						let type = 'regionFill';
						let config = {};
						config[type] = 1;

						if(['regionFill', 'heatline'].includes(type)) {
							config.hideDots = 1;
						}

						plotChartArgs.init = false;
						plotChartArgs.lineOptions = config;
						new Chart(element, plotChartArgs);

			}

// // let moon_names = ["Ganymede", "Callisto", "Io", "Europa"];
// // let distances = [1070.412, 1882.709, 421.700, 671.034];
		function zhuzhuangtu(element,x,y){

		let events_data = {
			labels:x,
			datasets: [
				{
					"values": y,
				}
			]
		};

		let events_chart = new Chart(element, {
			// title: "Jupiter's Moons: Semi-major Axis (1000 km)",
			data: events_data,
			type: 'bar',
			height: 250,
			colors: ['grey'],
			// isNavigable: 1,
		});

		}
