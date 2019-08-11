<template>
    <div class="ui grid">
        <div class="ten wide column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

            <button v-if="!isAdmin" class="ui teal tiny button" v-on:click="() => {$refs.newVolumeModal.show();}"><i class="plus icon"></i> 创建</button>

            <button class="ui red inverted tiny button" :disabled="selectedItemCount() <= 0" v-on:click="massDetach"><i class="unlink icon"></i> 分离</button>

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
                    :sort-disabled="false"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:table-created="tableCreated"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
                    v-on:edit-item="editItem"
                    v-on:table-updated="tableUpdated"
            >
                <tr v-for="item in items">
                    <td>
                        <div v-if="item.protected">
                            <i class="shield alternate icon"></i>
                        </div>
                        <div class="ui fitted child checkbox" v-else-if="!item.processing_operation_requests.length">
                            <input type="checkbox" v-model="$refs.listTable.isItemSelected[item.id]">
                            <label></label>
                        </div>
                    </td>
                    <td>
                        {{ item.unique_id }}
                    </td>
                    <td>
                        {{ item.capacity }} GiB
                    </td>
                    <td>
                        <volume-bus :volume="item"></volume-bus>
                    </td>
                    <td>
                        <template v-if="item.instance">
                            <router-link :to="{name: 'computeInstances.dashboard', params: {id: item.instance.id}}" style="display: inline-block;">
                                <div class="instance-name">{{ item.instance.name }}</div>
                                <div class="instance-size">
                                    {{ item.instance.unique_id }} - {{ item.instance.client_instance_size.vCPU }} vCPU/ {{item.instance.client_instance_size.memory }} MiB
                                </div>
                            </router-link>
                        </template>
                        <template v-else>-</template>
                    </td>
                    <td>
                        <i :class="item.node.zone.region.icon_class"></i> {{ item.node.zone.region.name }} - {{ item.node.zone.name }}
                    </td>
                    <td>
                        <amount :amount="calculatePrice(item)"></amount>/hour
                    </td>
                    <td>
                        <status :status="item.status"></status>
                    </td>
                    <duration-column :entry="item" key-name="created_at"></duration-column>
                    <td>
                        <semantic-ui-dropdown-menu text="操作" v-bind:class="{loading: item.processing_operation_requests.length, disabled: item.processing_operation_requests.length}">
                            <div class="item" v-on:click="() => { volume2Change = item; $refs.resizeModal.show(); }"><i class="plus icon"></i> 扩容</div>
                            <div class="item" v-on:click="() => {volume2Change = item; $refs.advanceSettingModal.show();}"><i class="cogs icon"></i> 高级设置</div>

                            <div v-if="item.protected" class="item" v-on:click="toggleProtectMode(item)"><i class="unlock icon"></i> 解除保护</div>
                            <div v-else class="item" v-on:click="toggleProtectMode(item)"><i class="lock icon"></i> 保护模式</div>

                            <div class="ui divider"></div>
                            <div v-if="item.instance && !item.protected" class="item" v-on:click="detach(item)">
                                <i class="unlink icon"></i> 分离
                            </div>
                            <div v-else-if="!item.instance" class="item" v-on:click="attach(item)">
                                <i class="linkify icon"></i> 连接
                            </div>
                            <div v-if="!item.protected" class="item" v-on:click="release(item)"><i class="trash icon"></i> 释放</div>
                        </semantic-ui-dropdown-menu>
                    </td>
                </tr>
            </sortable-table>
        </div>

        <form-modal ref="resizeModal" class="small" :custom-header="(volume2Change ? volume2Change.unique_id : '') + ' - 扩容'" :no-stay-select="true" submit-button-text="提交" v-on:submit="() => { $refs.resizeForm.resize() }" :is-loading="modalLoading">
            <resize-form ref="resizeForm" :volume="volume2Change" :volume-price-per-gi-b-per-hour="volume2Change ? volume2Change.node.zone.storage_price_per_hour_per_gib : 0" v-on:operationRequestCreated="() => {
            $refs.resizeModal.hide();
            filter();
}" v-on:beforeSubmit="modalLoading = true" v-on:complete="modalLoading = false"></resize-form>
        </form-modal>

        <form-modal ref="advanceSettingModal" class="small" :custom-header="(volume2Change ? volume2Change.unique_id : '') + ' - 高级设置'" :no-scroll="true" :no-stay-select="true" submit-button-text="提交" v-on:submit="() => {$refs.advanceSettingForm.changeBus();}" :is-loading="modalLoading">
            <advance-setting-form ref="advanceSettingForm" :volume="volume2Change" v-on:beforeSubmit="modalLoading = true" v-on:complete="modalLoading = false" v-on:operationRequestCreated="(operationRequest) => {$emit('operationRequestCreated', operationRequest)}" v-on:success="() => {filter(); $refs.advanceSettingModal.hide()}" v-on:saved="(bus) => {volume2Change.bus = bus;}"></advance-setting-form>
        </form-modal>

        <form-modal ref="newVolumeModal" class="small" custom-header="创建卷" :no-scroll="true" :no-stay-select="true" submit-button-text="提交" v-on:submit="() => { $refs.newVolumeForm.newVolume() }" :is-loading="modalLoading">
            <new-volume-form ref="newVolumeForm" v-on:beforeSubmit="modalLoading = true" v-on:complete="modalLoading = false" v-on:success="() => {filter(); $refs.newVolumeModal.hide();}"></new-volume-form>
        </form-modal>

        <form-modal ref="attachVolumeModal" class="small" :custom-header="(volume2Change ? volume2Change.unique_id : '') + ' - 连接卷'" :no-scroll="true" :no-stay-select="true" submit-button-text="提交" v-on:submit="() => { $refs.attachVolumeForm.attachVolume() }" :is-loading="modalLoading">
            <attach-volume-form ref="attachVolumeForm" :volume="volume2Change" v-on:beforeSubmit="modalLoading = true" v-on:complete="modalLoading = false" v-on:success="() => {filter(); $refs.attachVolumeModal.hide();}"></attach-volume-form>
        </form-modal>
    </div>
</template>

<script>
    import indexOperation from "./../ModelIndex/IndexOperation";
    import pageOperation from "./../ModelIndex/PageOperation";
    import VolumeBus from "./../ClientArea/ComputeInstance/Show/VolumeBus";
    import ResizeForm from "./ResizeForm";
    import OperationRequestQuery from "./../OperationRequestQuery"
    import AdvanceSettingForm from "./AdvanceSettingForm";
    import NewVolumeForm from "./NewVolumeForm";
    import AttachVolumeForm from "./AttachVolumeForm";
    import ToggleProtectMode from "./ToggleProtectMode";
    import OnOperationFinished from "./OnOperationFinished";
    import Amount from "../CreditRecord/Amount";
    import Status from "./Status";

    export default {
        name: "Volumes",
        props: ["isAdmin"],
        components: {Status, Amount, AttachVolumeForm, NewVolumeForm, AdvanceSettingForm, ResizeForm, VolumeBus},
        mixins: [indexOperation, pageOperation, OperationRequestQuery, ToggleProtectMode, OnOperationFinished],
        data: function () {
            var operationRoutePrefix = "";
            if (this.isAdmin) {
                operationRoutePrefix = "admin.";
            }

            return {
                columns: {
                    unique_id: "ID",
                    capacity: "容量",
                    bus: "总线",
                    attached_compute_instance_id: "已连接实例",
                    area: "区域",
                    price_per_hour: "价格",
                    status: "状态",
                    created_at: "创建于",
                },

                operationRoutePrefix: operationRoutePrefix,

                indexRouteName: operationRoutePrefix + "volumes.index",
                massDestroyRouteName: operationRoutePrefix + "volumes.massRelease",

                noChangeURL: true,

                volume2Change: null,

                modalLoading: false,

                needOperationRequestResource: true,

                isMassDetaching: false,
                isMassReleasing: false,
            };
        },
        mounted: function () {
            this.$nextTick(() => {
                this.filter();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                // console.log(data);
                this.items = data.volumes.data;
                this.paginator = data.volumes;
            },
            tableCreated: function () {
            },
            editItem: function () {
            },
            detach: function (volume) {
                this.volumeDetachConfirmModal(() => {
                    axios.post(route(this.operationRoutePrefix + "localVolumes.operation.detach", [volume.id]))
                        .then(this.commonResponseHandler)
                        .catch(this.$axiosCatchError2Console)
                    ;
                });
            },
            attach: function (volume) {
                this.volume2Change = volume;
                this.$refs.attachVolumeModal.show();
            },
            massDetach: function () {
                this.volumeDetachConfirmModal(() => {
                    var items = this.$refs.listTable.selectedItemList;
                    this.isMassDetaching = true;
                    axios.post(route(this.operationRoutePrefix + "localVolumes.operation.massDetach"), {items: items}, {vueInstance: this})
                        .then(this.commonResponseHandler)
                        .catch(this.$axiosCatchError2Console)
                        .then(() => {
                            this.isMassDetaching = false;
                        })
                    ;
                });
            },
            release: function (volume) {
                this.volumeReleaseConfirmModal(() => {
                    axios.post(route(this.operationRoutePrefix + "localVolumes.operation.release", [volume.id]))
                        .then(this.commonResponseHandler)
                        .catch(this.$axiosCatchError2Console)
                    ;
                });
            },
            massRelease: function () {
                this.volumeReleaseConfirmModal(() => {
                    var items = this.$refs.listTable.selectedItemList;
                    this.isMassReleasing = true;
                    axios.post(route(this.operationRoutePrefix + "localVolumes.operation.massRelease"), {items: items}, {vueInstance: this})
                        .then(this.commonResponseHandler)
                        .catch(this.$axiosCatchError2Console)
                        .then(() => {
                            this.isMassReleasing = false;
                        })
                    ;
                });
            },
            commonResponseHandler: function (response) {
                var data = response.data;
                if (data.result) {
                    this.filter();
                    this.positiveFloatingMessage(this.$t("common.operationRequestSubmitted"));
                } else {
                    this.$globalErrnoHandler(data);
                }
            },
            volumeDetachConfirmModal: function (onApprove) {
                this.volumeOperationConfirmModal("卷 - 分离确认", "确定分离所选的卷吗？请确认已在操作系统上卸载目标卷上的文件系统，且已同步I/O缓存。", onApprove);
            },
            volumeReleaseConfirmModal: function (onApprove) {
                this.volumeOperationConfirmModal("卷 - 释放确认", "确定释放所选的卷？存放于这些卷上的数据，在卷释放后将无法恢复！", onApprove);
            },
            volumeOperationConfirmModal: function (header, content, onApprove) {
                this
                    .confirmModal()
                    .withHeader(header)
                    .withContent(content)
                    .withOnApprove(onApprove)
                    .show()
                ;
            },
            calculatePrice: function (volume) {
                return new Decimal(volume.node.zone.storage_price_per_hour_per_gib).mul(volume.capacity).toString();
            }
        },
        computed: {
            operationRequestList: function () {
                var operationRequestList = [];
                for (var itemIndex in this.items) {
                    for (var operationIndex in this.items[itemIndex].processing_operation_requests)
                        operationRequestList.push(this.items[itemIndex].processing_operation_requests[operationIndex].id)
                }
                return operationRequestList;
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