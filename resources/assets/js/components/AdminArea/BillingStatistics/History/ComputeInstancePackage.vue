<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: historyData === null}">
                <date-input v-on:query="query"></date-input>
                <div class="ui section divider"></div>
                <template v-if="historyData">
                    <div style="text-align: center;">{{ historyData.start }} - {{ historyData.end }}</div>
                    <table class="ui very basic table">
                        <thead>
                        <tr>
                            <th>规格</th>
                            <th>总收费</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="computeInstancePackage in historyData.computeInstancePackages">
                            <td>{{ computeInstancePackage.category.name }} - {{ computeInstancePackage.name }}</td>
                            <td><amount :amount="computeInstancePackage.charge_histories_sum"></amount></td>
                        </tr>
                        <tr>
                            <td>已删除规格</td>
                            <td><amount :amount="historyData.deleted"></amount></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><b>总计</b></th>
                            <th><amount :amount="totalCharged"></amount></th>
                        </tr>
                        </tfoot>
                    </table>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import Amount from "../../../CreditRecord/Amount";
    import DateInput from "../Statistics/DateInput";
    export default {
        name: "ComputeInstancePackage",
        components: {DateInput, Amount},
        data: function () {
            return {
                historyData: null,
            };
        },
        created: function () {
            this.query();
        },
        methods: {
            query: function (start = "", end = "") {
                this.$axiosGet(route(this.routeName, {start: start, end: end}), (data) => {
                    this.historyData = data;
                })
            }
        },
        computed: {
            totalCharged: function () {
                var total = new Decimal("0.0000");
                for (var i in this.historyData.computeInstancePackages) {
                    var computeInstancePackage = this.historyData.computeInstancePackages[i];
                    total = total.add(computeInstancePackage.charge_histories_sum);
                }
                total = total.add(this.historyData.deleted);
                return total.toString();
            }
        }
    }
</script>

<style scoped>

</style>