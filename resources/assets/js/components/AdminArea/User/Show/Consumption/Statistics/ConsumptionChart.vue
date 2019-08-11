<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        name: "ConsumptionChart",
        props: [
            "consumption",
            "type",
        ],
        mounted: function () {
            var vueInstance = this;
            this.renderChart({
                labels: this.labels,
                datasets: this.dataset,
            }, {
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: (item, chartObject) => {
                            return chartObject.datasets[item.datasetIndex].label + " " + item.yLabel + ' ' + vueInstance.$store.getters.defaultCurrency.code;
                        },
                    },
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
                            userCallback: function(item) {
                                return item + ' ' + vueInstance.$store.getters.defaultCurrency.code;
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
                if (typeof this.type === "undefined") {
                    for (var i in this.consumption.total_consumption) {
                        labels.push(this.consumption.total_consumption[i].time);
                    }
                } else {
                    for (var i in this.consumption.total_consumption_group_by_type) {
                        if (this.consumption.total_consumption_group_by_type[i].type === this.type) {
                            labels.push(this.consumption.total_consumption_group_by_type[i].time);
                        }
                    }
                }
                return labels;
            },
            dataset: function () {
                var data = [];

                if (typeof this.type === "undefined") {
                    data["Total"] = [];
                    var totalConsumption;
                    for (var i in this.consumption.total_consumption) {
                        totalConsumption = this.consumption.total_consumption[i];
                        data["Total"].push({
                            x: totalConsumption.time,
                            y: Math.abs(parseFloat(totalConsumption.total_amount)),
                        });
                    }
                } else {
                    for (var i in this.consumption.total_consumption_group_by_type) {
                        totalConsumption = this.consumption.total_consumption_group_by_type[i];
                        var type = totalConsumption.type;
                        if (type !== this.type)
                            continue;
                        if (typeof data[type] === "undefined")
                            data[type] = [];
                        data[type].push({
                            x: totalConsumption.time,
                            y: Math.abs(parseFloat(totalConsumption.total_amount)),
                        });
                    }
                }

                var dataset = [];

                for (type in data) {
                    dataset.push({
                        label: this.$te("creditRecordType." + type) ? this.$t("creditRecordType." + type) : type,
                        borderColor: '#05cce1',
                        backgroundColor: '#05cce1',
                        borderWidth: 1,
                        data: data[type],
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