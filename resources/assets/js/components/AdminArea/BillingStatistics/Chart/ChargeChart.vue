<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "ChargeChart",
        props: [
            "dateList", "statisticsData", "incomingKeyByDate",
        ],
        mounted: function () {
            var instanceChargeData = [];
            var localVolumeChargeData = [];
            var elasticIPv4ChargeData = [];
            var elasticIPv6ChargeData = [];
            var txTrafficChargeData = [];
            var incomingData = [];
            var i;
            for (i in this.dateList) {
                var date = this.dateList[i];
                instanceChargeData.push(this.propertyValueOrZero(this.statisticsData.instanceTotalCharged, date));
                localVolumeChargeData.push(this.propertyValueOrZero(this.statisticsData.localVolumeTotalCharged, date));
                elasticIPv4ChargeData.push(this.propertyValueOrZero(this.statisticsData.elasticIPv4TotalCharged, date));
                elasticIPv6ChargeData.push(this.propertyValueOrZero(this.statisticsData.elasticIPv6TotalCharged, date));
                txTrafficChargeData.push(this.propertyValueOrZero(this.statisticsData.txTrafficTotalCharged, date));
                incomingData.push(this.propertyValueOrZero(this.incomingKeyByDate, date));
            }

            var color = 0xbd1550;
            var step = 0x1634d9;

            this.renderChart({
                labels: this.dateList,
                datasets: [
                    {
                        label: '计算实例',
                        borderColor: '#fc2525',
                        backgroundColor: '#fc2525',
                        borderWidth: 1,
                        data: instanceChargeData,
                        fill: false,
                    },
                    {
                        label: '本地卷',
                        borderColor: '#05Cbe1',
                        backgroundColor: '#05Cbe1',
                        borderWidth: 1,
                        data: localVolumeChargeData,
                        fill: false,
                    },
                    {
                        label: '弹性IPv4',
                        borderColor: '#9e2cc6',
                        backgroundColor: '#9e2cc6',
                        borderWidth: 1,
                        data: elasticIPv4ChargeData,
                        fill: false,
                    },
                    {
                        label: '弹性IPv6',
                        borderColor: '#' + (color),
                        backgroundColor: '#' + (color),
                        borderWidth: 1,
                        data: elasticIPv6ChargeData,
                        fill: false,
                    },
                    {
                        label: '上行流量',
                        borderColor: '#' + (color += step),
                        backgroundColor: '#' + (color),
                        borderWidth: 1,
                        data: txTrafficChargeData,
                        fill: false,
                    },
                    {
                        label: '总计',
                        borderColor: '#ffe900',
                        backgroundColor: '#ffe900',
                        borderWidth: 1,
                        data: incomingData,
                        fill: false,
                    },
                ]
            }, {
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                scales:{
                    xAxes: [{
                        time: {
                            displayFormats: {
                                day: 'YYYY-MM-DD',
                                month: 'YYYY-MM',
                            }
                        },
                        ticks: {
                            autoSkip: true,
                        },
                    }],
                    yAxes: [{
                        ticks: {
                            suggestedMin: 0,
                        }
                    }]
                },
                elements: {
                    point: {
                        radius: 0
                    }
                }
            })
        },
        methods: {
            propertyValueOrZero: function (object, key) {
                return object.hasOwnProperty(key) ? object[key] : "0";
            }
        }
    }
</script>