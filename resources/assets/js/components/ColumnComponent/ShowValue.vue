<template>
    <td>{{ entry[keyName] }}</td>
</template>

<script>
    export default {
        name: "ShowValue",
        props: ["keyName", "entry"],
    }
</script>

<style scoped>

</style>