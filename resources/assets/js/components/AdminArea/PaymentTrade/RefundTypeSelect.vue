<template>
    <select ref="typeSelect" class="ui dropdown" :value="value" v-on:change="$emit('input', $event.target.value)">
        <option value="0">自动调用模块API退款</option>
        <option value="1">手动退款</option>
    </select>
</template>

<script>
    export default {
        name: "RefundTypeSelect",
        props: ["value"],
        mounted: function () {
            $(this.$refs.typeSelect).dropdown();
        }
    }
</script>

<style scoped>

</style>