<template>
    <div ref="imageSelect" class="ui basic fluid medium dropdown button">
        <input type="hidden">
        <span class="text">请选择版本</span>
        <i class="dropdown icon"></i>
        <div class="menu">
            <div v-for="(image, index) in images"
                 class="item"
                 :data-value="image.id" v-on:click="$emit('input', image.id)">
                {{ image.name }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ImageSelect",
        props: ["images"],
        mounted: function () {
            $(this.$refs.imageSelect).dropdown();
        },
        watch: {
            images: function () {
                $(this.$refs.imageSelect).dropdown("restore defaults");
                this.$emit('input', null);
            }
        }
    }
</script>

<style scoped>

</style>