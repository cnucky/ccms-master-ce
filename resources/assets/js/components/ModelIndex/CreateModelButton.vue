<template>
    <button class="ui teal tiny button" v-on:click.prevent="$emit('click')"><i class="plus icon"></i> 添加{{ modelName }}</button>
</template>

<script>
    export default {
        name: "CreateModelButton",
        props: ["modelName"],
    }
</script>

<style scoped>

</style>