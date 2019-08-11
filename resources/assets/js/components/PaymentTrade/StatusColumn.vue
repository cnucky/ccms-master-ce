<template>
    <td><status-label :status="entry[keyName]"></status-label></td>
</template>

<script>
    import StatusLabel from "./StatusLabel";
    export default {
        name: "StatusColumn",
        components: {StatusLabel},
        props: ["entry", "keyName"],
    }
</script>

<style scoped>

</style>