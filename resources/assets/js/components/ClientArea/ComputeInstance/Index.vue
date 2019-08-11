<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1>我的{{ $t('common.computeInstance') }}</h1>
        </div>

        <div class="nine wide column">
            <model-index-refresh-button v-bind:is-loading="isLoading" v-on:click="filter"></model-index-refresh-button>

            <router-link class="ui teal tiny button" :to="{ name: 'computeInstances.create' }"><i class="plus icon"></i> 创建</router-link>

            <button class="ui green inverted tiny button" :disabled="emptySelectedItem() || isMassOperationSubmitting" v-on:click="massPowerOn"><i class="play icon"></i> 启动</button>

            <semantic-ui-dropdown-menu v-bind:class="{disabled: emptySelectedItem() || isMassOperationSubmitting}" text="更多">
                <div class="item" :disabled="emptySelectedItem() || isMassOperationSubmitting" v-on:click="massPowerReset"><i class="redo icon"></i> 重启</div>

                <div class="item" v-on:click="massPowerOff"><i class="stop icon"></i> 关机</div>

                <div class="item" style="color: red;" v-on:click="showMassDestroyConfirmModal"><i class="trash icon"></i> 销毁</div>
            </semantic-ui-dropdown-menu>

            <index-table-search-input v-model="filterKey" v-bind:is-loading="isLoading" v-on:search="filter"></index-table-search-input>
        </div>

        <div class="seven wide middle right aligned column">
            <div style="display: inline;">
                <model-index-page-number-input v-model="page" v-bind:paginator="paginator"
                                               v-on:page-change="filter"></model-index-page-number-input>
            </div>

            <div style="display: inline; margin-left: 10px;">
                <model-index-pre-page-item-selector v-model="prePageItem"
                                                    v-on:pre-page-item-change="filter"
                                                    v-bind:is-loading="isLoading"></model-index-pre-page-item-selector>
            </div>

            <div ref="columnSelect" style="display: inline; margin-left: 10px;" class="ui pointing dropdown tiny button">
                <span class="text">选择列</span>
                <i class="dropdown icon"></i>
                <div class="menu">
                    <div v-for="(columnText, columnKey) in columns" class="item" v-on:click="$set(selectedColumns, columnKey, !selectedColumns[columnKey])"><i v-if="selectedColumns[columnKey]" class="check icon"></i> {{ columnText }}</div>
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <sortable-table
                    id="region-table"
                    class="sortable unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="true"
                    :columns="column2Show"
                    :operable="true"
                    operation-component="span"
                    :is-loading="isLoading"
                    :use-slot-row="true"
                    :sort-disabled="{
                        ip: true,
                        area: true,
                        price_per_hour: true,
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
                <tr v-for="(instance, index) in items">
                    <td>
                        <span class="ui active small inline indeterminate loader" v-if="instance.processing_operation_requests.length"></span>
                        <div class="ui fitted child checkbox" v-else>
                            <input type="checkbox" v-model="$refs.listTable.isItemSelected[instance.id]">
                            <label></label>
                        </div>
                    </td>

                    <td v-if="selectedColumns.name" class="td-flex">
                        <power-status-icon-label :power-status="instance.power_status" style="display: inline-block;"></power-status-icon-label>
                        <router-link :to="{name: 'computeInstances.dashboard', params: {id: instance.id}}" style="display: inline-block;">
                            <div class="instance-name">{{ instance.name }}</div>
                            <div class="instance-size">
                                {{ instance.unique_id }} - {{ instance.client_instance_size.vCPU }} vCPU/ {{instance.client_instance_size.memory }} MiB
                            </div>
                        </router-link>

                    </td>

                    <td v-if="selectedColumns.ip">
                        <template v-if="instance.network_interfaces && instance.network_interfaces.length">
                            <div class="ip link-font" v-if="instance.network_interfaces[0].ipv4_addresses && instance.network_interfaces[0].ipv4_addresses.length" v-on:click="$copyText(instance.network_interfaces[0].ipv4_addresses[0].human_readable_first_usable)" data-content="点击复制">{{ instance.network_interfaces[0].ipv4_addresses[0].human_readable_first_usable }}</div>
                            <div class="ip link-font" v-if="instance.network_interfaces[0].ipv6_addresses && instance.network_interfaces[0].ipv6_addresses.length" v-on:click="$copyText(instance.network_interfaces[0].ipv6_addresses[0].human_readable_first_usable)"  data-content="点击复制">{{ instance.network_interfaces[0].ipv6_addresses[0].human_readable_first_usable }}</div>
                        </template>
                    </td>

                    <td v-if="selectedColumns.area">
                        <template v-if="instance.node">
                            <i :class="instance.node.zone.region.icon_class"></i> {{ instance.node.zone.region.name }} - {{ instance.node.zone.name }}
                        </template>
                        <template v-else>-</template>
                    </td>

                    <td v-if="selectedColumns.price_per_hour">
                        <amount :amount="instance.client_instance_size.price_per_hour"></amount>/hour
                    </td>

                    <td v-if="selectedColumns.created_at" class="popup-time" :data-content="$toLocalTime(instance.created_at)">
                        {{ $momentFrom(instance.created_at) }}
                    </td>

                    <td v-if="selectedColumns.status">
                        <status-label :status-code="instance.status"></status-label>
                    </td>

                    <td v-if="selectedColumns.twenty_minutes_average_cpu_utilization">
                        {{ instance.twenty_minutes_average_cpu_utilization.toFixed(2) }}%
                    </td>

                    <td v-if="selectedColumns.twenty_minutes_average_public_network_rx_bytes_per_second">
                        {{ $convertResult2Text($convert2HumanReadableUnit(instance.twenty_minutes_average_public_network_rx_bytes_per_second), "/s") }}
                    </td>

                    <td v-if="selectedColumns.twenty_minutes_average_public_network_tx_bytes_per_second">
                        {{ $convertResult2Text($convert2HumanReadableUnit(instance.twenty_minutes_average_public_network_tx_bytes_per_second), "/s") }}
                    </td>

                    <td v-if="selectedColumns.twenty_minutes_average_private_network_rx_bytes_per_second">
                        {{ $convertResult2Text($convert2HumanReadableUnit(instance.twenty_minutes_average_private_network_rx_bytes_per_second), "/s") }}
                    </td>

                    <td v-if="selectedColumns.twenty_minutes_average_private_network_tx_bytes_per_second">
                        {{ $convertResult2Text($convert2HumanReadableUnit(instance.twenty_minutes_average_private_network_tx_bytes_per_second), "/s") }}
                    </td>

                    <td v-if="selectedColumns.twenty_minutes_average_disk_read_bytes_per_second">
                        {{ $convertResult2Text($convert2HumanReadableUnit(instance.twenty_minutes_average_disk_read_bytes_per_second), "/s") }}
                    </td>

                    <td v-if="selectedColumns.twenty_minutes_average_disk_write_bytes_per_second">
                        {{ $convertResult2Text($convert2HumanReadableUnit(instance.twenty_minutes_average_disk_write_bytes_per_second), "/s") }}
                    </td>

                    <td v-if="selectedColumns.twenty_minutes_average_disk_read_req_per_second">
                        {{ instance.twenty_minutes_average_disk_read_req_per_second.toFixed(2) }}/s
                    </td>

                    <td v-if="selectedColumns.twenty_minutes_average_disk_write_req_per_second">
                        {{ instance.twenty_minutes_average_disk_write_req_per_second.toFixed(2) }}/s
                    </td>

                    <instance-operation :entry="instance"></instance-operation>
                </tr>
            </sortable-table>
        </div>

        <div ref="destroyInstanceConfirmModal" class="ui small modal">
            <i class="close icon"></i>
            <div class="header">
                销毁确认
            </div>
            <div class="content">
                <div class="ui negative message">
                    警告：销毁实例是不可逆的操作，被销毁的数据将无法恢复，被释放的IP地址无法保证再次可申请！
                </div>

                <div class="ui form">
                    <div class="ui field">
                        <div class="ui checkbox">
                            <input id="deleteAttachedVolumes" type="checkbox" v-model="massDestroyOptions.deleteAttachedVolumes">
                            <label for="deleteAttachedVolumes">同时释放已连接到被销毁实例的卷</label>
                        </div>
                    </div>

                    <div class="ui field">
                        <div class="ui checkbox">
                            <input id="releaseExtraIPs" type="checkbox" v-model="massDestroyOptions.releaseExtraIPs">
                            <label for="releaseExtraIPs">同时释放已绑定到被销毁实例的弹性IP地址</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="actions">
                <div class="ui small green cancel button">
                    取消
                </div>
                <div class="ui small red right labeled icon button" :disable="isMassOperationSubmitting" v-bind:class="{loading: isMassOperationSubmitting}" v-on:click="massDestroy">
                    确认
                    <i class="exclamation icon"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Convert2HumanReadableUnit from "./../../Convert2HumanReadableUnit";
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";
    import StatusLabel from "./StatusLabel";
    import InstanceOperation from "./Operation";
    import PowerStatusIconLabel from "../../ComputeInstance/PowerStatusIconLabel";
    import PowerAction from "./PowerAction";
    import FinishedOperationRequestHandler from "./../../ComputeInstance/FinishedOperationRequestHandler";
    import Amount from "../../CreditRecord/Amount";

    export default {
        name: "Index",
        components: {Amount, PowerStatusIconLabel, InstanceOperation, StatusLabel},
        mixins: [Convert2HumanReadableUnit, indexOperation, pageOperation, PowerAction, FinishedOperationRequestHandler],
        data: function () {
            return {
                columns: {
                    name: "名称",
                    ip: "IP地址",
                    area: "地区",
                    price_per_hour: "价格",
                    created_at: "创建于",
                    status: "状态",
                    twenty_minutes_average_cpu_utilization: "近平均CPU使用率",
                    twenty_minutes_average_public_network_rx_bytes_per_second: "近平均公网RX速率",
                    twenty_minutes_average_public_network_tx_bytes_per_second: "近平均公网TX速率",
                    twenty_minutes_average_private_network_rx_bytes_per_second: "近平均内网RX速率",
                    twenty_minutes_average_private_network_tx_bytes_per_second: "近平均公网TX速率",
                    twenty_minutes_average_disk_read_bytes_per_second: "近平均硬盘读速",
                    twenty_minutes_average_disk_write_bytes_per_second: "近平均硬盘写速",
                    twenty_minutes_average_disk_read_req_per_second: "近平均硬盘读操作/秒",
                    twenty_minutes_average_disk_write_req_per_second: "近平均硬盘写操作/秒",
                },

                selectedColumns: {
                    name: true,
                    user: true,
                    ip: true,
                    area: true,
                    price_per_hour: true,
                    created_at: true,
                    status: true,
                },

                indexRouteName: "computeInstances.index",
                massDestroyRouteName: "computeInstances.massDestroy",

                isDestroyed: false,

                isMassOperationSubmitting: false,

                massDestroyOptions: {
                    deleteAttachedVolumes: false,
                    releaseExtraIPs: false,
                }
            };
        },
        mounted: function () {
            $(".ui.checkbox").checkbox();
            $(this.$refs.columnSelect).dropdown({
                action: "nothing",
            });
            this.$nextTick(() => {
                this.filter();
                this.startTimeoutHandler();
            });
        },
        destroyed: function () {
            this.isDestroyed = true;
        },
        updated: function () {
            $('.ip.link-font')
                .popup({
                    distanceAway: 40,
                })
            ;
            $('.popup-time')
                .popup({
                    distanceAway: 25,
                })
            ;
        },
        methods: {
            loadSuccessCallback: function (data) {
                // console.log(data);
                this.items = data.instances.data;
                this.paginator = data.instances;
            },
            tableCreated: function () {
            },
            editItem: function () {
            },
            beforeMassOperationSend: function () {
                this.isMassOperationSubmitting = true;
            },
            massOperationSubmitted: function () {
                this.isMassOperationSubmitting = false;
            },
            massOperationSubmittedSuccessfully: function () {
                this.filter();
            },
            massPowerOn: function () {
                this.massPowerOnAction(this.getSelectedItems(), this.beforeMassOperationSend, this.massOperationSubmitted, this.massOperationSubmittedSuccessfully);
            },
            massPowerReset: function () {
                this.massPowerResetAction(this.getSelectedItems(), this.beforeMassOperationSend, this.massOperationSubmitted, this.massOperationSubmittedSuccessfully);
            },
            massPowerOff: function () {
                this.massPowerOffAction(this.getSelectedItems(), this.beforeMassOperationSend, this.massOperationSubmitted, this.massOperationSubmittedSuccessfully);
            },
            showMassDestroyConfirmModal: function () {
                $(this.$refs.destroyInstanceConfirmModal).modal("show");
            },
            hideMassDestroyConfirmModal: function () {
                $(this.$refs.destroyInstanceConfirmModal).modal("hide");
            },
            massDestroy: function () {
                this.beforeMassOperationSend();
                axios.post(route("computeInstances.operation.massDestroy"), {
                    deleteAttachedVolumes: this.massDestroyOptions.deleteAttachedVolumes,
                    releaseExtraIPs: this.massDestroyOptions.releaseExtraIPs,
                    items: this.getSelectedItems()
                }, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.filter();
                            this.hideMassDestroyConfirmModal();
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(this.massOperationSubmitted)
                ;
            },
            startTimeoutHandler: function () {
                setTimeout(this.timeoutHandler, 2000);
            },
            timeoutHandler: function () {
                if (this.isDestroyed)
                    return;

                var operationRequestList = this.operationRequestList;
                if (operationRequestList.length) {
                    axios.post(route('computeInstanceOperationRequests.query'), {operationRequestList: operationRequestList}, {vueInstance: this})
                        .then((response) => {
                            var data = response.data;
                            if (data.result) {
                                var finishedItems = [];

                                // Retrieve finished operation requests
                                data.operationRequests.forEach((item) => {
                                    if (item.operation_status === 2 || item.operation_status === 3) {
                                        finishedItems.push(item);
                                    }
                                });

                                // If there any operation request finished, update instance information
                                if (finishedItems.length) {
                                    this.filter(true);
                                    this.finishedOperationRequestHandler(finishedItems);
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
        },
        computed: {
            operationRequestList: function () {
                var operationRequests = [];
                for (var instanceIndex in this.items) {
                    for (var requestIndex in this.items[instanceIndex].processing_operation_requests) {
                        operationRequests.push(this.items[instanceIndex].processing_operation_requests[requestIndex].id);
                    }
                }

                return operationRequests;
            },
            column2Show: function () {
                var column2Show = {};
                for (var columnKey in this.selectedColumns) {
                    var isSelected = this.selectedColumns[columnKey];
                    if (isSelected && this.columns.hasOwnProperty(columnKey)) {
                        column2Show[columnKey] = this.columns[columnKey];
                    }
                }

                return column2Show;
            }
        }
    }
</script>

<style scoped>
    .link-font {
        color: #0069ff;
        cursor: pointer;
    }

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

    .td-flex {
        display: flex;
        flex-direction: row;
        -webkit-box-align: center;
        align-items: center;
    }
</style>