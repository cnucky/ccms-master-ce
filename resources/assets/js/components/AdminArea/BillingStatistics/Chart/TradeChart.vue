<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "TradeChart",
        props: [
            "dateList", "totalPaid", "totalRefunded", "dailyNetIncoming",
        ],
        mounted: function () {
            var labels = [];
            var paidData = [];
            var refundedData = [];
            var netIncomingData = [];
            var i;
            for (i in this.dateList) {
                var date = this.dateList[i];
                labels.push(date);
                paidData.push(this.totalPaid.hasOwnProperty(date) ? this.totalPaid[date] : "0");
                refundedData.push(this.totalRefunded.hasOwnProperty(date) ? this.totalRefunded[date] : "0");
                netIncomingData.push(this.dailyNetIncoming.hasOwnProperty(date) ? this.dailyNetIncoming[date] : "0");
            }

            this.renderChart({
                labels: labels,
                datasets: [
                    {
                        label: '充值',
                        borderColor: '#fc2525',
                        backgroundColor: '#fc2525',
                        borderWidth: 1,
                        // backgroundColor: this.gradient,
                        data: paidData,
                        fill: false,
                    },
                    {
                        label: '退款',
                        borderColor: '#05Cbe1',
                        backgroundColor: '#05Cbe1',
                        borderWidth: 1,
                        // backgroundColor: this.gradient2,
                        data: refundedData,
                        fill: false,
                    },
                    {
                        label: '净收入',
                        borderColor: '#9e2cc6',
                        backgroundColor: '#9e2cc6',
                        borderWidth: 1,
                        // backgroundColor: this.gradient2,
                        data: netIncomingData,
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
    }
</script>