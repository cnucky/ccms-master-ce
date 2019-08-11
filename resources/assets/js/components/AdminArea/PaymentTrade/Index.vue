<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">充值订单</h1>
        </div>

        <div class="sixteen wide column">
            <payment-trade-index-table></payment-trade-index-table>
        </div>
    </div>
</template>

<script>
    import PaymentTradeIndexTable from "./IndexTable";
    export default {
        name: "Index",
        components: {PaymentTradeIndexTable}
    }
</script>

<style scoped>

</style>