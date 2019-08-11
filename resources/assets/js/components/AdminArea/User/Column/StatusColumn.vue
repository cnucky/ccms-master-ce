<template>
    <td><status-label :status="entry.status"></status-label></td>
</template>

<script>
    import StatusLabel from "./../StatusLabel";
    export default {
        name: "StatusColumn",
        components: {StatusLabel},
        props: ["entry"],
    }
</script>

<style scoped>

</style>