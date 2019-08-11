<template>
    <td><priority :priority="entry[keyName]"></priority></td>
</template>

<script>
    import Priority from "./Priority";
    export default {
        name: "PriorityColumn",
        components: {Priority},
        props: ["keyName", "entry"],
    }
</script>

<style scoped>

</style>