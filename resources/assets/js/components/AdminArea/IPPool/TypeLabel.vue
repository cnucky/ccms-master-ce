<template>
    <label class="ui mini label" v-bind:class="[labelClass]">{{ labelText }}</label>
</template>

<script>
    export default {
        name: "TypeLabel",
        props: ["entry"],
        computed: {
            labelClass: function () {
                switch (this.entry.type) {
                    case 0:
                        return "blue";
                    default:
                        return "grey";
                }
            },
            labelText: function () {
                switch (this.entry.type) {
                    case 0:
                        return "公网";
                    default:
                        return "内网";
                }
            }
        }
    }
</script>

<style scoped>

</style>