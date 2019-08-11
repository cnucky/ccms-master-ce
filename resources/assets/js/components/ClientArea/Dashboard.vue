<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">总览</h1>
        </div>

        <div v-if="!dashboardData" class="sixteen wide column">
            <div class="ui very padded no-shadow segment loading">
            </div>
        </div>

        <template v-else>
            <div class="sixteen wide column">
                <div class="ui very padded no-shadow segment">
                    <div v-if="parseFloat(dashboardData.credit) < 0" class="ui negative message">
                        您的帐号当前处于欠费状态，请尽快<router-link :to="{name: 'billing.addCredit'}">充值</router-link><template v-if="releaseAt">，否则账号下的所有资源将在{{ releaseAt }}后自动释放</template>。
                    </div>

                    <h3 class="ui header">配额</h3>
                    <div class="ui four column grid">
                        <column name="实例（个）" :inline="true" :dt-style="dtStyle">
                            {{ this.dashboardData.computeInstanceCount }} / {{ getUserQuotaValue("max_instance")  }}
                        </column>
                        <column name="本地卷（GiB）" :inline="true" :dt-style="dtStyle">
                            {{ this.dashboardData.localVolumeTotalCapacity }} / {{ getUserQuotaValue("max_storage_capacity_in_gib_unit") }}
                        </column>
                        <column name="弹性IPv4（个）" :inline="true" :dt-style="dtStyle">
                            {{ this.dashboardData.ipv4Count }} / {{ getUserQuotaValue("max_elastic_ipv4_block")  }}
                        </column>
                        <column name="弹性IPv6（个）" :inline="true" :dt-style="dtStyle">
                            {{ this.dashboardData.ipv6Count }} / {{ getUserQuotaValue("max_elastic_ipv6_block") }}
                        </column>
                    </div>

                    <div class="ui section divider"></div>

                    <h3 class="ui header">当前消费详情</h3>
                    <table class="ui unstackable fixed table">
                        <thead>
                        <tr>
                            <th>项目</th>
                            <th>价格</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>计算实例</td>
                            <td><amount :amount="dashboardData.instanceTotalPrice"></amount>/hour</td>
                        </tr>
                        <tr>
                            <td>本地卷</td>
                            <td><amount :amount="dashboardData.localVolumeTotalPrice"></amount>/hour</td>
                        </tr>
                        <tr>
                            <td>IPv4</td>
                            <td><amount :amount="dashboardData.ipv4TotalPrice"></amount>/hour</td>
                        </tr>
                        <tr>
                            <td>IPv6</td>
                            <td><amount :amount="dashboardData.ipv6TotalPrice"></amount>/hour</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>总计</th>
                            <th>
                                <amount :amount="totalPricePerHour.toString()"></amount>/hour &nbsp; (<amount :amount="totalPricePerHour.mul(720).toString()"></amount>/month )
                            </th>
                        </tr>
                        </tfoot>
                    </table>

                    <div class="ui section divider"></div>

                    <h3 class="ui header">本月流量使用</h3>
                    <table class="ui unstackable table">
                        <thead>
                        <tr>
                            <th>类型</th>
                            <th>RX</th>
                            <th>TX</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-if="Object.keys(trafficUsages).length">
                            <template v-for="(trafficShareGroupTrafficUsages, trafficShareGroupId) in trafficUsages">
                            <tr>
                                <td colspan="3" style="text-align: center;">
                                    <b>
                                        <template v-if="dashboardData.trafficShareGroups.hasOwnProperty(trafficShareGroupId)">
                                            流量组：{{ dashboardData.trafficShareGroups[trafficShareGroupId].name }}
                                        </template>
                                        <template v-else>
                                            已删除流量组
                                        </template>
                                    </b>
                                </td>
                            </tr>
                            <tr v-for="trafficUsagesInGroup in trafficShareGroupTrafficUsages">
                                <td>
                                    <template v-if="trafficUsagesInGroup.network_type === 0">公网</template>
                                    <template v-else-if="trafficUsagesInGroup.network_type === 1">内网</template>
                                    <template v-else>未知</template>
                                </td>
                                <td>
                                    {{ $convertResult2Text($convert2HumanReadableUnit(trafficUsagesInGroup.total_rx_byte_count), "ytes") }}
                                </td>
                                <td>
                                    {{ $convertResult2Text($convert2HumanReadableUnit(trafficUsagesInGroup.total_tx_byte_count), "ytes") }}<template v-if="trafficUsagesInGroup.network_type === 0 && dashboardData.freeTXTrafficAtEachShareGroup.hasOwnProperty(trafficShareGroupId)"> / {{dashboardData.freeTXTrafficAtEachShareGroup[trafficShareGroupId]}} GBytes</template>
                                </td>
                            </tr>
                        </template>
                        </template>
                        <tr v-else>
                            <td colspan="3" class="center aligned">无记录</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="ui section divider"></div>

                    <h3 class="ui header">本月消费详情</h3>
                    <div class="ui two column grid" v-if="dashboardData.currentMonthChargedAmountGroupByType.length">
                        <div class="ui column">
                            <consumption-piechart :consumption-data="dashboardData.currentMonthChargedAmountGroupByType"></consumption-piechart>
                        </div>
                        <div class="ui column">
                            <table class="ui very basic table">
                                <tbody>
                                <tr v-for="consumption in dashboardData.currentMonthChargedAmountGroupByType">
                                    <td>{{ $t("creditRecordType." + consumption.type) }}</td>
                                    <td><amount :amount="consumption.total_amount.replace('-', '')"></amount></td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>总计</th>
                                    <th><amount :amount="dashboardData.currentMonthTotalChargedAmount"></amount></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div v-else style="text-align: center;">无记录</div>
                </div>
            </div>
        </template>

    </div>
</template>

<script>
    import Convert2HumanReadableUnit from "./../Convert2HumanReadableUnit";

    import Column from "./ComputeInstance/Show/Information/Column";
    import ConsumptionPiechart from "./Dashboard/ConsumptionPiechart";
    import Amount from "../CreditRecord/Amount";
    export default {
        name: "Dashboard",
        mixins: [Convert2HumanReadableUnit],
        components: {Amount, ConsumptionPiechart, Column},
        data: function () {
            return {
                dtStyle: {
                    width: "80px;",
                },
                dashboardData: null,
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.$axiosGet(route("dashboard"), (data) => {
                    this.dashboardData = data;
                    this.$store.commit("userCredit", data.credit);
                    this.$store.commit("userFrozenCredit", data.frozenCredit);
                })
            },
            getUserQuotaValue: function (name) {
                if (this.dashboardData.userQuota && this.dashboardData.userQuota[name] !== null) {
                    return this.dashboardData.userQuota[name];
                }
                return "无限制";
            }
        },
        computed: {
            trafficUsages: function () {
                var trafficUsages = {};
                for (var i in this.dashboardData.currentMonthTrafficUsages) {
                    var trafficUsage = this.dashboardData.currentMonthTrafficUsages[i];
                    var trafficShareGroupId = trafficUsage.traffic_share_group_id;
                    if (trafficShareGroupId === null) {
                        trafficShareGroupId = 0;
                    }
                    if (!trafficUsages.hasOwnProperty(trafficShareGroupId)) {
                        trafficUsages[trafficShareGroupId] = {};
                    }
                    trafficUsages[trafficShareGroupId][trafficUsage.network_type] = trafficUsage;
                }
                return trafficUsages;
            },
            totalPricePerHour: function () {
                return new Decimal(this.dashboardData.instanceTotalPrice)
                    .add(this.dashboardData.localVolumeTotalPrice)
                    .add(this.dashboardData.ipv4TotalPrice)
                    .add(this.dashboardData.ipv6TotalPrice);
            },
            releaseAt: function () {
                if (this.dashboardData.inactive !== 1)
                    return false;
                if (this.dashboardData.start_lack_of_credit_at && this.dashboardData.autoReleaseAfter) {
                    return this.$moment(this.dashboardData.start_lack_of_credit_at).add(this.dashboardData.autoReleaseAfter, "hours").local().format("YYYY-MM-DD HH:mm");
                }
                return false;
            }
        }
    }
</script>

<style scoped>

</style>