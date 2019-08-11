<template>
    <td><type-label :entry="entry"></type-label></td>
</template>

<script>
    import TypeLabel from "../TypeLabel";
    export default {
        name: "Type",
        components: {TypeLabel},
        props: ["entry"],
    }
</script>

<style scoped>

</style>