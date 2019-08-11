<template>
    <label class="ui tiny label" v-bind:class="[labelClass]">{{ text }}</label>
</template>

<script>
    export default {
        name: "Status",
        props: ["status"],
        computed: {
            text: function () {
                switch (parseInt(this.status)) {
                    case 0:
                        return "创建中";
                    case 1:
                        return "正常";
                    case 2:
                        return "锁定";
                    case 3:
                        return "创建失败";
                    case 5:
                        return "释放失败";
                }
            },
            labelClass: function () {
                switch (parseInt(this.status)) {
                    case 0:
                        return "";
                    case 1:
                        return "green";
                    case 2:
                        return "red";
                    case 3:
                        return "";
                    case 5:
                        return "";
                }
            }
        }
    }
</script>

<style scoped>

</style>