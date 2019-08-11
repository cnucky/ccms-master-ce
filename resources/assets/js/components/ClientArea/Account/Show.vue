<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">帐号详情</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui secondary pointing menu">
                <router-link class="item teal" :to="{name: 'account.profile'}" exact-active-class="active">帐号信息</router-link>
                <router-link class="item teal" :to="{name: 'account.password'}" exact-active-class="active">更改密码</router-link>
            </div>
        </div>

        <div class="sixteen wide column">
            <slide-fade-transition>
                <router-view ref="child"></router-view>
            </slide-fade-transition>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Show",
        computed: {
            vueInstance: function () {
                return this;
            }
        }
    }
</script>

<style scoped>

</style>