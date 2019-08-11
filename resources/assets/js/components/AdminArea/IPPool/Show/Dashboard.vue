<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">基本信息</h3>
            <div class="ui very padded no-shadow segment">
                <div class="ui one column grid" v-if="pool.description">
                    <column name="描述">
                        {{ pool.description }}
                    </column>
                </div>

                <div class="ui four column grid">
                    <column name="首可用IP">
                        {{ pool.human_readable_first_usable_ip }}
                    </column>
                    <column name="末可用IP">
                        {{ pool.human_readable_last_usable_ip }}
                    </column>
                    <column name="网关">
                        {{ pool.human_readable_gateway ? pool.human_readable_gateway : '-' }}
                    </column>
                    <column name="掩码长度">
                        {{ pool.network_bits }}
                    </column>
                </div>

                <div class="ui four column grid">
                    <column name="子网分配掩码长度">
                        {{ pool.subnet_network_bits }}
                    </column>
                    <column name="使用量">
                        <usage-span :entry="pool"></usage-span>
                    </column>
                    <column name="使用率">
                        <usage-percentage-label :entry="pool"></usage-percentage-label>
                    </column>
                    <column name="状态">
                        <label class="ui green label">正常</label>
                    </column>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "../../../ClientArea/ComputeInstance/Show/Information/Column";
    import UsageSpan from "../UsageSpan";
    import UsagePercentageLabel from "../UsagePercentageLabel";
    export default {
        name: "Dashboard",
        components: {UsagePercentageLabel, UsageSpan, Column},
        props: ["pool"]
    }
</script>

<style scoped>

</style>