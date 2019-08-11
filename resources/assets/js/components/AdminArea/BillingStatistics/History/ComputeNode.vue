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
                            <th>节点</th>
                            <th>计算实例</th>
                            <th>本地卷</th>
                            <th>总</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="model in historyData.models">
                            <td><router-link :to="{name: 'zones.dashboard', params: {id: model.zone.id}}"><i v-bind:class="[model.zone.region.icon_class]"></i>{{ model.zone.region.name }} - {{ model.zone.name }}</router-link> - <router-link :to="{name: 'computeNodes.show', params: {id: model.id}}">{{ model.name }}</router-link></td>
                            <td><amount :amount="model.instance_charge_histories_sum"></amount></td>
                            <td><amount :amount="model.volume_charge_histories_sum"></amount></td>
                            <td><amount :amount="model.charge_histories_sum"></amount></td>
                        </tr>
                        <tr>
                            <td>已删除节点</td>
                            <td><amount :amount="historyData.deleted.instance_charge_histories_sum"></amount></td>
                            <td><amount :amount="historyData.deleted.volume_charge_histories_sum"></amount></td>
                            <td><amount :amount="historyData.deleted.charge_histories_sum"></amount></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><b>总计</b></th>
                            <th><amount :amount="computeInstanceTotalCharged"></amount></th>
                            <th><amount :amount="localVolumeTotalCharged"></amount></th>
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
        name: "ComputeNode",
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
            computeInstanceTotalCharged: function () {
                var total = new Decimal("0.0000");
                for (var i in this.historyData.models) {
                    var model = this.historyData.models[i];
                    total = total.add(model.instance_charge_histories_sum);
                }
                total = total.add(this.historyData.deleted.instance_charge_histories_sum);
                return total.toString();

            },
            localVolumeTotalCharged: function () {
                var total = new Decimal("0.0000");
                for (var i in this.historyData.models) {
                    var model = this.historyData.models[i];
                    total = total.add(model.volume_charge_histories_sum);
                }
                total = total.add(this.historyData.deleted.volume_charge_histories_sum);
                return total.toString();

            },
            totalCharged: function () {
                var total = new Decimal("0.0000");
                for (var i in this.historyData.models) {
                    var model = this.historyData.models[i];
                    total = total.add(model.charge_histories_sum);
                }
                total = total.add(this.historyData.deleted.charge_histories_sum);
                return total.toString();
            }
        }
    }
</script>

<style scoped>

</style>