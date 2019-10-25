function chart1(element,arr,title){
        var chart = null;
        var data = arr;
        // var data =[[1370131200000, 0.7695],[1370217600000, 0.7648],[1370304000000, 0.7645],[1370390400000, 0.7638],[1370476800000, 0.7549],[1370563200000, 0.7562],[1370736000000, 0.7574],[1370822400000, 0.7543],[1370908800000, 0.751]];

            chart = Highcharts.chart(element, {
                //版权信息
                credits: {
                    enabled: false
                },
                // legend: {
                //     backgroundColor: 'red',
                //     shadow: true,
                //     padding: 1
                // },
                chart: {
                    zoomType: 'x',
                    type: 'area',
                    // plotBorderColor: '#346691',
                    // plotBorderWidth: 2,
                    backgroundColor: 'rgba(0,0,0,0)'
                },
                title: {
                    text: title,
                    floating: true,
                    // y: 0,
                    style:{
                        color:"#4679bd",
                        "font-weight":"bold"
                    },
                },
                subtitle: {
                    text: document.ontouchstart === undefined ?
                    '鼠标拖动可以进行缩放' : '手势操作进行缩放',
                    y: 30,
                },
                // 分类x坐标
                xAxis: {
                    type: 'category',
                    visible:false
                    // dateTimeLabelFormats: {
                    //     millisecond: '%H:%M:%S.%L',
                    //     second: '%H:%M:%S',
                    //     minute: '%H:%M',
                    //     hour: '%H:%M',
                    //     day: '%m-%d',
                    //     week: '%m-%d',
                    //     month: '%Y-%m',
                    //     year: '%Y'
                    // }
                },

                // 提示框
                tooltip: {
                    enabled: true,
                    // dateTimeLabelFormats: {
                    //     millisecond: '%H:%M:%S.%L',
                    //     second: '%H:%M:%S',
                    //     minute: '%H:%M',
                    //     hour: '%H:%M',
                    //     day: '%Y-%m-%d',
                    //     week: '%m-%d',
                    //     month: '%Y-%m',
                    //     year: '%Y'
                    // }
                    pointFormatter: function() {
                        return '<span style="color: '+ this.series.color + '">\u25CF</span> '+this.series.name+': <b>'+ this.y +'</b><br/>'
                    },

                    pointFormatter: function() {
                        return '<span style="color: '+ this.series.color + '">\u25CF</span> '+this.series.name+': <b>'+ this.y +'</b><br/>'
                    },


                    // enabled:false//x坐标显示
                },
                yAxis: {
                    title: {
                        text: title
                    },
                    allowDecimals:Boolean,
                    // visible:false,//y坐标显示
                    // labels: {
                    //     formatter:function(){
                    //       if(this.value <=100) { 
                    //         return "第("+this.value*100+"%)";
                    //       }else if(this.value >100 && this.value <=200) { 
                    //         return "第("+this.value*100+"%)"; 
                    //       }else { 
                    //         return "第("+this.value*100+"%)";
                    //       }
                    //     }
                    // }

                },

                // 底部标识
                legend: {
                    enabled: true
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
                            ],
                            color: '#FF0000'
                        },
                        // marker: {
                        //     radius: 2
                        // },
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
                    name: title,
                    data: data,
                    // lineWidth:2,
                    // lineColor: '#FF0000',//线颜色
                    color: '#666666',//点颜色线颜色
                    // cursor: 'pointer',

                }],

                //是否允许下载
                navigation: {
                    buttonOptions: {
                        enabled: false
                    }
                },
            });
}


function chartzhu(element,data,title,unit=''){
    console.log(data)
    var x= [],y= [];
    for (var i = 0; i < data.length; i++) {
        x.push(data[i][0]);
        y.push(data[i][1]);
    }
    // var x = ['苹果', '橘子', '梨', '葡萄', '香蕉'];
    // var y = [5, 3, 4, 7, 2];
    var chart = Highcharts.chart(element,{
        //版权信息
        credits: {
            enabled: false
        },
        
        chart: {
            type: 'column'
        },
        title: {
            text: title
        },
        xAxis: {
            categories: x
        },
        series: [{
            name: title,
            data: y
        }]
    });    
}


function chartbing(element,data,title){
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

// 环形
function charthuan(element){
    var chart = Highcharts.chart(element, {
            chart: {
                    type: 'pie',
                    options3d: {
                            enabled: true,
                            alpha: 45
                    }
            },
            title: {
                    text: '简数科技每周水果消耗量'
            },
            subtitle: {
                    text: 'Highcharts 中的3D环形图'
            },
            plotOptions: {
                    pie: {
                            innerSize: 100,
                            depth: 45
                    }
            },
            series: [{
                    name: '货物金额',
                    data: [
                            ['香蕉', 8],
                            ['猕猴桃', 3],
                            ['桃子', 1],
                            ['橘子', 6],
                            ['苹果', 8],
                            ['梨', 4],
                            ['柑橘', 4],
                            ['橙子', 1],
                            ['葡萄 (串)', 1]
                    ]
            }]
    });
}

function chartiao(element,data,title){
    var x= [],y= [];
    for (var i = 0; i < data.length; i++) {
        x.push(data[i][0]);
        y.push(data[i][1]);
    }

    var chart = Highcharts.chart(element, {
    //版权信息
    credits: {
        enabled: false
    },
        
    chart: {
        type: 'bar'
    },
    title: {
        text: title
    },
    // subtitle: {
    //     text: '数据来源: Wikipedia.org'
    // },
    xAxis: {
        categories: x,
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            // text: '人口总量 (百万)',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        // valueSuffix: ' 百万'
    },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true,
                allowOverlap: true // 允许数据标签重叠
            }
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'top',
        x: -40,
        y: 100,
        floating: true,
        borderWidth: 1,
        backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
        shadow: true,
        enabled: true
    },
    series: [{
        name: title,
        data: y
    }]
});
}