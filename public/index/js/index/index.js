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
                    dataZoom: {
                        yAxisIndex: 'none'
                    },
                    restore: {}
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
                    name: '销量',
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
                    name: '移动端访问量',
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
    Web.ApiRequest('index/visit_count',null,function (rdata) {
        drawVchart(rdata);
    });

});