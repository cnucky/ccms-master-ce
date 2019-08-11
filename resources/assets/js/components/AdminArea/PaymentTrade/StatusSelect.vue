<template>
    <div ref="statusSelect" class="ui mini selection dropdown" v-bind:class="{loading: isSubmitting}">
        <i class="dropdown icon"></i>
        <div class="text">{{ dropdownText }}</div>
        <div class="menu">
            <div class="item" v-on:click="$emit('input', 0)">未支付</div>
            <div class="item" v-on:click="$emit('input', 1)">已支付</div>
            <div class="item" v-on:click="$emit('input', 2)">已取消</div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "StatusSelect",
        props: ["isSubmitting", "value"],
        mounted: function () {
            $(this.$refs.statusSelect).dropdown({
                action: "hide",
            });
        },
        computed: {
            dropdownText: function () {
                return this.$t("status.paymentTrade." + this.value);
            }
        },
        watch: {
            value: function (newValue, oldValue) {
                $(this.$refs.statusSelect).dropdown("set text", this.$t("status.paymentTrade." + newValue));
            },
        }
    }
</script>

<style scoped>

</style>