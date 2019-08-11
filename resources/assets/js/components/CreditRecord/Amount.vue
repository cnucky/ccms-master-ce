<template>
    <span v-bind:class="amountClass">{{ $store.getters.defaultCurrency.prefix }} {{ amount }}</span>
</template>

<script>
    export default {
        name: "Amount",
        props: ["amount", "forceColor"],
        computed: {
            amountClass: function () {
                if (typeof this.forceColor === "string") {
                    return this.forceColor;
                }
                if (this.amount === null || this.amount === undefined)
                    return "";
                if (this.amount.length && this.amount[0] === "-") {
                    return "red";
                }
                return "green";
            }
        }
    }
</script>

<style scoped>
    .green {
        color: green;
    }

    .red {
        color: red;
    }
</style>