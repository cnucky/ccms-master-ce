<template>
    <td>
        <router-link v-if="entry.hasOwnProperty('user') && entry.user" :to="{name: 'users.show', params: {id: entry.user.id}}">
            {{ entry.user.name }}
            <br>
            {{ entry.user.email }}
        </router-link>
        <template v-else>-</template>
    </td>
</template>

<script>
    export default {
        name: "UserColumn",
        props: ["entry", "keyName"],
    }
</script>

<style scoped>

</style>