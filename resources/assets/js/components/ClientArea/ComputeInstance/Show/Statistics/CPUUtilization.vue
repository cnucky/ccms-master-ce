<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "CPUUtilization",
        props: [
            "cpuUsages",
        ],
        mounted: function () {
            this.gradient2 = this.$refs.canvas.getContext('2d').createLinearGradient(0, 0, 0, 450);

            this.gradient2.addColorStop(0, 'rgba(0, 231, 255, 0.9)');
            this.gradient2.addColorStop(0.5, 'rgba(0, 231, 255, 0.25)');
            this.gradient2.addColorStop(1, 'rgba(0, 231, 255, 0)');

            this.renderChart({
                labels: this.labels,
                datasets: [
                    {
                        label: 'CPU Utilization',
                        borderColor: '#05CBE1',
                        borderWidth: 1,
                        backgroundColor: this.gradient2,
                        data: this.cpuUsageData,
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
                for (var i in this.cpuUsages) {
                    data.push(this.cpuUsages[i].cpu_usage.toFixed(2));
                }
                return data;
            }
        },
    }
</script>

<style scoped>

</style>