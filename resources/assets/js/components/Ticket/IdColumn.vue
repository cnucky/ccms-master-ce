<template>
    <td><router-link :to="{name: 'tickets.show', params: {id: entry.id}}">{{ entry[keyName] }}</router-link> <priority :priority="entry.priority"></priority></td>
</template>

<script>
    import Priority from "./Priority";
    export default {
        name: "ShowValue",
        components: {Priority},
        props: ["keyName", "entry"],
    }
</script>

<style scoped>

</style>