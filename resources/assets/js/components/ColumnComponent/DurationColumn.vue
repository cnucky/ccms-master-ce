<template>
    <td ref="column" class="popup-time" :data-content="localTime">
        {{ momentFrom }}
    </td>
</template>

<script>
    export default {
        name: "DurationColumn",
        props: ["entry", "keyName"],
        mounted: function () {
            $(this.$refs.column)
                .popup({
                    distanceAway: 25,
                    hoverable: true,
                })
            ;
        },
        updated: function () {
            $(this.$refs.column)
                .popup({
                    distanceAway: 25,
                    hoverable: true,
                })
            ;
        },
        computed: {
            localTime: function () {
                var value = this.$toLocalTime(this.entry[this.keyName]);
                if (value === "Invalid date")
                    return "";
                return value;
            },
            momentFrom: function () {
                var value = this.$momentFrom(this.entry[this.keyName]);
                if (value === "Invalid date")
                    return "";
                return value;
            }
        }
    }
</script>

<style scoped>

</style>