<template>
    <div id="left-fixed-menu" class="ui left fixed vertical menu">
        <div class="item"><b>Cloud Computing</b></div>
        <div class="item"><b>账号</b></div>
        <router-link :to="{ name: 'login' }" class="item" exact-active-class="active">{{ $t('clientarea.login') }}</router-link>
        <router-link :to="{ name: 'register' }" class="item" exact-active-class="active">{{ $t('clientarea.register') }}</router-link>
    </div>
</template>

<script>
    export default {
        name: "LeftFixedMenu"
    }
</script>

<style scoped>
    #left-fixed-menu {
        width: 200px;
    }
</style>