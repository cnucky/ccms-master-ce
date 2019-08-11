<template>
    <td>
        <router-link v-if="entry.status === 1" class="ui tiny button" :to="{name: 'paymentTrades.refunds.index', params: {id: entry.id}}"><i class="undo icon"></i> 退款</router-link>
    </td>
</template>

<script>
    export default {
        name: "TradeOperationColumn",
        props: ["entry", "parentApp"],
    }
</script>

<style scoped>

</style>