<template>
    <guest-navigation>
        <!-- Quick Actions -->
        <div class="nav navbar-nav quick-actions">
            <router-link class="ui tiny blue button" :to="{ name: 'register' }">注册</router-link>
            <router-link class="ui tiny button" :to="{ name: 'login' }">登录</router-link>
        </div>
        <!-- /Quick Actions -->
    </guest-navigation>
</template>

<script>
    export default {
        name: "UserGuestNavigation"
    }
</script>

<style scoped>
</style>