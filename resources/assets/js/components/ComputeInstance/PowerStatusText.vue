<template>
    <span>
        {{ text }}
    </span>
</template>

<script>
    export default {
        name: "PowerStatusText",
        props: ["instance"],
        computed: {
            text: function () {
                var text = this.$t("status.computeInstancePowerStatus." + this.instance.power_status);
                if (text === "status.computeInstancePowerStatus.null")
                    return "未知";
                return text;
            }
        }
    }
</script>

<style scoped>

</style>