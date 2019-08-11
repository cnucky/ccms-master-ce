<template>
    <div class="ui inverted dimmer" v-bind:class="{ active: isActive }">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "InvertedDimmer",
        props: ["isActive"]
    }
</script>

<style scoped>
    .ui.inverted.dimmer {
        z-index: 1000 !important;
    }
</style>