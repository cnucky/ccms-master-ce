<template>
    <td>{{ entry.current_disk_free_in_gib_unit.toFixed(2) }}GiB</td>
</template>

<script>
    export default {
        name: "DiskSpaceUsage",
        props: ["parentApp", "keyName", "entry"],
    }
</script>

<style scoped>

</style>