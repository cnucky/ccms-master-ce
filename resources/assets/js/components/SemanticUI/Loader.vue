<template>
    <semantic-ui-inverted-dimmer v-bind:is-active="isActive">
        <div class="ui text loader" :class="loaderClass"><slot></slot></div>
    </semantic-ui-inverted-dimmer>
</template>

<script>
    export default {
        name: "Loader",
        props: ["isActive", "loaderClass"],
    }
</script>

<style scoped>

</style>