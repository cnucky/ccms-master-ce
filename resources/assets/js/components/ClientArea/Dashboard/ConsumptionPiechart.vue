<script>
    import {Pie} from 'vue-chartjs'

    export default {
        extends: Pie,
        name: "ConsumptionPiechart",
        props: ["consumptionData"],
        mounted () {
            var labels = [];
            var data = [];

            var consumptionData;
            for (var i in this.consumptionData) {
                consumptionData = this.consumptionData[i];
                labels.push(this.$t("creditRecordType." + consumptionData.type));
                data.push(Math.abs(parseFloat(consumptionData.total_amount)));
            }

            this.renderChart({
                labels: labels,
                datasets: [
                    {
                        backgroundColor: [
                            'rgba(65, 184, 131, .8)',
                            'rgba(228, 102, 81, .8)',
                            'rgba(0, 216, 255, .8)',
                            'rgba(155, 89, 182, .8)',
                            'rgba(20, 150, 150, .8)',
                            'rgba(100, 67, 204, .8)',
                        ],
                        borderWidth: 0,
                        data: data
                    }
                ]
            }, {responsive: true, maintainAspectRatio: false})
        },
    }
</script>