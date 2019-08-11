<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "TrafficUsage",
        props: [
            "trafficUsages",
        ],
        mounted: function () {
            this.gradient = this.$refs.canvas.getContext('2d').createLinearGradient(0, 0, 0, 450);
            this.gradient2 = this.$refs.canvas.getContext('2d').createLinearGradient(0, 0, 0, 450);

            this.gradient2.addColorStop(0, 'rgba(0, 231, 255, 0.9)');
            this.gradient2.addColorStop(0.5, 'rgba(0, 231, 255, 0.25)');
            this.gradient2.addColorStop(1, 'rgba(0, 231, 255, 0)');

            this.gradient.addColorStop(0, 'rgba(255, 0,0, 0.5)');
            this.gradient.addColorStop(0.5, 'rgba(255, 0, 0, 0.25)');
            this.gradient.addColorStop(1, 'rgba(255, 0, 0, 0)');

            this.renderChart({
                labels: this.labels,
                datasets: [
                    {
                        label: 'RX',
                        borderColor: '#FC2525',
                        borderWidth: 1,
                        backgroundColor: this.gradient,
                        data: this.rxData,
                    },
                    {
                        label: 'TX',
                        borderColor: '#05CBE1',
                        borderWidth: 1,
                        backgroundColor: this.gradient2,
                        data: this.txData,
                    },
                ]
            }, {
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: (item) => {
                            var prefix = "RX ";
                            if (item.datasetIndex === 1)
                                prefix = "TX ";
                            return prefix + item.yLabel + ' MiB'
                        },
                    },
                },
                scales:{
                    xAxes: [{
                        type: 'time',
                        time: {
                            displayFormats: {
                                minute: 'HH:mm'
                            }
                        },
                        ticks: {
                            autoSkip: true,
                        },
                    }],
                    yAxes: [{
                        ticks: {
                            suggestedMin: 0,
                            userCallback: function(item) {
                                return item + ' MiB';
                            },
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
        computed: {
            labels: function () {
                var labels = [];
                for (var i in this.trafficUsages) {
                    labels.push(this.$moment.unix(this.trafficUsages[i].microtime).local().format('YYYY-MM-DD HH:mm:ss'));
                }
                return labels;
            },
            rxData: function () {
                var rates = [];
                for (var i in this.trafficUsages) {
                    rates.push((this.trafficUsages[i].rx_byte_count / 1048576).toFixed(2));
                }
                return rates;
            },
            txData: function () {
                var rates = [];
                for (var i in this.trafficUsages) {
                    rates.push((this.trafficUsages[i].tx_byte_count / 1048576).toFixed(2));
                }
                return rates;
            },
        },
    }
</script>

<style scoped>

</style>