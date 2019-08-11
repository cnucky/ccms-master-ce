<template>
    <div class="ui grid">
        <div class="three wide column">
            <div class="ui vertical fluid tabular menu">
                <router-link exact-active-class="active" class="item" :to="{name: 'billing.statistics.charge.history.byComputeInstancePackages'}">
                    计算实例规格
                </router-link>
                <router-link active-class="active" class="item" :to="{name: 'billing.statistics.charge.history.byComputeNodes'}">
                    计算节点
                </router-link>
                <router-link active-class="active" class="item" :to="{name: 'billing.statistics.charge.history.byZone'}">
                    可用区
                </router-link>
                <router-link active-class="active" class="item" :to="{name: 'billing.statistics.charge.history.byIPv4Pools'}">
                    IPv4地址池
                </router-link>
                <router-link active-class="active" class="item" :to="{name: 'billing.statistics.charge.history.byIPv6Pools'}">
                    IPv6地址池
                </router-link>
                <router-link active-class="active" class="item" :to="{name: 'billing.statistics.charge.history.byTrafficShareGroups'}">
                    流量共享组
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
        name: "History"
    }
</script>

<style scoped>

</style>