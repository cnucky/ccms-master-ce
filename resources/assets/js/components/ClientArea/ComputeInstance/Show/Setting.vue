<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">基本信息</h3>
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isUpdatingBasic}">
                <form class="ui form" v-on:submit.prevent="updateBasic">
                    <div class="ui field">
                        <label>名称：</label>
                        <input type="text" v-model="temporaryInstanceSetting.basic.name">
                    </div>
                    <div class="ui field">
                        <label>描述：</label>
                        <textarea rows="2" maxlength="255" v-model="temporaryInstanceSetting.basic.description"></textarea>
                    </div>

                    <button class="ui small green button" style="display: block; margin-left: auto;">保存</button>
                </form>
            </div>
        </div>

        <div class="sixteen wide column">
            <div class="ui two column grid">
                <div class="column">
                    <h3 class="ui header">更改主机名</h3>
                    <div class="ui padded no-shadow segment" v-bind:class="{loading: isChangingHostname}">
                        <div class="ui warning message">
                            <ul>
                                <li>更改主机名功能仅支持公共镜像</li>
                                <li>请确定实例的操作系统已启动，且未关闭Guest Agent服务</li>
                                <li>主机名更改成功后，可能重启操作系统后方可生效</li>
                            </ul>
                        </div>
                        <form class="ui form" v-on:submit.prevent="changeHostname">
                            <div class="ui field">
                                <label>主机名：</label>
                                <input type="text" maxlength="15" v-model="temporaryInstanceSetting.hostname">
                            </div>

                            <button type="submit" class="ui small green fluid button">提交</button>
                        </form>
                    </div>
                </div>

                <div class="column">
                    <h3 class="ui header">系统密码重置</h3>
                    <div class="ui padded no-shadow segment" v-bind:class="{loading: isResettingOSPassword}">
                        <div class="ui warning message">
                            <ul>
                                <li>系统密码重置功能仅支持公共镜像</li>
                                <li>请确定实例的操作系统已启动，且未关闭Guest Agent服务</li>
                                <li>密码重置成功后，点击页面右上角的“获取系统密码”即可查看新密码</li>
                            </ul>
                        </div>
                        <form class="ui form" v-on:submit.prevent="resetOSPassword">
                            <div class="ui two fields">
                                <div class="ui field">
                                    <label>操作系统密码</label>
                                    <input type="text" value="由系统自动生成" disabled>
                                </div>
                                <div class="ui field">
                                    <label>确认密码</label>
                                    <input type="text" value="由系统自动生成" disabled>
                                </div>
                            </div>
                            <button type="submit" class="ui small fluid red button">重置操作系统密码</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <div class="ui two column grid">
                <div class="column">
                    <h3 class="ui header">重配置操作系统网络</h3>
                    <div class="ui padded no-shadow segment" v-bind:class="{loading: isReconfiguringOSNetwork}">
                        <div class="ui warning message">
                            <ul>
                                <li>更改主机名功能仅支持公共镜像</li>
                                <li>请确定实例的操作系统已启动，且未关闭Guest Agent服务</li>
                                <li>网络配置重置成功后，可能重启操作系统后方可生效</li>
                            </ul>
                        </div>
                        <form class="ui form" v-on:submit.prevent="reconfigureOSNetwork">
                            <button type="submit" class="ui small red fluid button">重置</button>
                        </form>
                    </div>
                </div>

                <div class="column">
                    <h3 class="ui header">重配置实例</h3>
                    <div class="ui padded no-shadow segment" v-bind:class="{loading: isReconfiguring}">
                        <p>使用此功能可根据实例当前的设置，重新配置虚拟机，可用于应用已保存的设置。</p>
                        <div class="ui form">
                            <div class="ui field" style="margin-top: 30px;">
                                <div class="ui checkbox">
                                    <input id="eject-medias" type="checkbox" v-model="reconfigureData.ejectMedias">
                                    <label for="eject-medias">弹出所有虚拟介质</label>
                                </div>
                            </div>
                            <div class="ui field">
                                <button class="ui small fluid red button" v-on:click="reconfigure" style="margin-top: 27px;">重配置</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <h3 class="ui header">更换实例规格</h3>
            <div class="ui padded no-shadow segment" v-bind:class="{loading: isChangingPackage}">
                <div ref="changeInstancePackageSelect" class="ui fluid selection dropdown">
                    <i class="dropdown icon"></i>
                    <div class="default text">请选择实例规格</div>
                    <div class="menu">
                        <template v-for="packageCategory in packageCategories">
                            <div class="divider"></div>
                            <div class="header">
                                <i class="cubes icon"></i>
                                {{ packageCategory.name }}
                            </div>
                            <template v-for="instancePackage in packageCategory.packages">
                                <div class="item" v-if="instance.compute_instance_package_id !== instancePackage.id && instancePackage.stocks !== 0" v-on:click="selectedPackage = instancePackage">
                                    {{ instancePackage.name }} - {{ instancePackage.vCPU }} vCPU / {{ instancePackage.memory }} MiB RAM
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
                <button type="button" class="ui small teal fluid button" style="margin-top: 20px;" :disabled="isChangingPackage" v-on:click="changePackage">更改</button>
            </div>
        </div>

        <div class="sixteen wide column">
            <div class="ui divider"></div>
        </div>

        <div class="sixteen wide column">
            <h3 class="ui header">更换公共镜像</h3>
            <h5 class="ui top attached negative message">
                <i class="warning icon"></i> 警告：更换公共进行将清除首选启动盘的数据，请做好备份！
            </h5>
            <div class="ui padded bottom attached segment" v-bind:class="{loading: isChangingPublicImage}">
                <div class="ui grid">
                    <div class="five wide column">
                        <div ref="imageCategorySelect" class="ui basic fluid medium dropdown button">
                            <input type="hidden" v-on:change="selectedImageCategoryChanged">
                            <span class="text">请选择操作系统</span>
                            <i class="dropdown icon"></i>
                            <div class="menu">
                                <div v-for="(category, index) in availablePublicImages" class="item"
                                     :data-value="index">
                                    <i :class="category.icon_class"></i> {{ category.name }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="eleven wide column">
                        <image-select :images="selectedImageCategoryIndex === null ? [] : availablePublicImages[selectedImageCategoryIndex].images" v-model="selectedImageId"></image-select>
                    </div>

                    <div class="sixteen wide column">
                        <div v-if="selectedImageId !== null" class="ui fluid small red button" v-on:click="changePublicImage">确认更换公共镜像</div>
                        <div v-else class="ui fluid small red button disabled">请选择镜像</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <h3 class="ui header">高级设置</h3>

            <h5 class="ui top attached warning message">
                <i class="warning icon"></i> 更改高级设置可能使虚拟机无法正常运行，请慎重操作！
            </h5>
            <div class="ui very padded no-shadow bottom attached segment">
                <form class="ui form" v-on:submit.prevent="saveAdvanceSetting">
                    <div class="two fields">
                        <div class="ui field">
                            <label>芯片组：</label>
                            <select class="ui dropdown" v-model="temporaryInstanceSetting.advance.machineType">
                                <option value="0">Q35 + ICH9</option>
                                <option value="1">i440FX + PIIX</option>
                            </select>
                        </div>

                        <div class="ui field">
                            <label>引导程序：</label>
                            <select class="ui dropdown" v-model="temporaryInstanceSetting.advance.useLegacyBIOS">
                                <option value="0">UEFI</option>
                                <option value="1">BIOS</option>
                            </select>
                        </div>
                    </div>

                    <div class="ui field">
                        <div class="ui checkbox">
                            <input id="apply-advance-setting" type="checkbox" v-model="temporaryInstanceSetting.advance.saveAndApply">
                            <label>保存并应用</label>
                        </div>
                    </div>

                    <button type="submit" class="ui small red button" style="display: block; margin-left: auto;" v-bind:class="{loading: isSubmittingReconfiguring}" :disabled="isSubmittingReconfiguring">保存</button>
                </form>
            </div>
        </div>

        <div class="sixteen wide wide column" id="destroy-instance">
            <h3 class="ui header">销毁实例</h3>

            <h5 class="ui top attached negative message">
                警告：销毁实例是不可逆的操作，被销毁的数据将无法恢复，被释放的IP地址无法保证再次可申请！
            </h5>
            <div class="ui very padded no-shadow bottom attached segment" v-bind:class="{loading: isDestroying}">
                <div class="ui form">
                    <div class="ui field">
                        <div class="ui checkbox">
                            <label>同时释放已连接到本实例的卷</label>
                            <input type="checkbox" v-model="temporaryInstanceSetting.destroy.deleteAttachedVolumes">
                        </div>
                    </div>

                    <div class="ui field">
                        <div class="ui checkbox">
                            <label>同时释放已绑定到本实例的弹性IP地址</label>
                            <input type="checkbox" v-model="temporaryInstanceSetting.destroy.releaseExtraIPs">
                        </div>
                    </div>
                </div>
                <button style="display: block; margin-top: 30px;" class="ui inverted fluid red button" :disabled="isDestroying" v-on:click="destroyInstance">销毁实例</button>
            </div>
        </div>
    </div>
</template>

<script>
    import ShowUtils from "./ShowUtils";
    import ImageSelect from "../../../ComputeInstance/ImageSelect";

    export default {
        name: "Setting",
        components: {ImageSelect},
        mixins: [ShowUtils],
        props: ["instance", "showComponent", "operationRoutePrefix"],
        data: function () {
            return {
                temporaryInstanceSetting: {
                    basic: {
                        name: this.instance.name,
                        description: this.instance.description,
                    },
                    hostname: this.instance.hostname,
                    advance: {
                        machineType: this.instance.machine_type.toString(),
                        useLegacyBIOS: this.instance.use_legacy_bios.toString(),
                        saveAndApply: true,
                    },
                    destroy: {
                        deleteAttachedVolumes: false,
                        releaseExtraIPs: false,
                    },
                },

                isSubmittingReconfiguring: false,
                reconfigureData: {
                    ejectMedias: false,
                },

                isSubmittingAdvanceSetting: false,

                isUpdatingBasic: false,
                isSubmittingChangeHostname: false,
                isSubmittingResetOSPassword: false,
                isSubmittingChangePublicImage: false,
                isSubmittingReconfiguringOSNetwork: false,
                isChangingPackage: false,

                availablePublicImages: [],
                packageCategories: [],

                selectedImageCategoryIndex: null,
                selectedImageId: null,

                selectedPackage: null,
            };
        },
        created: function () {
            axios.get(route(this.operationRoutePrefix + "computeInstance.settingAvailableData", [this.instance.id]))
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.availablePublicImages = data.imageCategories;
                        this.packageCategories = data.packageCategories;
                    }
                })
            ;
        },
        mounted: function () {
            $(".ui.dropdown").dropdown();
            $(".ui.checkbox").checkbox();
            if (this.$route.hash.length)
                location.href = this.$route.hash;
        },
        methods: {
            selectedImageCategoryChanged: function (event) {
                this.selectedImageId = null;
                this.selectedImageCategoryIndex = event.target.value;
            },
            selectedImageChanged: function (event) {
                debugger;
                this.selectedImageId = event.target.value;
            },
            updateBasic: function () {
                this.isUpdatingBasic = true;
                axios.patch(route(this.operationRoutePrefix + "computeInstances.updateBasic", [this.instance.id]), this.temporaryInstanceSetting.basic, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$emit("instanceUpdated", (instance) => {
                                instance.name = this.temporaryInstanceSetting.basic.name;
                                instance.description = this.temporaryInstanceSetting.basic.description;
                                this.positiveFloatingMessage("保存成功");
                            });
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isUpdatingBasic = false;
                    })
                ;
            },
            changeHostname: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定更改主机名吗？")
                    .withOnApprove(() => {
                        this.isSubmittingChangeHostname = true;
                        axios.patch(route(this.operationRoutePrefix + "computeInstances.changeHostname", [this.instance.id]), {hostname: this.temporaryInstanceSetting.hostname}, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.$emit("operationRequestCreated", data.operationRequest);
                                    this.positiveFloatingMessage("主机名更改请求已提交");
                                } else {
                                    this.$globalErrnoHandler(data);
                                }
                            })
                            .catch(this.$axiosCatchError2Console)
                            .then(() => {
                                this.isSubmittingChangeHostname = false;
                            })
                        ;
                    })
                    .withModalClass("mini")
                    .show()
                ;
            },
            resetOSPassword: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定重置系统密码吗？")
                    .withOnApprove(() => {
                        this.isSubmittingResetOSPassword = true;
                        axios.patch(route(this.operationRoutePrefix + "computeInstances.resetOSPassword", [this.instance.id]), null, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.$emit("operationRequestCreated", data.operationRequest);
                                    this.positiveFloatingMessage("重置密码请求已提交");
                                } else {
                                    this.$globalErrnoHandler(data);
                                }
                            })
                            .catch(this.$axiosCatchError2Console)
                            .then(() => {
                                this.isSubmittingResetOSPassword = false;
                            })
                        ;
                    })
                    .withModalClass("mini")
                    .show()
                ;
            },
            reconfigureOSNetwork: function () {
                this.isSubmittingReconfiguringOSNetwork = true;
                this.$axiosPost(route(this.operationRoutePrefix + "computeInstances.reconfigureOSNetwork", [this.instance.id]), null, (data) => {
                    this.$emit("operationRequestCreated", data.operationRequest);
                    this.positiveFloatingMessage("操作系统网络重置请求已提交");
                }, () => {
                    this.isSubmittingReconfiguringOSNetwork = false;
                });
            },
            changePackage: function () {
                if (this.selectedPackage === null) {
                    this.negativeFloatingMessage("请选择实例规格");
                    return;
                }
                this.isChangingPackage = true;
                this.$axiosPatch(route(this.operationRoutePrefix + "computeInstances.changePackage", [this.instance.id, this.selectedPackage.id]), null, (data) => {
                    this.$emit("operationRequestCreated", data.operationRequest);
                    this.positiveFloatingMessage("实例规格更换成功");
                }, () => {
                    this.isChangingPackage = false;
                });
            },
            changePublicImage: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定更换公共镜像？")
                    .withOnApprove(() => {
                        this.isSubmittingChangePublicImage = true;
                        axios.patch(route(this.operationRoutePrefix + "computeInstances.operation.changePublicImage", [this.instance.id]), {publicImageId: this.selectedImageId}, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.$emit("operationRequestCreated", data.operationRequest);
                                    this.positiveFloatingMessage("操作请求已提交");
                                } else {
                                    this.$globalErrnoHandler(data);
                                }
                            })
                            .catch(this.$axiosCatchError2Console)
                            .then(() => {
                                this.isSubmittingChangePublicImage = false;
                            })
                        ;
                    })
                    .withModalClass("mini")
                    .show()
                ;
            },
            destroyInstance: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定销毁实例[" + this.instance.unique_id + "]？<b>请务必确认重要数据已转移完毕。</b>")
                    .withOnApprove(() => {
                        axios.post(route(this.operationRoutePrefix + "computeInstances.operation.destroy", [this.instance.id]), this.temporaryInstanceSetting.destroy, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.$emit("operationRequestCreated", data.operationRequest);
                                } else {
                                    this.$globalErrnoHandler(data);
                                }
                            })
                            .catch((error) => {
                                console.log(error);
                            })
                            .then(() => {
                            })
                        ;
                    })
                    .withModalClass("mini")
                    .show()
                ;
            },
            reconfigure: function () {
                this.isSubmittingReconfiguring = true;
                axios.post(route(this.operationRoutePrefix + "computeInstances.operation.reconfigure", [this.instance.id]), this.reconfigureData, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$emit("operationRequestCreated", data.operationRequest);
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.isSubmittingReconfiguring = false;
                    })
                ;
            },
            saveAdvanceSetting: function () {
                this.isSubmittingReconfiguring = true;
                axios.patch(route(this.operationRoutePrefix + "computeInstances.operation.saveAdvanceSettings", [this.instance.id]), this.temporaryInstanceSetting.advance, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            if (data.hasOwnProperty("operationRequest")) {
                                this.$emit("operationRequestCreated", data.operationRequest);
                                this.positiveFloatingMessage(this.$t("common.operationRequestSubmitted"));
                            } else {
                                this.$emit("instanceUpdated", (instance) => {
                                    instance.machine_type = data.machineType;
                                    instance.use_legacy_bios = data.useLegacyBIOS;
                                });
                                this.positiveFloatingMessage(this.$t("common.saveSuccessfully"));
                            }
                        } else {
                            this.$globalErrnoHandler(data, () => {
                                this.negativeFloatingMessage(data.message);
                            });
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.isSubmittingReconfiguring = false;
                    })

                ;
            }
        },
        computed: {
            isDestroying: function () {
                return this.isOperationRequestTypeExists(6);
            },
            isReconfiguring: function () {
                return this.isSubmittingReconfiguring || this.isOperationRequestTypeExists(10);
            },
            isChangingHostname: function () {
                return this.isSubmittingChangeHostname || this.isOperationRequestTypeExists(12);
            },
            isResettingOSPassword: function () {
                return this.isSubmittingResetOSPassword || this.isOperationRequestTypeExists(13);
            },
            isChangingPublicImage: function () {
                return this.isSubmittingChangePublicImage || this.isOperationRequestTypeExists(15);
            },
            isReconfiguringOSNetwork: function () {
                return this.isSubmittingReconfiguringOSNetwork || this.isOperationRequestTypeExists(16);
            }
        },
    }
</script>

<style scoped>

</style>