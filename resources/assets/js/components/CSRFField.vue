<template>
    <input type="hidden" name="_token" v-model="token">
</template>

<script>
    export default {
        name: "csrf-field",
        data: () => {
            return {
                token: document.head.querySelector('meta[name="csrf-token"]').content,
            }
        }
    }
</script>

<style scoped>

</style>