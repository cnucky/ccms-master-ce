<template>
    <td>
        <template v-if="entry.payment_module">{{ entry.payment_module.name }}</template>
        <template v-else>{{ entry.basic_payment_module }}</template>
    </td>
</template>

<script>
    export default {
        name: "PaymentModuleColumn",
        props: ["entry"],
    }
</script>

<style scoped>

</style>