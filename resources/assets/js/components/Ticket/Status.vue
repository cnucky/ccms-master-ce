<template>
    <label class="ui label" v-bind:class="[statusClass]">{{ statusText }}</label>
</template>

<script>
    export default {
        name: "Status",
        props: ["status"],
        computed: {
            statusText: function () {
                if (this.$te("status.ticket." + this.status))
                    return this.$t("status.ticket." + this.status);
                return "未知";
            },
            statusClass: function () {
                switch (this.status) {
                    case 0:
                        return "green";
                    case 1:
                        return "black";
                    case 2:
                        return "orange";
                    case 3:
                        return "blue";
                    case 5:
                        return "teal";
                    case 6:
                        return "grey";
                    default:
                        return "";
                }
            }
        },
    }
</script>

<style scoped>

</style>