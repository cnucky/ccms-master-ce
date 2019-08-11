<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "DiskSpaceUsage",
        props: [
            "diskSpaceUsages",
        ],
        mounted: function () {
            this.renderChart({
                labels: this.labels,
                datasets: [
                    {
                        label: 'Disk Space Usage',
                        borderColor: '#05cce1',
                        backgroundColor: '#05cce1',
                        borderWidth: 1,
                        data: this.diskSpaceUsageData,
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
                            suggestedMax: this.diskTotal,
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
                for (var i in this.diskSpaceUsages) {
                    labels.push(this.$moment.unix(this.diskSpaceUsages[i].microtime).local().format('YYYY-MM-DD HH:mm:ss'));
                }
                return labels;
            },
            diskSpaceUsageData: function () {
                var data = [];
                var memoryUsed;
                for (var i in this.diskSpaceUsages) {
                    memoryUsed = (this.diskSpaceUsages[i].total - this.diskSpaceUsages[i].free) / 1073741824;
                    data.push(memoryUsed.toFixed(2));
                }
                return data;
            },
            diskTotal: function () {
                if (this.diskSpaceUsages.length)
                    return parseInt(this.diskSpaceUsages[this.diskSpaceUsages.length - 1].total / 1073741824) + 1;
                return 0;
            }
        },
    }
</script>

<style scoped>

</style>