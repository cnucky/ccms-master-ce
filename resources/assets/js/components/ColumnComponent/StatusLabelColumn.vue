<template>
    <td><status-label :entry="entry"></status-label></td>
</template>

<script>
    import StatusLabel from "../StatusLabel";
    export default {
        name: "StatusLabelColumn",
        props: ["entry"],
        components: {StatusLabel}
    }
</script>

<style scoped>

</style>