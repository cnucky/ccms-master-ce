<template>
    <td><amount :amount="entry[keyName]"></amount></td>
</template>

<script>
    import Amount from "../CreditRecord/Amount";
    export default {
        name: "AmountColumn",
        components: {Amount},
        props: ["keyName", "entry"],
    }
</script>

<style scoped>

</style>