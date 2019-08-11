<template>
    <div class="ui modal">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "AutoShowModal",
        mounted: function () {
            $(this.$el).modal("show");
        }
    }
</script>

<style scoped>

</style>