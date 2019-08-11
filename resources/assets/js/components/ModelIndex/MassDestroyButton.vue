<template>
    <button class="ui red inverted tiny button" :disabled="selectedItemCount <= 0" v-on:click="$emit('click')"><i class="delete icon"></i> 删除</button>
</template>

<script>
    export default {
        name: "MassDestroyButton",
        props: ["selectedItemCount"],
    }
</script>

<style scoped>

</style>