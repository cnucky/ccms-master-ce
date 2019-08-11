<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "MemoryUsage",
        props: [
            "memoryUsages",
        ],
        mounted: function () {
            this.renderChart({
                labels: this.labels,
                datasets: [
                    {
                        label: 'Memory Usage',
                        borderColor: '#05cce1',
                        backgroundColor: '#05cce1',
                        borderWidth: 1,
                        data: this.memoryUsageData,
                        fill: false,
                    }
                ]
            }, {
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: (item) => item.yLabel + ' GiB',
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
                            suggestedMax: this.memoryTotal,
                            userCallback: function(item) {
                                return item + ' GiB';
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
                for (var i in this.memoryUsages) {
                    labels.push(this.$moment.unix(this.memoryUsages[i].microtime).local().format('YYYY-MM-DD HH:mm:ss'));
                }
                return labels;
            },
            memoryUsageData: function () {
                var data = [];
                var memoryUsed;
                for (var i in this.memoryUsages) {
                    memoryUsed = (this.memoryUsages[i].total - this.memoryUsages[i].available) / 1048576;
                    data.push(memoryUsed.toFixed(2));
                }
                return data;
            },
            memoryTotal: function () {
                if (this.memoryUsages.length)
                    return parseInt(this.memoryUsages[this.memoryUsages.length - 1].total / 1048576) + 1;
                return 0;
            }
        },
    }
</script>

<style scoped>

</style>