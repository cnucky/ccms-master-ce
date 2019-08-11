<template>
    <td>{{ $t("operationRequest.type.computeInstance." + entry.type) }}</td>
</template>

<script>
    export default {
        name: "OperationRequestTypeColumn",
        props: ["entry", "keyName"],
    }
</script>

<style scoped>

</style>