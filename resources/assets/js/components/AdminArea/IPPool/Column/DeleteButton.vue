<template>
    <td><button class="ui tiny red button" v-on:click="$emit('buttonClick')" v-bind:class="{loading: isDeleting}"><i class="delete icon"></i> 删除</button></td>
</template>

<script>
    export default {
        name: "DeleteButton",
        props: ["isDeleting"],
    }
</script>

<style scoped>

</style>