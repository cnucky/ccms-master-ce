<template>
    <span>{{ localTime }}</span>
</template>

<script>
    export default {
        name: "LocalTime",
        props: ["time"],
        computed: {
            localTime: function () {
                var localTime = this.$toLocalTime(this.time);
                if (localTime === "Invalid date")
                    return "";
                return localTime;
            }
        }
    }
</script>

<style scoped>

</style>