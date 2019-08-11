<template>
    <i ref="priorityIcon" class="flag icon" :data-content="priorityText" v-bind:style="{color: priorityClass}"></i>
</template>

<script>
    export default {
        name: "Priority",
        props: ["priority"],
        mounted: function () {
            $(this.$refs.priorityIcon)
                .popup({
                    distanceAway: 45,
                })
            ;
        },
        computed: {
            priorityText: function () {
                switch (this.priority) {
                    case 0:
                        return "低";
                    case 1:
                        return "中";
                }
                return "高";
            },
            priorityClass: function () {
                switch (this.priority) {
                    case 0:
                        return "teal";
                    case 1:
                        return "orange";
                }
                return "red";
            }
        }
    }
</script>

<style scoped>

</style>