<template>
    <div ref="refundStatusSelect" class="ui mini selection dropdown" :disabled="isChangingStatus">
        <i class="dropdown icon"></i>
        <div class="text" v-bind:style="{color: dropdownTextColor}">{{ dropdownText }}</div>
        <div class="menu">
            <div class="item" v-on:click="$emit('input', 0)" style="color: grey;">处理中</div>
            <div class="item" v-on:click="$emit('input', 1)" style="color: black;">已取消</div>
            <div class="item" v-on:click="$emit('input', 2)" style="color: red;">失败</div>
            <div class="item" v-on:click="$emit('input', 3)" style="color: green;">成功</div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "RefundStatus",
        props: ["isChangingStatus", "value"],
        mounted: function () {
            $(this.$refs.refundStatusSelect).dropdown({
                action: "hide",
            });
        },
        computed: {
            dropdownText: function () {
                switch (parseInt(this.value)) {
                    case 0:
                        return "处理中";
                    case 1:
                        return "已取消";
                    case 2:
                        return "失败";
                    case 3:
                        return "成功";
                    default:
                        return "未知";
                }
            },
            dropdownTextColor: function () {
                switch (parseInt(this.value)) {
                    case 0:
                        return "grey";
                    case 1:
                        return "black";
                    case 2:
                        return "red";
                    case 3:
                        return "green";
                    default:
                        return "";
                }
            }
        }
    }
</script>

<style scoped>

</style>