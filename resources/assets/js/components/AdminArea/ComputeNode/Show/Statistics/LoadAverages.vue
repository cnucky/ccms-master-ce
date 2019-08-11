<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "LoadAverage",
        props: [
            "loadAverages",
        ],
        mounted: function () {
            var loadAverageData = this.loadAverageData;

            this.renderChart({
                labels: this.labels,
                datasets: [
                    {
                        label: 'One minute average',
                        borderColor: '#fc2525',
                        backgroundColor: '#fc2525',
                        borderWidth: 1,
                        // backgroundColor: this.gradient,
                        data: loadAverageData[0],
                        fill: false,
                    },
                    {
                        label: 'Five minutes average',
                        borderColor: '#05Cbe1',
                        backgroundColor: '#05Cbe1',
                        borderWidth: 1,
                        // backgroundColor: this.gradient2,
                        data: loadAverageData[1],
                        fill: false,
                    },
                    {
                        label: 'Fifteen minutes average',
                        borderColor: '#9e2cc6',
                        backgroundColor: '#9e2cc6',
                        borderWidth: 1,
                        // backgroundColor: this.gradient2,
                        data: loadAverageData[2],
                        fill: false,
                    },
                ]
            }, {
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    /*
                    callbacks: {
                        label: (item) => {
                            var prefix = "RX ";
                            if (item.datasetIndex === 1)
                                prefix = "TX ";
                            return prefix + item.yLabel + ' Mbps'
                        },
                    },
                    */
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
                for (var i in this.loadAverages) {
                    labels.push(this.$moment.unix(this.loadAverages[i].microtime).local().format('YYYY-MM-DD HH:mm:ss'));
                }
                return labels;
            },
            loadAverageData: function () {
                var data = [[], [], []];
                for (var i in this.loadAverages) {
                    data[0].push(this.loadAverages[i].one_minute_average);
                    data[1].push(this.loadAverages[i].five_minutes_average);
                    data[2].push(this.loadAverages[i].fifteen_minutes_average);
                }
                return data;
            },
        },
    }
</script>

<style scoped>

</style>