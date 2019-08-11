<template>
    <guest-navigation brand-route-name="admin.welcome">
        <!-- Quick Actions -->
        <div class="nav navbar-nav quick-actions">
            <router-link class="ui tiny blue button" :to="{ name: 'admin.login' }">登录</router-link>
        </div>
        <!-- /Quick Actions -->
    </guest-navigation>
</template>

<script>
    export default {
        name: "AdminGuestNavigation"
    }
</script>

<style scoped>
</style>