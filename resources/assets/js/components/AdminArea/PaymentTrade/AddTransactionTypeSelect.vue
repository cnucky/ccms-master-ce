<template>
    <div ref="typeSelect" class="ui selection dropdown" style="min-width: 100px;">
        <i class="dropdown icon"></i>
        <div class="text">{{ dropdownText }}</div>
        <div class="menu">
            <div class="item" :data-value="0" v-on:click="$emit('input', 0)">关闭退款</div>
            <div class="item" :data-value="1" v-on:click="$emit('input', 1)">允许退款</div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "AddTransactionTypeSelect",
        props: ["value"],
        mounted: function () {
            $(this.$refs.typeSelect).dropdown({
                action: "hide",
            });
        },
        computed: {
            dropdownText: function () {
                switch (parseInt(this.value)) {
                    case 0:
                        return "关闭退款";
                    case 1:
                        return "允许退款";
                }
            },
        },
        watch: {
            value: function (newValue, oldValue) {
                var dropdownText;
                switch (parseInt(newValue)) {
                    case 0:
                        dropdownText = "关闭退款";
                        break;
                    case 1:
                        dropdownText = "允许退款";
                        break;
                }
                $(this.$refs.typeSelect).dropdown("set text", dropdownText);
            }
        }
    }
</script>

<style scoped>

</style>