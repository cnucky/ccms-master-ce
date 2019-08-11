<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "DiskIOUsage",
        props: [
            "diskIOUsages",
            "type",
        ],
        mounted: function () {
            this.renderChart({
                labels: this.labels,
                datasets: this.dataset,
            }, {
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: (item, chartObject) => {
                            return chartObject.datasets[item.datasetIndex].label + " " + item.yLabel + ' MiB/s'
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
                                return item + ' MiB/s';
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
                if (this.diskIOUsages.length)
                    labels.push(this.$moment.unix(this.diskIOUsages[0].microtime).local().format('YYYY-MM-DD HH:mm:ss'));
                if (this.diskIOUsages.length > 1)
                    labels.push(this.$moment.unix(this.diskIOUsages[this.diskIOUsages.length - 1].microtime).local().format('YYYY-MM-DD HH:mm:ss'));
                return labels;
            },
            dataset: function () {
                var data = [];
                var blockDevice;
                for (var index in this.diskIOUsages) {
                    blockDevice = this.diskIOUsages[index].block_device;
                    if (typeof data[blockDevice] === "undefined")
                        data[blockDevice] = [];

                    data[blockDevice].push({
                        x: this.$moment.unix(this.diskIOUsages[index].microtime).local().format('YYYY-MM-DD HH:mm:ss'),
                        y: (this.diskIOUsages[index][this.type] / 1048576).toFixed(2),
                    });
                }

                var dataset = [];

                var color = 0xbd1550;
                var step = 0x1634d9;

                for (blockDevice in data) {
                    color += step;
                    dataset.push({
                        label: blockDevice,
                        borderColor: "#" + color.toString(16),
                        backgroundColor: "#" + color.toString(16),
                        borderWidth: 1,
                        data: data[blockDevice],
                        fill: false,
                    });
                }

                return dataset;
            }
        },
    }
</script>

<style scoped>

</style>