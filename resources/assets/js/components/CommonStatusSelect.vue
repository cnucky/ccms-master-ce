<template>
    <select class="ui dropdown" v-bind:value="value" v-on:change="$emit('input', $event.target.value)">
        <option value="0">{{ $t('status.image.0') }}</option>
        <option value="1">{{ $t('status.image.1') }}</option>
        <option value="2">{{ $t('status.image.2') }}</option>
    </select>
</template>

<script>
    export default {
        name: "CommonStatusSelect",
        props: ["value"],
        mounted: function () {
            $(this.$el).dropdown();
        },
        methods: {
            clearValue: function () {
                $(this.$el).dropdown("clear");
            },
            setValue: function (value) {
                $(this.$el).dropdown("set selected", value);
            },
        }
    }
</script>

<style scoped>

</style>