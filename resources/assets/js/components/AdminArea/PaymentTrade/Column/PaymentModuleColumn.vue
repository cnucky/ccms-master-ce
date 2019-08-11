<template>
    <td>
        <router-link v-if="entry.payment_module" :to="{name: 'paymentModules.edit', params: {id: entry.payment_module.id}}">{{ entry.payment_module.name }}</router-link>
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