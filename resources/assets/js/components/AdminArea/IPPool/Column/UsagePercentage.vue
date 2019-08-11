<template>
    <td>
        <usage-percentage-label :entry="entry"></usage-percentage-label>
    </td>
</template>

<script>
    import UsagePercentageLabel from "../UsagePercentageLabel";
    export default {
        name: "UsagePercentage",
        components: {UsagePercentageLabel},
        props: ["entry"],
    }
</script>

<style scoped>

</style>