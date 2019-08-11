<template>
    <div class="ui checkbox">
        <input type="checkbox" :name="name" :checked="checked" @change="$emit('input', $event.target.checked)" :required="required">
        <label v-if="typeof label === 'string'" v-html="label"></label>
    </div>
</template>

<script>
    export default {
        name: "semantic-ui-checkbox",
        props: ['name', 'label', 'checked', 'required'],
        mounted: function () {
            $(this.$el).checkbox();
        }
    }
</script>

<style scoped>

</style>