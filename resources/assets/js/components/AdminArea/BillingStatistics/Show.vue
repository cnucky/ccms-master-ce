<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">财务统计</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui secondary pointing menu">
                <router-link exact-active-class="active" class="item teal" :to="{name: 'billing.statistics.dashboard'}">
                    概览
                </router-link>
                <router-link active-class="active" class="item teal" :to="{name: 'billing.statistics.daily.trade'}">
                    每日
                </router-link>
                <router-link active-class="active" class="item teal " :to="{name: 'billing.statistics.monthly.trade'}">
                    每月
                </router-link>
                <router-link active-class="active" class="item teal" :to="{name: 'billing.statistics.charge.history.byComputeInstancePackages'}">
                    历史
                </router-link>
            </div>
        </div>

        <div class="sixteen wide stretched column">
            <slide-fade-transition>
                <router-view></router-view>
            </slide-fade-transition>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Show"
    }
</script>

<style scoped>

</style>