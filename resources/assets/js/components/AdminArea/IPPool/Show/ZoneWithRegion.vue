<template>
    <td><i :class="entry.region.icon_class"></i> {{ entry.region.name }}-{{ entry.name }}</td>
</template>

<script>
    export default {
        name: "ZoneWithRegion",
        props: ["entry"]
    }
</script>

<style scoped>

</style>