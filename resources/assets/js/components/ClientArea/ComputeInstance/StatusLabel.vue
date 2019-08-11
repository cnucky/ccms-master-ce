<template>
    <label class="ui label" v-bind:class="[statusClass]">{{ $t('status.computeInstance.' + statusCode) }}</label>
</template>

<script>
    const statusCode2Class = {
        "0": "green",
        "1": "grey",
        "2": "grey",
        "3": "red",
        "5": "red",
        "6": "black",
        "8": "black",
    };

    export default {
        name: "StatusLabel",
        props: ["statusCode"],
        computed: {
            statusClass: function () {
                return statusCode2Class[this.statusCode];
            }
        }
    }
</script>

<style scoped>

</style>