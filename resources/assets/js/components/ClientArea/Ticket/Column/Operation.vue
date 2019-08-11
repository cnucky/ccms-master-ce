<template>
    <td>
        <router-link class="ui tiny button" :to="{name: 'tickets.show', params: {id: entry.id}}"><i class="edit icon"></i> 详情</router-link>
    </td>
</template>

<script>
    export default {
        name: "Operation",
        props: ["keyName", "entry"],
    }
</script>

<style scoped>

</style>