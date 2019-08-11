<template>
    <td><i :class="entry.icon_class"></i> {{ entry.name }}</td>
</template>

<script>
    export default {
        name: "NameWithIcon",
        props: ["entry"]
    }
</script>

<style scoped>

</style>