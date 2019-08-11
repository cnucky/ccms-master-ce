<template>
    <label class="ui label" v-bind:class="labelClass">{{ labelText }}</label>
</template>

<script>
    export default {
        name: "StatusLabel",
        props: ["status"],
        computed: {
            labelClass: function () {
                var status = parseInt(this.status);
                switch (status) {
                    case 0:
                        return "red";
                    case 1:
                        return "green";
                    case 2:
                        return "grey"
                }
            },
            labelText: function () {
                return this.$t("status.paymentTrade." + this.status);
            }
        }
    }
</script>

<style scoped>

</style>