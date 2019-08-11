<template>
    <div class="flex-container">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "FlexContainer"
    }
</script>

<style scoped>
    .flex-container {
        margin: 0 auto;
        width: 100%;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .flex-container:before {
        display: block;
        content: '';
        flex-grow: 1;
    }

    .flex-container:after {
        display: block;
        content: '';
        flex-grow: 1;
    }
</style>