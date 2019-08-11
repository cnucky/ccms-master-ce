<template>
    <div class="ui grid">
        <div class="ui sixteen wide column" style="height: 300px;" v-if="isLoading">
            <semantic-ui-loader :is-active="isLoading" loader-class="large">Loading...</semantic-ui-loader>
        </div>
        <template v-else>
            <div class="ui sixteen wide column">
                <div class="ui padded no-shadow segment">
                    <h3 class="ui header">CPU使用率</h3>
                    <c-p-u-utilization :cpu-usages="this.statistics.cpuUsages" :height="130"></c-p-u-utilization>
                </div>
            </div>

            <div class="ui sixteen wide column">
                <div class="ui padded no-shadow segment">
                    <h3 class="ui header">平均负载</h3>
                    <load-average :load-averages="this.statistics.loadAverages" :height="130"></load-average>
                </div>
            </div>

            <div class="ui sixteen wide column">
                <div class="ui padded no-shadow segment">
                    <h3 class="ui header">硬盘读取速度</h3>
                    <disk-i-o-usage :disk-i-o-usages="this.statistics.diskIOUsages" type="read_bytes_per_second" :height="130"></disk-i-o-usage>
                </div>
            </div>

            <div class="ui sixteen wide column">
                <div class="ui padded no-shadow segment">
                    <h3 class="ui header">硬盘写入速度</h3>
                    <disk-i-o-usage :disk-i-o-usages="this.statistics.diskIOUsages" type="write_bytes_per_second" :height="130"></disk-i-o-usage>
                </div>
            </div>

            <div class="ui sixteen wide column">
                <div class="ui padded no-shadow segment">
                    <h3 class="ui header">网络下行速率</h3>
                    <bandwidth-usage :bandwidth-usages="this.statistics.bandwidthUsages" type="rx_bytes_per_second" :height="130"></bandwidth-usage>
                </div>
            </div>

            <div class="ui sixteen wide column">
                <div class="ui padded no-shadow segment">
                    <h3 class="ui header">网络上行速率</h3>
                    <bandwidth-usage :bandwidth-usages="this.statistics.bandwidthUsages" type="tx_bytes_per_second" :height="130"></bandwidth-usage>
                </div>
            </div>

            <div class="ui sixteen wide column">
                <div class="ui padded no-shadow segment">
                    <h3 class="ui header">内存使用量</h3>
                    <memory-usage :memory-usages="this.statistics.memoryUsages" :height="130"></memory-usage>
                </div>
            </div>

            <div class="ui sixteen wide column">
                <div class="ui padded no-shadow segment">
                    <h3 class="ui header">硬盘使用量</h3>
                    <disk-space-usage :disk-space-usages="this.statistics.diskSpaceUsages" :height="130"></disk-space-usage>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import CPUUtilization from "./Statistics/CPUUtilization";
    import LoadAverage from "./Statistics/LoadAverages";
    import DiskIOUsage from "./Statistics/DiskIOUsage";
    import BandwidthUsage from "./Statistics/BandwidthUsage";
    import MemoryUsage from "./Statistics/MemoryUsage";
    import DiskSpaceUsage from "./Statistics/DiskSpaceUsage";
    export default {
        name: "Statistics",
        components: {DiskSpaceUsage, MemoryUsage, BandwidthUsage, DiskIOUsage, LoadAverage, CPUUtilization},
        props: ["computeNode", "trustedCertificateInformation", "clientCertificateInformation", "serverTime", "nodeStatus"],
        data: function () {
            return {
                isLoading: true,
                statistics: [],
            };
        },
        created: function () {
            this.isLoading = true;
            axios.get(route("computeNodes.statistics", [this.computeNode.id]))
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.statistics = data.statistics;
                    }
                })
                .catch()
                .then(() => {
                    this.isLoading = false;
                })
            ;
        }
    }
</script>

<style scoped>

</style>