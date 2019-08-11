<template>
    <td><amount :amount="entry[keyName]"></amount></td>
</template>

<script>
    import Amount from "./Amount";
    export default {
        name: "AmountColumn",
        components: {Amount},
        props: ["entry", "keyName"],
    }
</script>

<style scoped>

</style>