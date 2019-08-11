<template>
    <td><type :type-code="entry.type"></type></td>
</template>

<script>
    import Type from "./Type";
    export default {
        name: "TypeColumn",
        components: {Type},
        props: ["entry"]
    }
</script>

<style scoped>

</style>