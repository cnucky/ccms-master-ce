<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui field">
            <label class="required">名称</label>
            <input type="text" maxlength="191" :value="name" v-on:input="$emit('name', $event.target.value)">
        </div>
        <div class="ui field">
            <label class="required">私钥</label>
            <textarea :value="privateKey" v-on:input="$emit('privateKey', $event.target.value)"></textarea>
        </div>
        <div class="ui field">
            <label class="required">证书（建议使用完整证书链）</label>
            <textarea :value="certificate" v-on:input="$emit('certificate', $event.target.value)"></textarea>
        </div>
        <div class="ui field">
            <button type="submit" class="ui small teal fluid button" v-bind:class="{loading: isSubmitting}" :disabled="isSubmitting">提交</button>
        </div>
    </form>
</template>

<script>
    export default {
        name: "CertificateForm",
        props: ["isSubmitting", "name", "privateKey", "certificate"],
    }
</script>

<style scoped>

</style>