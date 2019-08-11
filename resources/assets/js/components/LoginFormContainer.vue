<template>
    <div class="login-form-container">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "login-form-container",
    }
</script>

<style scoped>
    @import "./../../sass/login.scss";
</style>