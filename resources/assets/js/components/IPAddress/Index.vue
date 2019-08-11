<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">
                公网{{ modelName }}列表
            </h3>
        </div>

        <div class="ten wide column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

            <button class="ui teal tiny button" v-on:click="() => {$refs.allocateIPAddressModal.show();}"><i class="plus icon"></i> 申请</button>

            <button class="ui red inverted tiny button" :disabled="selectedItemCount() <= 0" v-on:click="massUnbind"><i class="unlink icon"></i> 解绑</button>

            <button class="ui red inverted tiny button" :disabled="selectedItemCount() <= 0" v-on:click="massRelease"><i class="trash icon"></i> 释放</button>

            <index-table-search-input v-model="filterKey" v-bind:is-loading="isLoading" v-on:search="filter"></index-table-search-input>
        </div>

        <div class="six wide middle right aligned column">
            <div style="display: inline;">
                <model-index-page-number-input v-model="page" v-bind:paginator="paginator"
                                               v-on:page-change="filter"></model-index-page-number-input>
            </div>

            <div style="display: inline; margin-left: 10px;">
                <model-index-pre-page-item-selector v-model="prePageItem"
                                                    v-on:pre-page-item-change="filter"
                                                    v-bind:is-loading="isLoading"></model-index-pre-page-item-selector>
            </div>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <sortable-table
                    id="attached-volume-table"
                    class="sortable unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="true"
                    :columns="columns"
                    :operable="true"
                    operation-component="span"
                    :is-loading="isLoading"
                    :use-slot-row="true"
                    :sort-disabled="{
                        netmask: true,
                        gateway: true,
                    }"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:table-created="tableCreated"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
                    v-on:edit-item="editItem"
                    v-on:table-updated="tableUpdated"
            >
                <tr v-for="(ipAddress, ipAddressIndex) in items">
                    <td>
                        <div class="ui fitted child checkbox" v-if="ipAddress.unbindable">
                            <input type="checkbox" v-model="$refs.listTable.isItemSelected[ipAddress.id]">
                            <label></label>
                        </div>
                    </td>
                    <td>
                        {{ ipAddress.human_readable_first_usable }}
                        <template v-if="ipAddress.human_readable_first_usable !== ipAddress.human_readable_last_usable">
                            <b style="color: red;"> - </b>{{ ipAddress.human_readable_last_usable }}
                        </template>
                    </td>

                    <td v-if="isV4">
                        {{ cidr2Netmask[ipAddress.pool.network_bits] }} ({{ ipAddress.pool.network_bits }})
                    </td>
                    <td v-else>
                        {{ ipAddress.pool.network_bits }}
                    </td>

                    <td v-if="ipAddress.pool.human_readable_gateway">
                        {{ ipAddress.pool.human_readable_gateway }}
                    </td>
                    <td v-else>
                        -
                    </td>

                    <td>
                        <template v-if="ipAddress.network_interface && ipAddress.network_interface.hasOwnProperty('instance') && ipAddress.network_interface.instance.hasOwnProperty('name')">
                            <router-link :to="{name: 'computeInstances.dashboard', params: {id: ipAddress.network_interface.instance.id}}" style="display: inline-block;">
                                <div class="instance-name">{{ ipAddress.network_interface.instance.name }}</div>
                                <div class="instance-size">
                                    {{ ipAddress.network_interface.instance.unique_id }} - {{ ipAddress.network_interface.instance.client_instance_size.vCPU }} vCPU/ {{ipAddress.network_interface.instance.client_instance_size.memory }} MiB
                                </div>
                            </router-link>
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td>
                        <template v-if="ipAddress.unbindable"><amount :amount="ipAddress.pool.price_per_hour"></amount>/hour</template>
                        <template v-else>-</template>
                    </td>
                    <duration-column :entry="ipAddress" key-name="assigned_at"></duration-column>
                    <td class="three wide column">
                        <template v-if="ipAddress.unbindable">
                            <button v-if="ipAddress.nic_id" class="ui tiny button" v-on:click="unbind(ipAddress, ipAddressIndex)">解绑</button>
                            <button v-else class="ui tiny green button" v-on:click="bind(ipAddress, ipAddressIndex)">绑定</button>
                            <button class="ui tiny red button" v-on:click="release(ipAddress, ipAddressIndex)">释放</button>
                        </template>
                    </td>
                </tr>
            </sortable-table>
        </div>

        <form-modal ref="allocateIPAddressModal" class="small" custom-header="申请IP" :no-scroll="true" :no-stay-select="true" submit-button-text="提交" v-on:submit="() => { $refs.allocateIPAddressForm.allocate() }" :is-loading="modalLoading">
            <allocate-i-p-address-form ref="allocateIPAddressForm" :model-route-name="modelRouteName" v-on:beforeSubmit="modalLoading = true" v-on:complete="modalLoading = false" v-on:success="() => {filter(); $refs.allocateIPAddressModal.hide();}"></allocate-i-p-address-form>
        </form-modal>

        <form-modal ref="bindIPAddressModal" class="small" :custom-header="(ipAssignment2Change ? ipAssignment2Change.human_readable_first_usable : '') + ' - 绑定'" :no-scroll="true" :no-stay-select="true" submit-button-text="提交" v-on:submit="() => { $refs.bindIPAddressForm.bind() }" :is-loading="modalLoading">
            <bind-i-p-address-form ref="bindIPAddressForm" :ip-assignment="ipAssignment2Change" :model-route-name="modelRouteName" v-on:beforeSubmit="modalLoading = true" v-on:complete="modalLoading = false" v-on:success="() => {filter(); $refs.bindIPAddressModal.hide();}"></bind-i-p-address-form>
        </form-modal>
    </div>
</template>

<script>
    import indexOperation from "./../ModelIndex/IndexOperation";
    import pageOperation from "./../ModelIndex/PageOperation";
    import CIDR2Netmask from "./CIDR2Netmask";
    import BindIPAddressForm from "./BindIPAddressForm";
    import AllocateIPAddressForm from "./AllocateIPAddressForm";
    import Amount from "../CreditRecord/Amount";

    export default {
        name: "Index",
        components: {Amount, AllocateIPAddressForm, BindIPAddressForm},
        mixins: [indexOperation, pageOperation],
        props: ["modelName", "modelRouteName", "isV4"],
        data: function () {
            return {
                modalLoading: false,

                cidr2Netmask: CIDR2Netmask.cidr2Netmask,

                columns: {
                    human_readable_first_usable: "可用范围",
                    netmask: "掩码",
                    gateway: "网关",
                    nic_id: "已绑定实例",
                    price_per_hour: "价格",
                    assigned_at: "申请于",
                },

                indexRouteName: this.modelRouteName + ".index",
                massDestroyRouteName: this.modelRouteName + ".massRelease",

                isMassDetaching: false,
                isMassReleasing: false,

                ipAssignment2Change: null,
            };
        },
        mounted: function () {
            this.$nextTick(() => {
                this.filter();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                this.paginator = data.ipAddresses;
                this.items = data.ipAddresses.data;
            },
            tableCreated: function () {
            },
            editItem: function () {
            },
            bind: function (ipAddress, index) {
                this.ipAssignment2Change = ipAddress;
                this.$refs.bindIPAddressModal.show();
            },
            unbind: function (ipAddress, index) {
                this.ipOperation("unbind", ipAddress.id);
            },
            release: function (ipAddress, index) {
                this.ipOperation("release", ipAddress.id);
            },
            massUnbind: function () {
                var ipType = this.modelRouteName;
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定解绑指定IP？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.post(route(ipType + ".massUnbind"), {items: this.getSelectedItems()}, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    if (data.count) {
                                        this.positiveFloatingMessage("成功解绑" + data.count + "条IP");
                                        this.filter();
                                    } else {
                                        this.negativeFloatingMessage("没有可解绑的IP");
                                    }
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
            massRelease: function () {
                var ipType = this.modelRouteName;
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定释放指定IP？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.post(route(ipType + ".massRelease"), {items: this.getSelectedItems()}, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;

                                if (data.count) {
                                    this.positiveFloatingMessage("成功释放" + data.count + "条IP");
                                    this.filter();
                                } else {
                                    this.negativeFloatingMessage("没有可释放的IP");
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
            ipOperation: function (operation, id) {
                var ipType = this.modelRouteName;
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定" + (operation === "unbind" ? "解绑" : "释放") + "指定IP？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.post(route(ipType + "." + operation, [id]), null, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.positiveFloatingMessage("成功");
                                    this.filter();
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
        }
    }
</script>

<style scoped>
    .instance-name {
        font-size: 1.1em;
        line-height: unset;
        font-weight: 600;
        color: #0069ff;
        margin-bottom: 2px;

    }

    .instance-size {
        color: #676767;
        line-height: .8rem;
        font-size: .8rem;
        clear: left;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding-bottom: 1px;
    }
</style>