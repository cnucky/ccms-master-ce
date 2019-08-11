<template>
    <div class="ui grid">
        <div class="ui sixteen wide column">
            <h3 class="ui header">信息概览</h3>
            <div class="ui very padded no-shadow segment">
                <div class="ui two column grid">
                    <column name="Host" :inline="true">
                        {{ computeNode.host }}
                    </column>
                    <column name="UUID" :inline="true">
                        {{ computeNode.uuid }}
                    </column>
                </div>

                <div class="ui two column grid">
                    <column name="最近Ping请求时间" :inline="true">
                        {{ $toLocalTime(computeNode.last_communicated_at) }}
                    </column>
                    <div class="column">
                        <div class="ui two column grid">
                            <column name="区域" :inline="true">
                                <router-link :to="{name: 'zones.dashboard', params: {id: computeNode.zone.id}}"><i :class="computeNode.zone.region.icon_class"></i> {{ computeNode.zone.region.name }} - {{ computeNode.zone.name }}</router-link>
                            </column>
                            <column name="节点状态" :inline="true">
                                <status-label :entry="computeNode"></status-label>
                            </column>
                        </div>
                    </div>
                </div>

                <div class="ui section divider"></div>

                <div class="ui two column grid">
                    <column name="服务器证书指纹">
                        {{ trustedCertificateInformation.fingerprint }}
                    </column>
                    <column name="服务器证书Subjects">
                        {{ trustedCertificateInformation.name }}
                    </column>
                </div>

                <div class="ui section divider"></div>

                <div class="ui two column grid">
                    <column name="客户端证书指纹">
                        {{ clientCertificateInformation.fingerprint }}
                    </column>
                    <div class="column">
                        <div class="ui two column grid">
                            <column name="客户端证书Subjects">
                                {{ clientCertificateInformation.name }}
                            </column>
                            <column name="客户端证书序列号">
                                {{ clientCertificateInformation.serialNumber }}
                            </column>
                        </div>
                    </div>
                </div>

                <div class="ui section divider"></div>

                <div class="ui two column grid">
                    <column name="CPU" :inline="true">
                        <template v-if="computeNode.cpu_model">{{ computeNode.cpu_model }} </template>
                        <template v-if="computeNode.cpu_cores">
                            ({{ computeNode.cpu_cores }} x processor<template v-if="computeNode.cpu_cores > 1">s</template>)
                        </template>
                    </column>
                    <div class="column">
                        <div class="ui two column grid">
                            <column name="CPU使用率" :inline="true">
                                <template v-if="computeNode.cpu_utilization !== null">
                                    <span>{{ computeNode.cpu_utilization.toFixed(2) }}%</span>
                                </template>
                                <template v-else>-</template>
                            </column>
                            <column name="平均负载" :inline="true">
                                <template v-if="nodeStatus.loadAverage">
                                    {{ nodeStatus.loadAverage.one_minute_average.toFixed(2) }},
                                    {{ nodeStatus.loadAverage.five_minutes_average.toFixed(2) }},
                                    {{ nodeStatus.loadAverage.fifteen_minutes_average.toFixed(2) }}
                                </template>
                                <template v-else>-</template>
                            </column>
                        </div>
                    </div>
                </div>

                <div class="ui four column grid">
                    <column name="物理内存" :inline="true">
                        <template v-if="computeNode.total_memory_capacity_in_mib_unit !== null && computeNode.current_memory_free_in_mib_unit !== null">
                            {{ ((computeNode.total_memory_capacity_in_mib_unit - computeNode.current_memory_free_in_mib_unit) / 1024).toFixed(2) }}GiB / {{ (computeNode.total_memory_capacity_in_mib_unit / 1024).toFixed(2) }} GiB
                        </template>
                        <template v-else>-</template>
                    </column>
                    <column name="已分配物理内存" :inline="true">
                        <template v-if="computeNode.total_allocated_memory_in_mib_unit !== null">
                            {{ (computeNode.total_allocated_memory_in_mib_unit / 1024).toFixed(2) }}GiB
                        </template>
                        <template v-else>-</template>
                    </column>
                    <column name="储存空间" :inline="true">
                        <template v-if="computeNode.total_disk_capacity_in_gib_unit !== null && computeNode.current_disk_free_in_gib_unit !== null">
                            {{ (computeNode.total_disk_capacity_in_gib_unit - computeNode.current_disk_free_in_gib_unit).toFixed(2) }}GiB / {{ computeNode.total_disk_capacity_in_gib_unit.toFixed(2) }}GiB
                        </template>
                        <template v-else>
                            -
                        </template>
                    </column>
                    <column name="已分配储存" :inline="true">
                        <template v-if="computeNode.total_allocated_disk_in_gib_unit !== null">
                            {{ computeNode.total_allocated_disk_in_gib_unit.toFixed(2) }}GiB
                        </template>
                        <template v-else>
                            -
                        </template>
                    </column>
                </div>

                <div class="ui four column grid">
                    <column name="实例数量" :inline="true">
                        {{ computeNode.instance_counter }}
                    </column>
                    <column name="本地卷数量" :inline="true">
                        {{ computeNode.local_volume_counter }}
                    </column>
                    <column name="Uptime" :inline="true">
                        {{ [computeNode.uptime, 'seconds'] | duration('humanize') }}
                    </column>
                </div>
            </div>
        </div>

        <div class="eight wide column">
            <div class="ui very padded no-shadow segment">
                <button class="ui inverted red fluid button" v-on:click="refreshAllocatedCounter" :disabled="isRefreshingCounter">刷新已分配内存&储存计数器</button>
            </div>
        </div>

        <div class="eight wide column">
            <div class="ui very padded no-shadow segment">
                <button class="ui inverted red fluid button" v-on:click="refreshInstanceAndLocalVolumeCounter" :disabled="isRefreshingCounter">刷新实例&本地卷计数器</button>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "../../../ClientArea/ComputeInstance/Show/Information/Column";
    import OnlineStatus from "../OnlineStatus";
    import StatusLabel from "../../../StatusLabel";
    export default {
        name: "Dashboard",
        components: {StatusLabel, OnlineStatus, Column},
        props: ["computeNode", "trustedCertificateInformation", "clientCertificateInformation", "serverTime", "nodeStatus"],
        data: function () {
            return {
                isRefreshingCounter: false,
            };
        },
        methods: {
            refreshAllocatedCounter: function () {
                this.isRefreshingCounter = true;
                axios.post(route("computeNodes.refreshAllocatedCounter", [this.computeNode.id]), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.retrieveData(data);
                            this.positiveFloatingMessage("计数器刷新成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isRefreshingCounter = false;
                    })
                ;
            },
            refreshInstanceAndLocalVolumeCounter: function () {
                this.isRefreshingCounter = true;
                axios.post(route("computeNodes.refreshComputeInstanceAndLocalVolume", [this.computeNode.id]), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.retrieveData(data);
                            this.positiveFloatingMessage("计数器刷新成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isRefreshingCounter = false;
                    })
                ;
            },
            retrieveData: function (data) {
                this.$emit("data-updated", data);
            }
        }
    }
</script>

<style scoped>

</style>