<template>
    <textarea :value="value" v-on:input="$emit('input', $event.target.value)" @blur="$emit('blur')"></textarea>
</template>

<script>
    export default {
        name: "AutofocusTextarea",
        props: ["value"],
        mounted: function () {
            this.$el.focus();
        }
    }
</script>

<style scoped>

</style>