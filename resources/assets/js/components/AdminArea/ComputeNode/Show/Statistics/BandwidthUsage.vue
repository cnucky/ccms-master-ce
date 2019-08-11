<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "BandwidthUsage",
        props: [
            "bandwidthUsages",
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
                            return chartObject.datasets[item.datasetIndex].label + " " + item.yLabel + ' Mbps'
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
                                return item + ' Mbps';
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
                if (this.bandwidthUsages.length)
                    labels.push(this.$moment.unix(this.bandwidthUsages[0].microtime).local().format('YYYY-MM-DD HH:mm:ss'));
                if (this.bandwidthUsages.length > 1)
                    labels.push(this.$moment.unix(this.bandwidthUsages[this.bandwidthUsages.length - 1].microtime).local().format('YYYY-MM-DD HH:mm:ss'));
                return labels;
            },
            dataset: function () {
                var data = [];
                var nicDevice;
                for (var index in this.bandwidthUsages) {
                    nicDevice = this.bandwidthUsages[index].network_device;
                    if (typeof data[nicDevice] === "undefined")
                        data[nicDevice] = [];

                    data[nicDevice].push({
                        x: this.$moment.unix(this.bandwidthUsages[index].microtime).local().format('YYYY-MM-DD HH:mm:ss'),
                        y: (this.bandwidthUsages[index][this.type] * 8 / 1048576).toFixed(2),
                    });
                }

                var dataset = [];

                var color = 0xbd1550;
                var step = 0x1634d9;

                for (nicDevice in data) {
                    color += step;
                    dataset.push({
                        label: nicDevice,
                        borderColor: "#" + color.toString(16),
                        backgroundColor: "#" + color.toString(16),
                        borderWidth: 1,
                        data: data[nicDevice],
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