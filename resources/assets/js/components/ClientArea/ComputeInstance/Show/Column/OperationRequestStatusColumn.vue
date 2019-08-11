<template>
    <td><label class="ui label" v-bind:class="[labelClass]">{{ $t("status.operationRequest." + entry.operation_status) }}</label></td>
</template>

<script>
    export default {
        name: "OperationRequestStatusColumn",
        props: ["entry", "keyName"],
        computed: {
            labelClass: function () {
                switch (parseInt(this.entry.operation_status)) {
                    case 2:
                        return "green";
                    case 3:
                        return "red";
                    default:
                        return "";
                }
            }
        }
    }
</script>

<style scoped>

</style>