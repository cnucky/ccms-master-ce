<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">节点</h3>
        </div>

        <div class="sixteen wide column">
            <table class="ui unstackable table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>名称</th>
                    <th>Host</th>
                    <th>CPU使用率</th>
                    <th>储存分配</th>
                    <th>内存分配</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="computeNode in data.zone.compute_nodes">
                    <compute-node-id-column :entry="computeNode" key-name="id" :parent-app="parentApp"></compute-node-id-column>
                    <compute-node-name-column :entry="computeNode" key-name="name" :parent-app="parentApp"></compute-node-name-column>
                    <td>{{ computeNode.host }}</td>
                    <compute-node-double-column :entry="computeNode" key-name="cpu_utilization" :parent-app="parentApp"></compute-node-double-column>
                    <td>
                        {{ computeNode.total_allocated_disk_in_gib_unit }}GiB / {{ computeNode.total_disk_capacity_in_gib_unit.toFixed(2) }}GiB
                        [{{ (computeNode.total_allocated_disk_in_gib_unit / computeNode.total_disk_capacity_in_gib_unit * 100).toFixed(2) }}%]
                    </td>
                    <td>
                        {{ computeNode.total_allocated_memory_in_mib_unit }}MiB / {{ computeNode.total_memory_capacity_in_mib_unit }}MiB
                        [{{ (computeNode.total_allocated_memory_in_mib_unit / computeNode.total_memory_capacity_in_mib_unit * 100).toFixed(2) }}% ]
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        name: "NodeIndex",
        props: ["data"],
        data: function () {
            return {
                serverTime: this.data.serverTime,
            };
        },
        computed: {
            parentApp: function () {
                return this;
            },
        }
    }
</script>

<style scoped>

</style>