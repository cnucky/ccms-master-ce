<template>
    <td>{{ diskCapacity }} GiB</td>
</template>

<script>
    export default {
        name: "DiskColumn",
        props: ["entry"],
        computed: {
            diskCapacity: function () {
                if (this.entry.resourceCounters) {
                    return Math.max(parseInt(this.entry.resourceCounters.zone_total_disk_capacity) - parseInt(this.entry.resourceCounters.zone_total_allocated_disk_capacity), 0);
                }
                return "-";
            },
        }
    }
</script>

<style scoped>

</style>