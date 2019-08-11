<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <div class="ui four column grid">
                    <column name="节点总数" :inline="true">
                        {{ data.zone.compute_nodes.length }}
                        <template v-if="data.offlineNodeCount"><span style="color: red;">（{{ data.offlineNodeCount }} 离线）</span></template>
                    </column>
                    <column name="实例总数" :inline="true">{{ data.computeInstanceCount }}</column>
                    <column name="IPv4地址池" :inline="true">{{ data.zone.ipv4_pools.length }}</column>
                    <column name="IPv6地址池" :inline="true">{{ data.zone.ipv6_pools.length }}</column>
                </div>

                <div class="ui divider"></div>

                <div class="ui one column grid" style="margin-top: 20px;">
                    <column name="内存分配">
                        <div class="ui green progress" :data-percent="data.allocatedMemoryCapacity / data.totalMemoryCapacity * 100">
                            <div class="bar">
                                <div class="progress"></div>
                            </div>
                            <div class="label">{{ data.allocatedMemoryCapacity }}MiB / {{ data.totalMemoryCapacity }}MiB</div>
                        </div>
                    </column>
                    <column name="储存分配">
                        <div class="ui green progress" :data-percent="data.allocatedDiskCapacity / data.totalDiskCapacity * 100">
                            <div class="bar">
                                <div class="progress"></div>
                            </div>
                            <div class="label">{{ data.allocatedDiskCapacity }}GiB / {{ data.totalDiskCapacity.toFixed(2) }}GiB</div>
                        </div>
                    </column>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "../../../ClientArea/ComputeInstance/Show/Information/Column";
    export default {
        name: "Dashboard",
        components: {Column},
        props: ["data"],
        mounted: function () {
            $(".ui.progress").progress();
        }
    }
</script>

<style scoped>

</style>