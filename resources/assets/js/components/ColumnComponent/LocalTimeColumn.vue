<template>
    <td><local-time :time="entry[keyName]"></local-time></td>
</template>

<script>
    import LocalTime from "../LocalTime";
    export default {
        name: "LocalTimeColumn",
        components: {LocalTime},
        props: ["keyName", "entry"],
    }
</script>

<style scoped>

</style>