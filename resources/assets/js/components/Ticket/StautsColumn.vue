<template>
    <td><status :status="entry[keyName]"></status></td>
</template>

<script>
    import Status from "./Status";
    export default {
        name: "StatusColumn",
        components: {Status},
        props: ["keyName", "entry"],
    }
</script>

<style scoped>

</style>