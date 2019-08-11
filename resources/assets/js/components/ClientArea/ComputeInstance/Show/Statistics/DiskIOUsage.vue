<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "DiskIOUsage",
        props: [
            "diskIOUsages",
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
                        label: 'Read',
                        borderColor: '#FC2525',
                        borderWidth: 1,
                        backgroundColor: this.gradient,
                        data: this.readRates,
                    },
                    {
                        label: 'Write',
                        borderColor: '#05CBE1',
                        borderWidth: 1,
                        backgroundColor: this.gradient2,
                        data: this.writeRates,
                    },
                ]
            }, {
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: (item) => {
                            var prefix = "Read ";
                            if (item.datasetIndex === 1)
                                prefix = "Write ";
                            return prefix + item.yLabel + ' MiB/s'
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
                for (var i in this.diskIOUsages) {
                    labels.push(this.$moment.unix(this.diskIOUsages[i].microtime).local().format('YYYY-MM-DD HH:mm:ss'));
                }
                return labels;
            },
            readRates: function () {
                var rates = [];
                for (var i in this.diskIOUsages) {
                    rates.push((this.diskIOUsages[i].rd_bytes_per_second / 1048576).toFixed(2));
                }
                return rates;
            },
            writeRates: function () {
                var rates = [];
                for (var i in this.diskIOUsages) {
                    rates.push((this.diskIOUsages[i].wr_bytes_per_second / 1048576).toFixed(2));
                }
                return rates;
            },
        },
    }
</script>

<style scoped>

</style>