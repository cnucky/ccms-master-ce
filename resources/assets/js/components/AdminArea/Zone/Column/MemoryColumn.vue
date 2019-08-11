<template>
    <td>{{ memoryCapacity }} MiB</td>
</template>

<script>
    export default {
        name: "MemoryColumn",
        props: ["entry"],
        computed: {
            memoryCapacity: function () {
                if (this.entry.resourceCounters) {
                    return Math.max(parseInt(this.entry.resourceCounters.zone_total_memory_capacity) - parseInt(this.entry.resourceCounters.zone_total_allocated_memory_capacity), 0);
                }
                return "-";
            }
        }
    }
</script>

<style scoped>

</style>