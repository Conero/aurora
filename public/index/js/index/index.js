/**
 * Created by Administrator on 2017/5/11 0011.
 */
$(function () {
    function drawVchart(rdata) {
        // echart 构图
        var vchart = echarts.init(document.getElementById('visit_chart'));
        var option = {
            animation: false,
            title: {
                left: 'center',
                text: '访问统计曲线图'
            },
            toolbox: {
                itemSize: 25,
                top: 55,
                feature: {
                    mark : {show: true},
                    dataView : {show: true, readOnly: false},
                    dataZoom: {
                        yAxisIndex: 'none'
                    },
                    restore: {},
                    saveAsImage : {show: true}
                }
            },
            yAxis: {
                type: 'value',
                axisTick: {
                    inside: true
                },
                splitLine: {
                    show: false
                },
                axisLabel: {
                    inside: true,
                    formatter: '{value}\n'
                },
                z: 10
            },
            xAxis: {
                data: rdata.xAxis
            },
            series: [
                {
                    name: '全部',
                    smooth: true,
                    symbol: 'circle',
                    symbolSize: 5,
                    sampling: 'average',
                    itemStyle: {
                        normal: {
                            color: '#8ec6ad'
                        }
                    },
                    type: 'line',
                    data: rdata.series[0]
                },
                {
                    name: '移动端',
                    smooth: true,
                    symbol: 'circle',
                    symbolSize: 5,
                    sampling: 'average',
                    itemStyle: {
                        normal: {
                            color: '#d68262'
                        }
                    },
                    type: 'line',
                    data: rdata.series[1]
                }
            ]
        };
        vchart.setOption(option);
    }
    function drawTiduChart(rdata) {
        $.get(Web._baseurl+'public/echart/source/china.json', function (chinaJson) {
            echarts.registerMap('china', chinaJson);
            var chart = echarts.init(document.getElementById('visit_ditu_chrart'));
            chart.setOption({
                title: {
                    left: 'center',
                    text: '访问地区分布图'
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data:['地区分布']
                },
                visualMap: {
                    min: 0,
                    max: 2500,
                    left: 'left',
                    top: 'bottom',
                    text: ['高','低'],           // 文本，默认为数值文本
                    calculable: true
                },
                toolbox: {
                    show: true,
                    orient: 'vertical',
                    left: 'right',
                    top: 'center',
                    feature: {
                        restore: {},
                        saveAsImage: {}
                    }
                },
                series: [{
                    name:'地区分布',
                    type: 'map',
                    map: 'china',
                    roam: false,
                    data:rdata
                }]
            });
        });
    }
    Web.ApiRequest('index/visit_count',null,function (rdata) {
        drawVchart(rdata);
    });
    Web.ApiRequest('visit/getDistributionCtt',null,function (rdata) {
       drawTiduChart(rdata);
    });
    //drawTiduChart();
    $('.js__tooltip').tooltip();
    // toggle 显示
    $('.js__toggle').click(function () {
       var id = $(this).attr("data-id");
        $('#'+id).toggleClass('d-none');
    });
});