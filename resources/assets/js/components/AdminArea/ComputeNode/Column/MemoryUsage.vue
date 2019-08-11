<template>
    <td>{{ memoryFree }}GiB</td>
</template>

<script>
    export default {
        name: "MemoryUsage",
        props: ["parentApp", "keyName", "entry"],
        computed: {
            memoryCapacity: function () {
                return (this.entry.total_memory_capacity_in_mib_unit / 1024).toFixed(2);
            },
            memoryFree: function () {
                return (this.entry.current_memory_free_in_mib_unit / 1024).toFixed(2);
            }
        }
    }
</script>

<style scoped>

</style>