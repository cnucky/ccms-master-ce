<template>
    <div v-if="relativeService" class="ui one column grid">
        <column name="相关服务" :inline="true" :dt-style="dtStyle">
            <template v-if="productType === 0">
                <router-link :to="{name: 'admin.computeInstances.dashboard', params: {id: relativeService.id}}">[计算实例] {{ relativeService.unique_id }} - {{ relativeService.name }}</router-link>
            </template>
            <template v-else-if="productType === 1">
                <router-link :to="{name: 'admin.volumes.index', query: {search: relativeService.id}}">[本地卷] {{ relativeService.unique_id }} - {{ relativeService.capacity }}GiB</router-link>
            </template>
            <template v-else-if="productType === 2">
                <router-link :to="{name: 'ipv4Pools.assignments.index', params: {id: relativeService.pool_id}, query: {search: relativeService.human_readable_first_usable}}">[IPv4] {{ relativeService.human_readable_first_usable }} - {{ relativeService.human_readable_last_usable }}</router-link>
            </template>
            <template v-else-if="productType === 3">
                <router-link :to="{name: 'ipv6Pools.assignments.index', params: {id: relativeService.pool_id}, query: {search: relativeService.human_readable_first_usable}}">[IPv6] {{ relativeService.human_readable_first_usable }} - {{ relativeService.human_readable_last_usable }}</router-link>
            </template>
        </column>
    </div>
</template>

<script>
    import Column from "../ClientArea/ComputeInstance/Show/Information/Column";
    export default {
        name: "AdminRelativeService",
        components: {Column},
        props: ["dtStyle", "productType", "relativeService"]
    }
</script>

<style scoped>

</style>