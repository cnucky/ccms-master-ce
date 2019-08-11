<template>
    <label class="ui label" v-bind:class="[labelClass]">{{ labelText }}</label>
</template>

<script>
    export default {
        name: "StatusLabel",
        props: ["entry"],
        computed: {
            labelClass: function () {
                switch (parseInt(this.entry.status)) {
                    case 0:
                        return "gray";
                    case 1:
                        return "green";
                    case 2:
                        return "dark";
                    default:
                        return "red";
                }
            },
            labelText: function () {
                switch (parseInt(this.entry.status)) {
                    case 0:
                        return "待定";
                    case 1:
                        return "启用";
                    case 2:
                        return "停用";
                    default:
                        return "未知";
                }
            }
        }
    }
</script>

<style scoped>

</style>