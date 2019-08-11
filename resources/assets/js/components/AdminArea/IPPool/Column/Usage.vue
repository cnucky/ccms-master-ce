<template>
    <td>
        <usage-span :entry="entry"></usage-span>
    </td>
</template>

<script>
    import UsageSpan from "../UsageSpan";
    export default {
        name: "Usage",
        components: {UsageSpan},
        props: ["entry"],
    }
</script>

<style scoped>

</style>