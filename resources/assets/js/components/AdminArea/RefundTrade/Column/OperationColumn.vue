<template>
    <td>
        <router-link class="ui tiny button" :to="{name: 'admin.paymentTrades.show', params: {id: entry.trade_id}}"><i class="edit icon"></i> 详情</router-link>
    </td>
</template>

<script>
    export default {
        name: "OperationColumn",
        props: ["entry"],
    }
</script>

<style scoped>

</style>