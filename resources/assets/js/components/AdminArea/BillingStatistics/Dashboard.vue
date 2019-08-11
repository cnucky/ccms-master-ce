<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <template v-if="statisticsData">
                    <div class="ui four column grid">
                        <column name="今日总充值" :inline="true">
                            <amount :amount="statisticsData.todayTotalAddCredit"></amount>
                        </column>
                        <column name="今日总退款" :inline="true">
                            <amount :amount="statisticsData.totalTotalRefund" force-color="red"></amount>
                        </column>
                        <column name="今日净收入" :inline="true">
                            <amount :amount="netIncoming"></amount>
                        </column>
                        <column name="用户总欠费金额" :inline="true">
                            <amount :amount="statisticsData.unpaid" force-color="red"></amount>
                        </column>
                    </div>

                    <div class="ui section divider"></div>

                    <h3 class="ui header">计算实例</h3>
                    <div class="ui four column grid">
                        <column name="总数" :inline="true">{{ statisticsData.instanceCount }}</column>
                        <column name="每小时收入" :inline="true"><amount :amount="statisticsData.instanceHourlyIncoming"></amount></column>
                        <column name="每日收入" :inline="true"><amount :amount="instanceDailyIncoming"></amount></column>
                        <column name="今日已收费" :inline="true"><amount :amount="statisticsData.instanceTodayTotalCharged"></amount></column>
                    </div>

                    <div class="ui section divider"></div>
                    <h3 class="ui header">本地卷</h3>
                    <div class="ui four column grid">
                        <column name="总数" :inline="true">
                            {{ statisticsData.localVolumeCount }} ({{ statisticsData.localVolumeCapacityCount }} GiB)
                        </column>
                        <column name="每小时收入" :inline="true"><amount :amount="statisticsData.localVolumeHourlyIncoming"></amount></column>
                        <column name="每日收入" :inline="true"><amount :amount="localVolumeDailyIncoming"></amount></column>
                        <column name="今日已收费" :inline="true"><amount :amount="statisticsData.localVolumeTodayTotalCharged"></amount></column>
                    </div>

                    <div class="ui section divider"></div>
                    <h3 class="ui header">弹性IPv4</h3>
                    <div class="ui four column grid">
                        <column name="总数" :inline="true">{{ statisticsData.elasticIPv4Count }}</column>
                        <column name="每小时收入" :inline="true"><amount :amount="statisticsData.elasticIPv4HourlyIncoming"></amount></column>
                        <column name="每日收入" :inline="true"><amount :amount="elasticIPv4DailyIncoming"></amount></column>
                        <column name="今日已收费" :inline="true"><amount :amount="statisticsData.elasticIPv4TodayTotalCharged"></amount></column>
                    </div>

                    <div class="ui section divider"></div>
                    <h3 class="ui header">弹性IPv6</h3>
                    <div class="ui four column grid">
                        <column name="总数" :inline="true">{{ statisticsData.elasticIPv6Count }}</column>
                        <column name="每小时收入" :inline="true"><amount :amount="statisticsData.elasticIPv6HourlyIncoming"></amount></column>
                        <column name="每日收入" :inline="true"><amount :amount="elasticIPv6DailyIncoming"></amount></column>
                        <column name="今日已收费" :inline="true"><amount :amount="statisticsData.elasticIPv6TodayTotalCharged"></amount></column>
                    </div>

                    <div class="ui section divider"></div>
                    <h3 class="ui header">今日公网流量</h3>
                    <div class="ui three column grid">
                        <column name="上行" :inline="true">{{ $convertResult2Text($convert2HumanReadableUnit(statisticsData.trafficTodayTotalUsed.total_tx_bytes), "ytes") }}</column>
                        <column name="下行" :inline="true">{{ $convertResult2Text($convert2HumanReadableUnit(statisticsData.trafficTodayTotalUsed.total_rx_bytes), "ytes") }}</column>
                        <column name="上行已收费" :inline="true"><amount :amount="statisticsData.trafficTodayTotalCharged"></amount></column>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "../../ClientArea/ComputeInstance/Show/Information/Column";
    import Amount from "../../CreditRecord/Amount";
    import Convert2HumanReadableUnit from "../../Convert2HumanReadableUnit";
    export default {
        name: "Dashboard",
        mixins: [Convert2HumanReadableUnit],
        components: {Amount, Column},
        data: function () {
            return {
                isLoading: false,
                statisticsData: null,
            };
        },
        created: function () {
            this.isLoading = true;
            this.$axiosGet(route("billing.statistics.dashboard"), (data) => {
                this.statisticsData = data;
            }, () => {
                this.isLoading = false;
            });
        },
        computed: {
            netIncoming: function () {
                return new Decimal(this.statisticsData.todayTotalAddCredit).sub(this.statisticsData.totalTotalRefund).toString();
            },
            instanceDailyIncoming: function () {
                return new Decimal(this.statisticsData.instanceHourlyIncoming).mul(24).toString();
            },
            localVolumeDailyIncoming: function () {
                return new Decimal(this.statisticsData.localVolumeTodayTotalCharged).mul(24).toString();
            },
            elasticIPv4DailyIncoming: function () {
                return new Decimal(this.statisticsData.elasticIPv4Count).mul(24).toString();
            },
            elasticIPv6DailyIncoming: function () {
                return new Decimal(this.statisticsData.elasticIPv6Count).mul(24).toString();
            }
        }
    }
</script>

<style scoped>

</style>