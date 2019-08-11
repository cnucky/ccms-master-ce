<template>
    <td>
        <phone :country="entry.country" :phone="entry.phone"></phone>
    </td>
</template>

<script>
    import Phone from "../Phone";
    export default {
        name: "PhoneColumn",
        components: {Phone},
        props: ["entry"],
    }
</script>

<style scoped>

</style>