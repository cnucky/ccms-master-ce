<template>
    <td>
        {{ entry[keyName] === null ? '0' : entry[keyName].toFixed(2) }}%
    </td>
</template>

<script>
    export default {
        name: "Double",
        props: ["parentApp", "keyName", "entry"],
    }
</script>

<style scoped>

</style>