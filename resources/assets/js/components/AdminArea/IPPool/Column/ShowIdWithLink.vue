<template>
    <td>
        <router-link :to="{name: parentApp.modelInternalName + '.show', params: {id: entry.id}}">{{ entry.id }}</router-link>
    </td>
</template>

<script>
    export default {
        name: "ShowIdWithLink",
        props: ["parentApp", "entry"],
    }
</script>

<style scoped>

</style>