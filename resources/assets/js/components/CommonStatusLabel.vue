<template>
    <label class="ui label" v-bind:class="[statusClass]">{{ $t('status.common.' + statusCode) }}</label>
</template>

<script>
    const statusCode2Class = {
        "0": "green",
        "1": "yellow",
        "2": "grey",
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