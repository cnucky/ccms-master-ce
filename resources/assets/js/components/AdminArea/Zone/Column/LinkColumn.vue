<template>
    <td><router-link :to="{name: 'zones.dashboard', params: {id: entry.id}}">{{ entry[keyName] }}</router-link></td>
</template>

<script>
    export default {
        name: "LinkColumn",
        props: ["entry", "keyName"],
    }
</script>

<style scoped>

</style>