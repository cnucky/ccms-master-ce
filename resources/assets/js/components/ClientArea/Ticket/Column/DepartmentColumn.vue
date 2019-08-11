<template>
    <td><template v-if="entry.department">{{ entry.department.name }}</template></td>
</template>

<script>
    export default {
        name: "ShowValue",
        props: ["keyName", "entry"],
    }
</script>

<style scoped>

</style>