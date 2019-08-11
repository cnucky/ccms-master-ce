<template>
    <slide-fade-transition>
        <semantic-ui-basic-message v-bind="$props"></semantic-ui-basic-message>
    </slide-fade-transition>
</template>

<script>
    export default {
        name: "semantic-ui-message",
        props: ["type", "messages", "header", "closable"],
        created: function () {
        }
    }
</script>

<style scoped>
</style>