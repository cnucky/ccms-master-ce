<template>
    <td><router-link :to="{name: 'userQuotas.edit', params: {id: entry.user_quota.id}}">{{ entry.user_quota.name }}</router-link></td>
</template>

<script>
    export default {
        name: "UserQuotaColumn",
        props: ["entry", "keyName"],
    }
</script>

<style scoped>

</style>