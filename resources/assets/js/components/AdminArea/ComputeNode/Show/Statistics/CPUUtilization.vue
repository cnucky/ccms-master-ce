<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "CPUUtilization",
        props: [
            "cpuUsages",
        ],
        mounted: function () {
            this.renderChart({
                labels: this.labels,
                datasets: [
                    {
                        label: 'CPU Utilization',
                        borderColor: '#05cce1',
                        backgroundColor: '#05cce1',
                        borderWidth: 1,
                        data: this.cpuUsageData,
                        fill: false,
                    }
                ]
            }, {
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: (item) => item.yLabel + '%',
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
                                return item + '%';
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
                for (var i in this.cpuUsages) {
                    labels.push(this.$moment.unix(this.cpuUsages[i].microtime).local().format('YYYY-MM-DD HH:mm:ss'));
                }
                return labels;
            },
            cpuUsageData: function () {
                var data = [];
                var utilization;
                for (var i in this.cpuUsages) {
                    utilization = this.cpuUsages[i].user + this.cpuUsages[i].nice + this.cpuUsages[i].system + this.cpuUsages[i].irq + this.cpuUsages[i].softirq + this.cpuUsages[i].steal;
                    data.push(utilization.toFixed(2));
                }
                return data;
            }
        },
    }
</script>

<style scoped>

</style>