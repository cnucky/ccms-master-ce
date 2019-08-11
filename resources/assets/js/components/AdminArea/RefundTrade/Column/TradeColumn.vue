<template>
    <td>
        <router-link :to="{name: 'admin.paymentTrades.show', params: {id: entry.trade_id}}">#{{ entry.trade_id }}</router-link>
    </td>
</template>

<script>
    export default {
        name: "TradeColumn",
        props: ["entry", "keyName"],
    }
</script>

<style scoped>

</style>