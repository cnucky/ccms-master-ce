<template>
    <td>
        <template v-if="entry.roles && entry.roles.length"><router-link :to="{name: 'admin.roles.edit', params: {id: entry.roles[0].id}}">{{ entry.roles[0].name }}</router-link></template>
    </td>
</template>

<script>
    export default {
        name: "RoleColumn",
        props: ["entry", "keyName"],
    }
</script>

<style scoped>

</style>