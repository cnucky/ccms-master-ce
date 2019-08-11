<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">总览</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: dashboardData === null}">
                <template v-if="dashboardData">
                    <div class="ui four column grid">
                        <column name="用户" :inline="true">
                            <router-link :to="{name: 'users.index'}">{{ dashboardData.userCount }}</router-link>
                        </column>
                        <column name="计算实例" :inline="true">
                            <router-link :to="{name: 'admin.computeInstances.index'}">{{ dashboardData.instanceCount }}</router-link>
                        </column>
                        <column name="弹性IPv4" :inline="true">
                            <router-link :to="{name: 'ipv4.assignments.index'}">{{ dashboardData.elasticIPv4Count }}</router-link>
                        </column>
                        <column name="弹性IPv6" :inline="true">
                            <router-link :to="{name: 'ipv6.assignments.index'}">{{ dashboardData.elasticIPv6Count }}</router-link>
                        </column>
                    </div>
                    <div class="ui section divider"></div>
                    <div class="ui four column grid">
                        <column name="离线节点" :inline="true">
                            <router-link :to="{name: 'computeNodes.index'}">{{ dashboardData.offlineComputeNodeCount }}</router-link>
                        </column>
                        <column name="待处理工单" :inline="true">
                            <router-link :to="{name: 'admin.tickets.index'}">{{ dashboardData.awaitingReplyTicketCount }}</router-link>
                        </column>
                        <column name="待处理退款" :inline="true">
                            <router-link :to="{name: 'admin.paymentTradeRefunds.index', query: {status: 0}}">{{ dashboardData.pendingRefundTradeCount }}</router-link>
                        </column>
                        <column name="创建失败实例" :inline="true">
                            <router-link :to="{name: 'admin.computeInstances.index', query: {status: 8}}">{{ dashboardData.createUnsuccessfullyInstanceCount }}</router-link>
                        </column>
                        <column name="销毁失败实例" :inline="true">
                            <router-link :to="{name: 'admin.computeInstances.index', query: {status: 9}}">{{ dashboardData.destroyUnsuccessfullyInstanceCount }}</router-link>
                        </column>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "../ClientArea/ComputeInstance/Show/Information/Column";
    export default {
        name: "Dashboard",
        components: {Column},
        data: function () {
            return {
                dashboardData: null,
            };
        },
        created: function () {
            this.$axiosGet(route("admin.dashboard"), (data) => {
                this.dashboardData = data;
            })
        }
    }
</script>

<style scoped>

</style>