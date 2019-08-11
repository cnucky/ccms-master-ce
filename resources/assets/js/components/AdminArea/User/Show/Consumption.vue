<template>
    <div class="ui grid">
        <div class="three wide column">
            <div class="ui vertical fluid tabular menu">
                <router-link exact-active-class="active" class="item" :to="{name: 'users.consumption', params: {id: userData.user.id}}">
                    概览
                </router-link>
                <router-link active-class="active" class="item" :to="{name: 'users.dailyConsumption', params: {id: userData.user.id}}">
                    每日
                </router-link>
                <router-link active-class="active" class="item" :to="{name: 'users.monthlyConsumption', params: {id: userData.user.id}}">
                    每月
                </router-link>
            </div>
        </div>
        <div class="thirteen wide stretched column">
            <slide-fade-transition>
                <router-view :user-data="userData"></router-view>
            </slide-fade-transition>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Consumption",
        props: ["userData"],
    }
</script>

<style scoped>

</style>