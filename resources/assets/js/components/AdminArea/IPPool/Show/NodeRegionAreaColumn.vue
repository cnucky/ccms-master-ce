<template>
    <td><i :class="entry.zone.region.icon_class"></i> {{ entry.zone.region.name }}-{{ entry.zone.name }}</td>
</template>

<script>
    export default {
        name: "NodeRegionAreaColumn",
        props: ["entry"]
    }
</script>

<style scoped>

</style>