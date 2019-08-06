// Composite Chart
// ================================================================================

let reportCountList = [152, 222, 199, 287, 534, 709,
  1179, 1256, 1632, 1856, 1850];

let lineCompositeData = {
  labels: ["2007", "2008", "2009", "2010", "2011", "2012",
    "2013", "2014", "2015", "2016", "2017"],

  // yMarkers: [
  //  {
  //    label: "Average 100 reports/month",
  //    value: 1200,
  //  }
  // ],

  datasets: [{
    "name": "Events",
    "values": reportCountList
  }]
};

let c1 = document.querySelector("#chart-composite-1");

let lineCompositeChart = new Chart (c1, {
	title: "Fireball/Bolide Events - Yearly (reported)",
	data: lineCompositeData,
	type: 'line',
	height: 190,
	colors: ['green'],
	isNavigable: 1,
	valuesOverPoints: 1,

	lineOptions: {
		dotSize: 8
	},
	// yAxisMode: 'tick'
	// regionFill: 1
});



