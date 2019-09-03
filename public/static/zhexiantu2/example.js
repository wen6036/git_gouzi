function ptzxt(element,elevationData,title,xname,yname,xunit,yunit){
// Data generated from http://www.bikeforums.net/professional-cycling-fans/1113087-2017-tour-de-france-gpx-tcx-files.html
// var elevationData = [
//     [0.0, 225],
//     [0.1, 226],
//     [0.2, 228],
//     [0.3, 228],
//     [0.4, 229],
//     [0.5, 229],
//     [0.6, 230],
//     [0.7, 234],
//     [0.8, 235],
//     [0.9, 236],
//     [1.0, 235],
// ];
// highcharts-label highcharts-tooltip highcharts-color-undefined
// 初始化图标
var chart = Highcharts.chart(element, {
    chart: {
        type: 'area',
        zoomType: 'x',
        panning: true,
        panKey: 'shift'
    },
    title: {
        text: '标题'
    },


    xAxis: {
        labels: {
            format: '{value}' +yunit//x下方单位
        },
        minRange: 1,
        title: {
            text: '下方标题'
        }
    },
    yAxis: {
        startOnTick: true,
        endOnTick: false,
        maxPadding: 0.35,
        title: {
            text: null
        },
        labels: {
            format: '{value}'+xunit//y下方单位
        }
    },
    tooltip: {
        headerFormat: xname + ': {point.x:.1f}'+xunit+' <br>',
        pointFormat: yname + '：{point.y}'+yunit+' m ',
        shared: true
    },
    legend: {
        enabled: false
    },
    // Highcharts.getOptions().colors[10]
    series: [{
        data: elevationData,
        lineColor: '#4679bd',
        color: '#4679bd',
        fillOpacity: 0.3,
        // name: '海拔高度',
        marker: {
            enabled: false
        },
        threshold: null
    }]
});

}

function timezxt(element,data,title,xname,yname,xunit,yunit){
    console.log(data)
    var chart = Highcharts.chart('element', {
        chart: {
            zoomType: 'x1'
        },
        title: {
            text: 'title'
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
            '鼠标拖动可以进行缩放' : '手势操作进行缩放'
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
                millisecond: '%H:%M:%S.%L',
                second: '%H:%M:%S',
                minute: '%H:%M',
                hour: '%H:%M',
                day: '%m-%d',
                week: '%m-%d',
                month: '%Y-%m',
                year: '%Y'
            }
        },
        tooltip: {
            dateTimeLabelFormats: {
                millisecond: '%H:%M:%S.%L',
                second: '%H:%M:%S',
                minute: '%H:%M',
                hour: '%H:%M',
                day: '%Y-%m-%d',
                week: '%m-%d',
                month: '%Y-%m',
                year: '%Y',
            }
        },
        yAxis: {
            title: {
                text: yname
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        series: [{
            type: 'area',
            name: xname,
            data: data
        }]
    });
}

// 柱状图
function zzt(element,xdata,ydata,title,titlename){
	var chart = Highcharts.chart(element,{
		chart: {
			type: 'column'
		},
		title: {
			text: title
		},
		xAxis: {
			categories:xdata
		},
		series: [{
			name: titlename,
			data: ydata
		}]
	});

}

function unitzzt(element,xdata,ydata,title,titlename,titlename){

var chart = Highcharts.chart(element, {
	chart: {
		type: 'areaspline'
	},
	title: {
		text: title
	},
	legend: {
		layout: 'vertical',
		align: 'left',
		verticalAlign: 'top',
		x: 150,
		y: 100,
		floating: true,
		borderWidth: 1,
		backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
	},
	xAxis: {
		categories:xdata,
		// plotBands: [{ // 标识出周末
		// 	from: 4.5,
		// 	to: 6.5,
		// 	color: 'rgba(68, 170, 213, .2)'
		// }]
	},
	yAxis: {
		title: {
			text: titlename2
		}
	},
	tooltip: {
		shared: true,
		valueSuffix: ' %'
	},
	plotOptions: {
		areaspline: {
			fillOpacity: 0.5
		}
	},
	series: [{
		name: titlename,
		data: ydata
	}]
});

}

function bzt(element,data,title){

Highcharts.chart(element, {
	chart: {
		plotBackgroundColor: null,
		plotBorderWidth: null,
		plotShadow: false,
		type: 'pie'
	},
	title: {
		text: title
	},
	tooltip: {
		pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
	},
	plotOptions: {
		pie: {
			allowPointSelect: true,
			cursor: 'pointer',
			dataLabels: {
				enabled: true,
				format: '<b>{point.name}</b>: {point.percentage:.1f} %',
				style: {
					color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
				}
			}
		}
	},
	series: [{
		name: 'Brands',
		colorByPoint: true,
		data: data
	}]
});


}