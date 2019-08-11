<template>
    <td>
        <amount :amount="entry.fee_in_default_currency"></amount>
        <span v-if="entry.refunds_count" style="color: red;">(发生退款)</span>
    </td>
</template>

<script>
    import Amount from "../../../CreditRecord/Amount";
    export default {
        name: "AmountColumn",
        components: {Amount},
        props: ["entry"]
    }
</script>

<style scoped>

</style>