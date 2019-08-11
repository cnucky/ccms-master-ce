<template>
    <label class="ui mini label" v-bind:class="[labelClass]">{{ percentage }}%</label>
</template>

<script>
    export default {
        name: "UsagePercentageLabel",
        props: ["entry"],
        computed: {
            percentage: function () {
                return (this.entry.assigned_count / this.entry.total_subnet * 100).toFixed(2);
            },
            labelClass: function () {
                if (this.percentage >= 100)
                    return "red";
                else if (this.percentage > 90)
                    return "orange ";
                else if (this.percentage > 80)
                    return "yellow";

                return "green";
            }
        }
    }
</script>

<style scoped>

</style>