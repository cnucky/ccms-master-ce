<template>
    <span>
        {{ entry.assigned_count }}/{{ entry.total_subnet }}
    </span>
</template>

<script>
    export default {
        name: "UsageSpan",
        props: ["entry"],
    }
</script>

<style scoped>

</style>