<template>
    <button class="ui tiny icon button" v-on:click.prevent="$emit('click')" v-bind:class="{ loading: isLoading }" :disabled="isLoading">
        <i class="sync icon"></i>
    </button>
</template>

<script>
    export default {
        name: "RefreshButton",
        props: ["isLoading"]
    }
</script>

<style scoped>

</style>