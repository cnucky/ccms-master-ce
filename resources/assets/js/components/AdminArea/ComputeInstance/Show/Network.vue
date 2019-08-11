<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">公网虚拟网卡</h3>
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <table class="ui unstackable fixed table">
                <thead>
                <tr>
                    <th class="one wide column"></th>
                    <th>可用IP范围</th>
                    <th class="two wide column">掩码</th>
                    <th class="three wide column">网关</th>
                    <th class="five wide column"></th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(publicNetworkInterface, publicNetworkInterfaceIndex) in publicNetworkInterfaces">
                    <tr>
                        <td></td>
                        <td colspan="3">
                            <i class="microchip icon"></i> <network-interface-model :network-interface="publicNetworkInterface"></network-interface-model> <b>网卡MAC：</b>{{ publicNetworkInterface.mac_address }}
                            <span style="color: gray; font-size: small; margin-left: 5px;">[{{ publicNetworkInterface.description }}]</span>
                        </td>
                        <td>
                            <button class="ui green tiny button" v-on:click="allocate(4)" :disabled="isAllocatingIP">申请IPv4</button>
                            <button class="ui green tiny button" v-on:click="allocate(6)" :disabled="isAllocatingIP">申请IPv6</button>
                            <button class="ui blue tiny button" v-on:click="showSettingModal(publicNetworkInterface)">设置</button>
                        </td>
                    </tr>
                    <template v-for="(ipv4Address, ipv4AddressIndex) in publicNetworkInterface.ipv4_addresses">
                        <i-p-address-table-row :is-admin="true" :interface-index="publicNetworkInterfaceIndex" :ip-address-index="ipv4AddressIndex" :ip-address="ipv4Address" v-on:unbind="unbindIPv4" v-on:release="releaseIPv4" v-on:convert="convertV4" :is-v4="true"></i-p-address-table-row>
                    </template>
                    <template v-for="(ipv6Address, ipv6AddressIndex) in publicNetworkInterface.ipv6_addresses">
                        <i-p-address-table-row :is-admin="true" :interface-index="publicNetworkInterfaceIndex" :ip-address-index="ipv6AddressIndex" :ip-address="ipv6Address" v-on:unbind="unbindIPv6" v-on:release="releaseIPv6" v-on:convert="convertV6"></i-p-address-table-row>
                    </template>
                </template>
                </tbody>
            </table>

            <h3 class="ui header">内网虚拟网卡</h3>
            <table class="ui unstackable fixed table">
                <thead>
                <tr>
                    <th class="one wide column"></th>
                    <th>可用IP范围</th>
                    <th class="two wide column">掩码</th>
                    <th class="three wide column">网关</th>
                    <th class="four wide column"></th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(networkInterface, interfaceIndex) in privateNetworkInterfaces">
                    <tr>
                        <td></td>
                        <td colspan="3">
                            <i class="microchip icon"></i> <network-interface-model :network-interface="networkInterface"></network-interface-model> <b>网卡MAC：</b>{{ networkInterface.mac_address }}
                            <span style="color: gray; font-size: small; margin-left: 5px;">[{{ networkInterface.description }}]</span>
                        </td>
                        <td>
                            <button class="ui blue tiny button" v-on:click="showSettingModal(networkInterface)">设置</button>
                        </td>
                    </tr>
                    <template v-for="(ipv4Address, ipv4AddressIndex) in networkInterface.ipv4_addresses">
                        <i-p-address-table-row :is-private="true" :is-admin="true" :interface-index="interfaceIndex" :ip-address-index="ipv4AddressIndex" :ip-address="ipv4Address" v-on:unbind="unbindIPv4" v-on:release="releaseIPv4" :is-v4="true"></i-p-address-table-row>
                    </template>
                    <template v-for="(ipv6Address, ipv6AddressIndex) in networkInterface.ipv6_addresses">
                        <i-p-address-table-row :is-private="true" :is-admin="true" :interface-index="interfaceIndex" :ip-address-index="ipv6AddressIndex" :ip-address="ipv6Address" v-on:unbind="unbindIPv6" v-on:release="releaseIPv6"></i-p-address-table-row>
                    </template>
                </template>
                </tbody>
            </table>
        </div>

        <form-modal ref="networkInterfaceSettingModel" class="small" :noScroll="true" :is-loading="isChangingNetworkInterfaceSetting" :no-stay-select="true" custom-header="虚拟网卡设置" v-on:submit="changeModel">
            <form class="ui form" v-on:submit.prevent="changeModel">
                <button type="submit" class="hidden">提交</button>
                <div class="ui field">
                    <label>虚拟网卡型号</label>
                    <select ref="networkInterfaceModelSelect" class="ui dropdown" v-model="modelChange2">
                        <option value="0">virtio</option>
                        <option value="1">e1000</option>
                        <option value="2">rtl8139</option>
                    </select>
                </div>
                <div class="ui field">
                    <div class="ui checkbox">
                        <input id="save-and-apply" type="checkbox" v-model="saveAndApply">
                        <label for="save-and-apply">保存并应用</label>
                    </div>
                </div>
            </form>
        </form-modal>
    </div>
</template>

<script>
    import NetworkInterfaceModel from "./../../../ClientArea/ComputeInstance/Show/NetworkInterfaceModel";
    import IPAddressTableRow from "./IPAddressTableRow";
    export default {
        name: "Network",
        components: {IPAddressTableRow, NetworkInterfaceModel},
        props: ["instance", "operationRoutePrefix"],
        data: function () {
            return {
                isLoading: false,
                networkInterfaces: [],

                networkInterface2Change: null,
                isChangingNetworkInterfaceSetting: false,
                modelChange2: null,
                saveAndApply: true,

                isAllocatingIP: false,
            };
        },
        created: function () {
            this.filter();
        },
        mounted: function () {
            $(this.$refs.networkInterfaceModelSelect).dropdown({
                showOnFocus: false,
            });
        },
        methods: {
            filter: function (noChangeLoadingStatus = false) {
                if (!noChangeLoadingStatus)
                    this.isLoading = true;
                axios.get(route(this.operationRoutePrefix + "computeInstances.networkInterfaces", [this.instance.id]), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.networkInterfaces = data.networkInterfaces;
                        } else {
                            this.negativeFloatingMessage(data.message);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        if (!noChangeLoadingStatus)
                            this.isLoading = false;
                    })
                ;
            },
            allocate: function (version) {
                var routeName = "publicIPv4Addresses.allocate";
                if (version === 6) {
                    routeName = "publicIPv6Addresses.allocate";
                }

                this.isAllocatingIP = true;
                axios.post(route(this.operationRoutePrefix + routeName, [this.instance.id]))
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("IP申请成功");
                            this.filter();
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$globalErrnoHandler)
                    .then(() => {
                        this.isAllocatingIP = false;
                    })
                ;
            },
            unbindIPv4: function (id, n, i) {
                this.unbind("publicIPv4Addresses", id, n, i)
            },
            unbindIPv6: function (id, n, i) {
                this.unbind("publicIPv6Addresses", id, n, i)
            },
            unbind: function (ipType, id, n, i) {
                this.ipOperation(ipType, "unbind", id, n, i);
            },
            releaseIPv4: function (id, n, i) {
                this.release("publicIPv4Addresses", id, n, i)
            },
            releaseIPv6: function (id, n, i) {
                this.release("publicIPv6Addresses", id, n, i)
            },
            release: function (ipType, id, n, i) {
                this.ipOperation(ipType, "release", id, n, i);
            },
            ipOperation: function (ipType, operation, id, n, i) {
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定" + (operation === "unbind" ? "解绑" : "释放") + "指定IP？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.post(route(this.operationRoutePrefix + ipType + "." + operation, [id]), null, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.positiveFloatingMessage("成功");

                                    var deleteType = "ipv4_addresses";
                                    if (ipType === "publicIPv6Addresses")
                                        deleteType = "ipv6_addresses";

                                    this.$delete(this.networkInterfaces[n][deleteType], i);
                                } else {
                                    this.negativeFloatingMessage("失败");
                                }
                            })
                            .catch((error) => {
                                console.log(error);
                            })
                            .then(() => {
                                this.isLoading = false;
                            })
                        ;
                    })
                    .show()
                ;
            },
            convertV4: function (id, n, i) {
                this.convert(4, id, n, i);
            },
            convertV6: function (id, n, i) {
                this.convert(6, id, n, i);
            },
            convert: function (version, id, n, i) {
                this.isLoading = true;
                axios.post(route("admin.publicIPv"+ version +"Addresses.convert", [id]), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("转换成功");

                            var ipType = "ipv4_addresses";
                            if (version === 6)
                                ipType = "ipv6_addresses";

                            this.$set(this.networkInterfaces[n][ipType][i], "unbindable", data.unbindable);
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            },
            showSettingModal: function (networkInterface) {
                this.networkInterface2Change = networkInterface;
                this.modelChange2 = networkInterface.model.toString();
                $(this.$refs.networkInterfaceModelSelect).dropdown("set selected", networkInterface.model.toString());
                this.$refs.networkInterfaceSettingModel.show();
            },
            changeModel: function () {
                this.isChangingNetworkInterfaceSetting = true;
                axios.patch(route(this.operationRoutePrefix + "computeInstances.networkInterfaces.changeModel", [this.networkInterface2Change.id]), {model: this.modelChange2, saveAndApply: this.saveAndApply}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            if (this.saveAndApply) {
                                this.positiveFloatingMessage("请求已提交");
                                this.$emit("operationRequestCreated", data.operationRequest);
                            } else {
                                this.positiveFloatingMessage("保存成功");
                                this.networkInterface2Change.model = this.modelChange2;
                            }
                            this.$refs.networkInterfaceSettingModel.hide();
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.isChangingNetworkInterfaceSetting = false;
                    })
                ;
            }
        },
        computed: {
            publicNetworkInterfaces: function () {
                var interfaces = [];
                for (var i in this.networkInterfaces) {
                    if (this.networkInterfaces[i].type === 0)
                        interfaces.push(this.networkInterfaces[i]);
                }
                return interfaces;
            },
            privateNetworkInterfaces: function () {
                var interfaces = [];
                for (var i in this.networkInterfaces) {
                    if (this.networkInterfaces[i].type === 1)
                        interfaces.push(this.networkInterfaces[i]);
                }
                return interfaces;
            }
        },
    }
</script>

<style scoped>

</style>