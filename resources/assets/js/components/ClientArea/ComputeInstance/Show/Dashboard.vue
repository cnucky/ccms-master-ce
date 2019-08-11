<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">基本信息</h3>
            <div class="ui very padded no-shadow segment">
                <div>
                    <div class="ui four column grid">
                        <column name="状态">
                            <power-status-icon-label
                                    v-bind:power-status="instance.power_status"></power-status-icon-label>
                            <power-status-text :instance="instance"></power-status-text>
                        </column>

                        <column name="ID">
                            {{ instance.unique_id }}
                        </column>

                        <column name="规格">
                            <template v-if="instance.package.category">{{ instance.package.category.name }} - {{ instance.package.name }}</template>
                            <template v-else>-</template>
                        </column>

                        <column name="区域">
                            <i :class="instance.node.zone.region.icon_class"></i> {{ instance.node.zone.region.name }} - {{ instance.node.zone.name }}
                        </column>
                    </div>

                    <div class="ui section divider"></div>

                    <div class="ui two column grid">
                        <column name="UUID" :inline="true">
                            {{ instance.uuid }}
                        </column>

                        <div class="column">
                            <div class="ui two column grid">
                                <column name="名称" :inline="true">
                                    {{ instance.name }}
                                </column>
                                <column name="创建于" :inline="true">
                                    {{ $toLocalTime(instance.created_at) }}
                                </column>
                            </div>
                        </div>
                    </div>

                    <div class="ui section divider"></div>

                    <div class="ui two column grid">
                        <column name="公网IPv4" :inline="true">
                            <div ref="ipv4" class="ip link-font" v-if="instance.network_interfaces[0].ipv4_addresses && instance.network_interfaces[0].ipv4_addresses.length" v-on:click="$copyText(instance.network_interfaces[0].ipv4_addresses[0].human_readable_first_usable)" data-content="点击复制">{{ instance.network_interfaces[0].ipv4_addresses[0].human_readable_first_usable }}</div>
                        </column>

                        <column name="公网IPv6" :inline="true">
                            <div ref="ipv6" class="ip link-font" v-if="instance.network_interfaces[0].ipv6_addresses && instance.network_interfaces[0].ipv6_addresses.length" v-on:click="$copyText(instance.network_interfaces[0].ipv6_addresses[0].human_readable_first_usable)"  data-content="点击复制">{{ instance.network_interfaces[0].ipv6_addresses[0].human_readable_first_usable }}</div>
                        </column>
                    </div>
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <h3 class="ui header">配置信息</h3>
            <div class="ui very padded no-shadow segment">
                <div>
                    <div class="ui four column grid">
                        <column name="类型">
                            {{ instance.machine_type === 0 ? 'Q35' : 'i440FX' }}
                        </column>

                        <column name="引导程序">
                            {{ instance.use_legacy_bios ? 'BIOS' : 'UEFI' }}
                        </column>

                        <column name="vCPU">
                            {{ instance.client_instance_size.vCPU }} 个
                        </column>

                        <column name="物理内存">
                            {{ instance.client_instance_size.memory }} MiB
                        </column>
                    </div>

                    <div class="ui section divider"></div>

                    <div class="ui two column grid">
                        <div class="column">
                            <div class="ui two column grid">
                                <column name="上行带宽">
                                    <template v-if="instance.client_instance_size.outbound_bandwidth">
                                        {{ instance.client_instance_size.outbound_bandwidth }} Mbps
                                    </template>
                                    <template v-else>
                                        无限制
                                    </template>
                                </column>

                                <column name="下行带宽">
                                    <template v-if="instance.client_instance_size.inbound_bandwidth">
                                        {{ instance.client_instance_size.inbound_bandwidth }} Mbps
                                    </template>
                                    <template v-else>
                                        无限制
                                    </template>
                                </column>
                            </div>
                        </div>

                        <div class="column">
                            <template v-if="instance.client_instance_size.traffic ">
                                <column name="流量">
                                    {{ instance.client_instance_size.traffic }} GiB/月
                                </column>
                            </template>
                            <template v-else>
                                <div class="ui two column grid">
                                    <column name="免费上行流量">
                                        <template v-if="instance.client_instance_size.outbound_traffic === null || instance.client_instance_size.outbound_traffic < 0">
                                            无限
                                        </template>
                                        <template>
                                            {{ instance.client_instance_size.outbound_traffic }} GiB/月
                                        </template>
                                        <template v-if="instance.node.zone.traffic_share_group">
                                            (<amount :amount="instance.node.zone.traffic_share_group.price_per_tx_gib"></amount>/GiB)
                                        </template>
                                    </column>

                                    <column name="免费下行流量">
                                        无限 GiB/月
                                    </column>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "./Information/Column";
    import PowerStatusIconLabel from "../../../ComputeInstance/PowerStatusIconLabel";
    import PowerStatusText from "../../../ComputeInstance/PowerStatusText";
    import Amount from "../../../CreditRecord/Amount";

    export default {
        name: "Dashboard",
        components: {Amount, PowerStatusText, PowerStatusIconLabel, Column},
        props: ["instance"],
        mounted: function () {
            $(this.$refs.ipv4)
                .popup({
                    distanceAway: 40,
                })
            ;
            $(this.$refs.ipv6)
                .popup({
                    distanceAway: 40,
                })
            ;
        }
    }
</script>

<style scoped>
    .link-font {
        color: #0069ff;
        cursor: pointer;
    }
</style>