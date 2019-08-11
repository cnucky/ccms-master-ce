<template>
    <td><label class="ui tiny label" v-bind:class="labelClass">{{ text }}</label></td>
</template>

<script>
    export default {
        name: "StatusColumn",
        props: ["entry", "keyName"],
        computed: {
            text: function () {
                switch (parseInt(this.entry[this.keyName])) {
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
            labelClass: function () {
                switch (parseInt(this.entry[this.keyName])) {
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