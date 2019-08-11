<template>
    <div v-bind:class="{'init-loading': isInitLoading}">
        <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
        <semantic-ui-loader v-if="isInitLoading" v-bind:is-active="isInitLoading" v-bind:loader-class="'large'">
            Loading...
        </semantic-ui-loader>
        <div v-else class="ui grid">
            <div class="twelve wide middle aligned column" style="
    display: flex;
    flex-direction: row;
    -webkit-box-align: center;
    align-items: center;">
                <power-status-icon-label v-bind:power-status="instance.power_status"></power-status-icon-label>
                <div style="display: inline-block;">
                    <h2 class="ui header instance-name">
                        {{ instance.name }}
                    </h2>

                    <div class="instance-size">
                        {{ instance.client_instance_size.vCPU }} vCPU/ {{instance.client_instance_size.memory }} MiB RAM
                    </div>
                </div>
            </div>

            <div class="four wide middle aligned column">
                <power-toggle :instance="instance" v-on:click="powerToggle" :processing="isPowerToggleProcessing"></power-toggle>
                <button class="ui small button" style="float: right;" :disabled="instance.power_status === 0" v-on:click="resetInstance" v-bind:class="{ loading: isResetProcessing }">重启</button>
            </div>

            <div class="sixteen wide column">
                <div class="ui secondary pointing menu">
                    <router-link class="item teal" :to="{name: 'admin.computeInstances.dashboard', params: {id: instance.id}}" exact-active-class="active">
                        概览
                    </router-link>
                    <router-link class="item teal" :to="{name: 'admin.computeInstances.statistics', params: {id: instance.id}}" exact-active-class="active">
                        资源统计图
                    </router-link>
                    <router-link class="item teal" :to="{name: 'admin.computeInstances.volumes', params: {id: instance.id}}" active-class="active">
                        卷
                    </router-link>
                    <div class="item teal disabled">
                        快照
                    </div>
                    <router-link class="item teal" :to="{name: 'admin.computeInstances.virtualMedias', params: {id: instance.id}}" active-class="active">
                        虚拟介质
                    </router-link>
                    <router-link class="item teal" :to="{name: 'admin.computeInstances.network', params: {id: instance.id}}" active-class="active">
                        网络
                    </router-link>
                    <router-link class="item teal" :to="{name: 'admin.computeInstances.settings', params: {id: instance.id}}" active-class="active">
                        设置
                    </router-link>
                    <router-link class="item teal" :to="{name: 'admin.computeInstances.histories', params: {id: instance.id}}" active-class="active">
                        操作历史
                    </router-link>
                    <div class="right menu">
                        <router-link class="item teal" :to="{name: 'admin.computeInstances.administrator', params: {id: instance.id}}" active-class="active">
                            <i class="cogs icon"></i> 管理设置
                        </router-link>
                        <a class="ui item teal" v-bind:class="{disabled: isGettingSystemPassword}"  v-on:click.prevent="showPassword"><i class="key icon"></i> 获取系统密码</a>
                        <a class="ui item teal" target="_blank" :href="route('admin.computeInstances.console', [instance.id])">
                            <i class="computer icon"></i> 控制台
                        </a>
                    </div>
                </div>
            </div>

            <div class="sixteen wide column">
                <slide-fade-transition>
                    <router-view ref="child" operation-route-prefix="admin." :instance="instance" :show-component="getVueInstance" v-on:operationRequestCreated="operationRequestCreated" v-on:instanceUpdated="(callback) => {callback(instance);}" v-on:refresh="updateInstanceInformation"></router-view>
                </slide-fade-transition>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "./../../ClientArea/ComputeInstance/Show/Information/Column";
    import Dashboard from "./../../ClientArea/ComputeInstance/Show/Dashboard";
    import Volumes from "./../../ClientArea/ComputeInstance/Show/Volumes";
    import PowerStatusIconLabel from "../../ComputeInstance/PowerStatusIconLabel";
    import PowerToggle from "../../ComputeInstance/PowerToggle";
    import ShowUtils from "./../../ClientArea/ComputeInstance/Show/ShowUtils";
    import Utils from "./../../ClientArea/ComputeInstance/Utils";
    import FinishedOperationRequestHandler from "./../../ComputeInstance/FinishedOperationRequestHandler";

    export default {
        name: "Show",
        mixins: [ShowUtils, Utils, FinishedOperationRequestHandler],
        components: {PowerToggle, PowerStatusIconLabel, Column, Dashboard, Volumes},
        data: function () {
            return {
                utilsRoutePrefix: "admin.",

                isInitLoading: true,
                isLoading: false,

                instance: {},

                isDestroyed: false,

                isGettingSystemPassword: false,
            };
        },
        mounted: function () {
            this.startTimeoutHandler();
        },
        destroyed: function () {
            this.isDestroyed = true;
        },
        created: function () {
            this.updateInstanceInformation();
        },
        updated: function () {
        },
        methods: {
            updateInstanceInformation: function (onComplete = undefined) {
                axios.get(route('admin.computeInstances.show', [this.$router.currentRoute.params.id]))
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.instance = data.instance;
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    }).catch((error) => {
                        console.log(error);
                    }).then(() => {
                        this.isInitLoading = false;
                        if (typeof onComplete === "function")
                            onComplete();
                    })
                ;
            },
            powerToggle: function () {
                var powerAction = "on";
                if (this.instance.power_status)
                    powerAction = "off";
                this.powerAction(powerAction);
            },
            resetInstance: function () {
                this.powerAction("reset");
            },
            powerAction: function (powerAction) {
                // this.isLoading = true;
                axios.post(route('admin.computeInstances.power.' + powerAction, [this.instance.id]), null, { vueInstance: this })
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            // this.positiveFloatingMessage("操作请求已提交");
                            this.instance.processing_operation_requests.push(data.operationRequest);
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    }).catch((error) => {
                    console.log(error);
                }).then(() => {
                    // this.isLoading = false;
                })
                ;
            },
            showPassword: function () {
                if (this.isGettingSystemPassword)
                    return;
                this.getSystemPasswordAndShow(this.instance, () => { this.isGettingSystemPassword = true; }, () => { this.isGettingSystemPassword = false });
            },
            startTimeoutHandler: function () {
                setTimeout(this.timeoutHandler, 2000);
            },
            timeoutHandler: function () {
                if (this.isDestroyed)
                    return;

                var operationRequestList = this.operationRequestList;
                if (operationRequestList.length) {
                    axios.post(route('admin.computeInstanceOperationRequests.query'), {operationRequestList: operationRequestList}, {vueInstance: this})
                        .then((response) => {
                            var data = response.data;
                            if (data.result) {
                                var finishedItems = [];

                                var instanceDeleted = false;

                                // Retrieve finished operation requests
                                data.operationRequests.forEach((item) => {
                                    if (item.operation_status === 2 || item.operation_status === 3) {
                                        finishedItems.push(item);
                                        if (item.type === 6 && item.operation_status === 2)
                                            instanceDeleted = true;
                                        /*
                                        else if (item.type === 12) {
                                            if (item.operation_status === 2) {
                                                this.positiveFloatingMessage("主机名更改成功");
                                            } else {
                                                this.negativeFloatingMessage("主机名更改失败");
                                            }
                                        } else if (item.type === 13) {
                                            if (item.operation_status === 2) {
                                                this.positiveFloatingMessage("系统密码重置成功");
                                            } else {
                                                this.negativeFloatingMessage("系统密码重置失败");
                                            }
                                        } else if (item.type === 15) {
                                            if (item.operation_status === 2) {
                                                this.positiveFloatingMessage("公共镜像更换成功");
                                            } else {
                                                this.negativeFloatingMessage("公共镜像更换失败");
                                            }
                                        }
                                        */
                                    }
                                });

                                if (instanceDeleted) {
                                    this.$router.push({name: "computeInstances.index"});
                                    this.positiveFloatingMessage("实例销毁成功");
                                } else if (finishedItems.length) {
                                    this.finishedOperationRequestHandler(finishedItems);
                                    // If there any operation request finished, update instance information
                                    this.updateInstanceInformation(() => {
                                    });

                                    if (typeof this.$refs.child.filter === "function") {
                                        this.$refs.child.filter(true);
                                    }
                                }
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        })
                        .then(() => {
                            this.startTimeoutHandler();
                        })
                    ;
                } else {
                    this.startTimeoutHandler();
                }

            },
            operationRequestCreated: function (operationRequest) {
                this.instance.processing_operation_requests.push(operationRequest);
            },
            getVueInstance: function () {
                return this;
            }
        },
        computed: {
            isPowerToggleProcessing: function () {
                return this.isOperationRequestTypeExists(1) || this.isOperationRequestTypeExists(2);
            },
            isResetProcessing: function () {
                return this.isOperationRequestTypeExists(5);
            },
            operationRequestList: function () {
                var list = [];
                this.instance.processing_operation_requests.forEach((item) => {
                    list.push(item.id);
                });
                return list;
            }
        }
    }
</script>

<style scoped>
    .init-loading {
        position: relative;
        height: 100%;
    }

    .instance-name {
        color: #676767;
        margin-bottom: 2px;
    }

    .instance-size {
        color: #676767;
    }

    .ui.secondary >>> a.item.teal:hover {
        color: #00b5ad!important;
    }
</style>