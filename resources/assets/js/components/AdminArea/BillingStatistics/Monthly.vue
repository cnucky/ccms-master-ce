<template>
    <div class="ui grid">
        <div class="three wide column">
            <div class="ui vertical fluid tabular menu">
                <router-link exact-active-class="active" class="item" :to="{name: 'billing.statistics.monthly.trade'}">
                    充值退款
                </router-link>
                <router-link active-class="active" class="item" :to="{name: 'billing.statistics.monthly.charge'}">
                    收费
                </router-link>
            </div>
        </div>
        <div class="thirteen wide stretched column">
            <slide-fade-transition>
                <router-view></router-view>
            </slide-fade-transition>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Monthly"
    }
</script>

<style scoped>

</style>