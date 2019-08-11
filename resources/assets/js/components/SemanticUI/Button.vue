<template>
    <button type="submit" class="ui button" v-bind:class="{ loading: isLoading }" :disabled="isLoading"><slot></slot></button>
</template>

<script>
    export default {
        name: "Button",
        props: ["isLoading"]
    }
</script>

<style scoped>

</style>