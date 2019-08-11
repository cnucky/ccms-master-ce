<template>
    <td>
        <router-link :to="{name: 'users.show', params: {id: entry.id}}">{{ entry[keyName] }}</router-link>
    </td>
</template>

<script>
    export default {
        name: "LinkColumn",
        props: ["entry", "keyName"],
    }
</script>

<style scoped>

</style>