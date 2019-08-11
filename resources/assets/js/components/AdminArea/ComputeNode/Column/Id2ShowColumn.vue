<template>
    <td>
        <router-link :to="{name: 'computeNodes.show', params: {id: entry.id}}">
            {{ entry.id }}
        </router-link>
    </td>
</template>

<script>
    export default {
        name: "IdLink2ShowColumn",
        props: ["parentApp", "keyName", "entry"],
    }
</script>

<style scoped>

</style>