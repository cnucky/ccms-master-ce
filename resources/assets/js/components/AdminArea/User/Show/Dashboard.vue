<template>
    <div class="ui gird">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <div class="ui five column grid">
                    <column name="姓名">{{ userData.user.name }}</column>
                    <column name="Email">{{ userData.user.email }}</column>
                    <column name="联系电话"><phone :country="userData.user.country" :phone="userData.user.phone"></phone></column>
                    <column name="注册时间"><local-time :time="userData.user.created_at"></local-time></column>
                    <column name="状态"><status-label :status="userData.user.status"></status-label></column>
                </div>
                <div class="ui section divider"></div>
                <div class="ui four column grid">
                    <column name="计算实例" :inline="true">{{ userData.user.instances_count }}</column>
                    <column name="本地卷" :inline="true">{{ userData.user.local_volumes_count }}</column>
                    <column name="IPv4" :inline="true">{{ userData.user.ipv4s_count }}</column>
                    <column name="IPv6" :inline="true">{{ userData.user.ipv6s_count }}</column>
                </div>
                <div class="ui section divider"></div>
                <div class="ui four column grid">
                    <column name="余额" :inline="true"><amount :amount="userData.user.credit"></amount></column>
                    <column name="已冻结余额" :inline="true"><amount :amount="userData.user.frozen_credit"></amount></column>
                    <column name="本月消费" :inline="true"><amount :amount="userData.currentMonthTotalChargedAmount"></amount></column>
                    <column name="历史净充值" :inline="true"><amount :amount="userData.netIncoming"></amount></column>
                </div>
                <div class="ui section divider"></div>
                <div class="ui one column grid">
                    <column name="本月流量使用">
                        <table class="ui unstackable table">
                            <thead>
                            <tr>
                                <th>类型</th>
                                <th>RX</th>
                                <th>TX</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="(trafficShareGroupTrafficUsages, trafficShareGroupId) in trafficUsages">
                                <tr>
                                    <td colspan="3" style="text-align: center;">
                                        <b>
                                            <template v-if="userData.trafficShareGroups.hasOwnProperty(trafficShareGroupId)">
                                                流量组：{{ userData.trafficShareGroups[trafficShareGroupId].name }}
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
                                        {{ $convertResult2Text($convert2HumanReadableUnit(trafficUsagesInGroup.total_tx_byte_count), "ytes") }}
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </column>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "../../../ClientArea/ComputeInstance/Show/Information/Column";
    import Phone from "../Phone";
    import LocalTime from "../../../LocalTime";
    import StatusLabel from "../StatusLabel";
    import Amount from "../../../CreditRecord/Amount";
    import Convert2HumanReadableUnit from "./../../../Convert2HumanReadableUnit";

    export default {
        name: "Dashboard",
        mixins: [Convert2HumanReadableUnit],
        components: {Amount, StatusLabel, LocalTime, Phone, Column},
        props: ["userData"],
        computed: {
            trafficUsages: function () {
                var trafficUsages = {};
                for (var i in this.userData.currentMonthTrafficUsages) {
                    var trafficUsage = this.userData.currentMonthTrafficUsages[i];
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
            }
        }
    }
</script>

<style scoped>

</style>