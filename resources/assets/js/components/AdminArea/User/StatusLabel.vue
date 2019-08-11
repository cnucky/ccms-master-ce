<template>
    <label class="ui label" v-bind:class="[labelClass]">{{ labelText }}</label>
</template>

<script>
    export default {
        name: "StatusLabel",
        props: ["status"],
        computed: {
            labelClass: function () {
                switch (this.status) {
                    case 0:
                        return "gray";
                    case 1:
                        return "green";
                    case 2:
                        return "red";
                }
                return "black";
            },
            labelText: function () {
                if (this.$te("status.user." + this.status)) {
                    return this.$t("status.user." + this.status);
                }
                return "未知";
            }
        }
    }
</script>

<style scoped>

</style>