<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <h3 class="ui header">CPU使用率</h3>
                <c-p-u-utilization v-if="cpuUsages" :height="130" :cpu-usages="cpuUsages"></c-p-u-utilization>
                <div v-else style="text-align: center; color: gray;">
                    暂无数据
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <div class="ui padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <h3 class="ui header">硬盘读写速率</h3>
                <disk-i-o-usage v-if="diskIOUsages" :height="130" :disk-i-o-usages="diskIOUsages"></disk-i-o-usage>
                <div v-else style="text-align: center; color: gray;">
                    暂无数据
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <div class="ui padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <h3 class="ui header">公网带宽使用量</h3>
                <bandwidth-usage v-if="networks" :height="130"
                                 :bandwidth-usages="networks[0].bandwidth_usages"></bandwidth-usage>
                <div v-else style="text-align: center; color: gray;">
                    暂无数据
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <div class="ui padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <h3 class="ui header">内网带宽使用量</h3>
                <bandwidth-usage v-if="networks" :height="130"
                                 :bandwidth-usages="networks[1].bandwidth_usages"></bandwidth-usage>
                <div v-else style="text-align: center; color: gray;">
                    暂无数据
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <div class="ui padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <h3 class="ui header">公网流量使用量</h3>
                <traffic-usage v-if="networks" :height="130"
                                 :traffic-usages="networks[0].traffic_usages"></traffic-usage>
                <div v-else style="text-align: center; color: gray;">
                    暂无数据
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <div class="ui padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <h3 class="ui header">内网流量使用量</h3>
                <traffic-usage v-if="networks" :height="130"
                                 :traffic-usages="networks[1].traffic_usages"></traffic-usage>
                <div v-else style="text-align: center; color: gray;">
                    暂无数据
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CPUUtilization from "./Statistics/CPUUtilization";
    import DiskIOUsage from "./Statistics/DiskIOUsage";
    import BandwidthUsage from "./Statistics/BandwidthUsage";
    import TrafficUsage from "./Statistics/TrafficUsage";

    export default {
        name: "Statistics",
        components: {TrafficUsage, BandwidthUsage, DiskIOUsage, CPUUtilization},
        props: ["instance", "operationRoutePrefix"],
        data: function () {
            return {
                isLoading: false,
                cpuUsages: null,
                diskIOUsages: null,
                networks: null,
            };
        },
        created: function () {
            this.isLoading = true;
            axios.get(route(this.operationRoutePrefix + "computeInstances.statistics", [this.instance.id]))
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.cpuUsages = data.cpuUsages;
                        this.diskIOUsages = data.diskIOUsages;
                        this.networks = data.networks;
                    } else {
                        this.$globalErrnoHandler(data);
                    }
                })
                .catch(this.$axiosCatchError2Console)
                .then(() => {
                    this.isLoading = false;
                })
            ;
        }
    }
</script>

<style scoped>

</style>