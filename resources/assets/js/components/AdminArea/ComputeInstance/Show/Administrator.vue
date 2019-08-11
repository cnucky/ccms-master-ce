<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3>其它信息</h3>
            <div class="ui very padded no-shadow segment">
                <div class="ui two column grid">
                    <column name="节点" :inline="true">
                        <router-link :to="{name: 'computeNodes.show', params: {id: instance.compute_node_id}}">{{ instance.node.name }}</router-link>
                    </column>

                    <column name="VNC密码" :inline="true">
                        {{ instance.vnc_password }}
                    </column>

                    <column name="最近计费" :inline="true">
                        <local-time :time="instance.last_charged_at"></local-time>
                    </column>
                </div>
            </div>
        </div>
        <div class="sixteen wide column">
            <h3 class="ui header">自定义规格</h3>
            <div class="ui top attached info message">
                为空项将使用实例规格中定义的值
            </div>
            <div class="ui bottom attached very padded no-shadow segment">
                <form class="ui form" v-on:submit.prevent="updateOverrideSettings">
                    <div class="two fields">
                        <div class="ui field">
                            <label>vCPU数量</label>
                            <input type="number" min="1" v-model="temporaryInstance.override_vCPU" placeholder="不可大于节点逻辑核心数量">
                        </div>

                        <div class="ui field">
                            <label>物理内存</label>
                            <div class="ui right labeled input">
                                <input type="number" min="1" v-model="temporaryInstance.override_memory" placeholder="单位：MiB">
                                <div class="ui basic label">
                                    MiB
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ui section divider"></div>

                    <div class="ui field">
                        <label>月流量</label>
                        <div class="ui right labeled input">
                            <input type="number" v-model="temporaryInstance.override_traffic" placeholder="< 0即为无限制，如有单独设置入网出网流量限制，将使用入网出网流量限制">
                            <div class="ui basic label">
                                GiB
                            </div>
                        </div>
                    </div>

                    <div class="ui horizontal divider">
                        Or
                    </div>

                    <div class="two fields">
                        <div class="ui field">
                            <label>入网月流量</label>
                            <div class="ui right labeled input">
                                <input type="number" v-model="temporaryInstance.override_inbound_traffic" placeholder="< 0即无限制">
                                <div class="ui basic label">
                                    GiB
                                </div>
                            </div>
                        </div>

                        <div class="ui field">
                            <label>出网月流量</label>
                            <div class="ui right labeled input">
                                <input type="number" v-model="temporaryInstance.override_outbound_traffic" placeholder="< 0即无限制">
                                <div class="ui basic label">
                                    GiB
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="two fields">
                        <div class="ui field">
                            <label>入网带宽</label>

                            <div class="ui right labeled input">
                                <input type="number" v-model="temporaryInstance.override_inbound_bandwidth" placeholder="为0即无限制">
                                <div class="ui basic label">
                                    Mbps
                                </div>
                            </div>
                        </div>

                        <div class="ui field">
                            <label>出网带宽</label>
                            <div class="ui right labeled input">
                                <input type="number" v-model="temporaryInstance.override_outbound_bandwidth" placeholder="为0即无限制">
                                <div class="ui basic label">
                                    Mbps
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ui section divider"></div>

                    <div class="ui field">
                        <label>I/O权重</label>
                        <input type="number" min="10" max="1000" placeholder="范围：10-1000，留空使用默认值" v-model="temporaryInstance.override_io_weight">
                    </div>

                    <div class="two fields">
                        <div class="ui field">
                            <label>读取速度上限</label>

                            <div class="ui right labeled input">
                                <input type="number" v-model="temporaryInstance.override_read_bytes_sec" placeholder="为0即无限制">
                                <div class="ui basic label">
                                    Bps
                                </div>
                            </div>
                        </div>

                        <div class="ui field">
                            <label>每秒读取操作上限</label>

                            <div class="ui right labeled input">
                                <input type="number" v-model="temporaryInstance.override_read_iops_sec" placeholder="为0即无限制">
                                <div class="ui basic label">
                                    操作/秒
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="two fields">
                        <div class="ui field">
                            <label>写入速度上限</label>

                            <div class="ui right labeled input">
                                <input type="number" v-model="temporaryInstance.override_write_bytes_sec" placeholder="为0即无限制">
                                <div class="ui basic label">
                                    Bps
                                </div>
                            </div>
                        </div>

                        <div class="ui field">
                            <label>每秒写入操作上限</label>
                            <div class="ui right labeled input">
                                <input type="number" v-model="temporaryInstance.override_write_iops_sec" placeholder="为0即无限制">
                                <div class="ui basic label">
                                    操作/秒
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ui section divider"></div>

                    <div class="ui field">
                        <label>{{ $t('common.pricePerHour') }}</label>
                        <input type="number" step="0.0001" max="9999.9999" min="0" placeholder="如：0.1235，最多可有四位整数与四位小数" v-model="temporaryInstance.override_price_per_hour">
                    </div>

                    <div class="ui field">
                        <button type="submit" class="ui small teal fluid button" v-bind:class="{loading: isSubmittingCustomSetting}" :disabled="isSubmittingCustomSetting">保存</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="sixteen wide column">
            <h3 class="ui header">高级设置</h3>
            <div class="ui very padded no-shadow segment">
                <form class="ui form" v-on:submit.prevent="updateAdvanceSetting">
                    <div class="ui two fields">
                        <div class="ui field">
                            <label>关闭NWFilter</label>
                            <select ref="nwFilterToggle" class="ui dropdown" v-model="temporaryInstance.no_clean_traffic">
                                <option :value="0">否</option>
                                <option :value="1">是</option>
                            </select>
                        </div>
                        <div class="ui field">
                            <label>状态</label>
                            <select ref="statusSelect" class="ui dropdown" v-model="temporaryInstance.status">
                                <option :value="0">{{ $t('status.computeInstance.0') }}</option>
                                <option :value="1">{{ $t('status.computeInstance.1') }}</option>
                                <option :value="2">{{ $t('status.computeInstance.2') }}</option>
                                <option :value="5">{{ $t('status.computeInstance.5') }}</option>
                                <option :value="6">{{ $t('status.computeInstance.6') }}</option>
                                <option :value="8">{{ $t('status.computeInstance.8') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="ui field">
                        <button class="ui small teal fluid button" v-bind:class="{loading: isSubmittingAdvanceSetting}" :disabled="isSubmittingAdvanceSetting">保存</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="sixteen wide column">
            <h3 class="ui header">强制删除</h3>
            <h5 class="ui top attached negative message">
                警告：强制删除仅删除系统中的记录，并不会释放相关资源，如需释放实例的资源，请使用[设置]标签页的“销毁实例”
            </h5>
            <div class="ui very padded no-shadow bottom attached segment" v-bind:class="{loading: isForceDeleting}">
                <div class="ui form">
                    <div class="ui field">
                        <div ref="forceDeleteVolumeCheckbox" class="ui checkbox">
                            <label>同时强制删除已连接到本实例的卷</label>
                            <input type="checkbox" v-model="temporaryInstanceSetting.destroy.deleteAttachedVolumes">
                        </div>
                    </div>

                    <div class="ui field">
                        <div ref="releaseExtraIPCheckbox" class="ui checkbox">
                            <label>同时释放已绑定到本实例的弹性IP地址</label>
                            <input type="checkbox" v-model="temporaryInstanceSetting.destroy.releaseExtraIPs">
                        </div>
                    </div>
                </div>
                <button style="display: block; margin-top: 30px;" class="ui inverted fluid red button" :disabled="isForceDeleting" v-on:click="forceDelete">强制删除</button>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "../../../ClientArea/ComputeInstance/Show/Information/Column";
    import LocalTime from "../../../LocalTime";
    export default {
        name: "Administrator",
        components: {LocalTime, Column},
        props: ["instance", "operationRoutePrefix"],
        data: function () {
            return {
                isSubmittingCustomSetting: false,
                isSubmittingAdvanceSetting: false,
                isForceDeleting: false,
                temporaryInstance: _.cloneDeep(this.instance),
                temporaryInstanceSetting: {
                    destroy: {
                        deleteAttachedVolumes: false,
                        releaseExtraIPs: false,
                    },
                },
            };
        },
        mounted: function () {
            $(this.$refs.nwFilterToggle).dropdown();
            $(this.$refs.statusSelect).dropdown();
            $(this.$refs.forceDeleteVolumeCheckbox).checkbox();
            $(this.$refs.releaseExtraIPCheckbox).checkbox();
        },
        methods: {
            updateOverrideSettings: function () {
                this.isSubmittingCustomSetting = true;
                axios.patch(route("admin.computeInstances.changeCustomSize", [this.instance.id]), this.temporaryInstance, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$emit("refresh");
                            this.positiveFloatingMessage("保存成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmittingCustomSetting = false;
                    })
                ;
            },
            updateAdvanceSetting: function () {
                this.isSubmittingAdvanceSetting = true;
                axios.patch(route("admin.computeInstances.changeAdvanceSettings", [this.instance.id]), this.temporaryInstance, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$emit("refresh");
                            this.positiveFloatingMessage("保存成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmittingAdvanceSetting = false;
                    })
                ;
            },
            forceDelete: function () {
                if (confirm("确定强制删除实例？")) {
                    this.isForceDeleting = true;
                    axios.post(route("admin.computeInstances.forceDelete", [this.instance.id]), this.temporaryInstanceSetting.destroy, {vueInstance: this})
                        .then((response) => {
                            var data = response.data;
                            if (data.result) {
                                this.$router.push({name: "admin.computeInstances.index"});
                                this.positiveFloatingMessage("已强制删除");
                            } else {
                                this.$globalErrnoHandler(data);
                            }
                        })
                        .catch(this.$axiosCatchError2Console)
                        .then(() => {
                            this.isForceDeleting = false;
                        })
                    ;
                }
            }
        },
        watch: {
            instance: function (newValue, oldValue) {
                this.temporaryInstance = _.cloneDeep(newValue);
            }
        }
    }
</script>

<style scoped>

</style>