<template>
    <div class="ui checkbox">
        <input type="checkbox" :id="id" :name="name" :checked="checked" @change="$emit('input', $event.target.checked)" :required="required">
        <label :for="id" v-html="label"></label>
    </div>
</template>

<script>
    export default {
        name: "linked-label-checkbox",
        props: ['name', 'label', 'checked', 'required', 'id'],
    }
</script>

<style scoped>

</style>