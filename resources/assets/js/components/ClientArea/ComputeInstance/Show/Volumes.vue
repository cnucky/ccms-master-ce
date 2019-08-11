<template>
    <div class="ui grid">
        <div class="eight wide middle aligned column hidden">
            <h3 class="ui header">已连接到本实例的卷</h3>
        </div>

        <div class="ten wide column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

            <button class="ui teal tiny button" v-on:click="() => {$refs.newVolumeModal.show();}"><i class="plus icon"></i> 创建</button>

            <button class="ui red inverted tiny button" :disabled="selectedItemCount() <= 0" v-on:click="massDetach" style="margin-left: 25px;"><i class="unlink icon"></i> 分离</button>

            <button class="ui red inverted tiny button" :disabled="selectedItemCount() <= 0" v-on:click="massRelease"><i class="trash icon"></i> 释放</button>
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
                        <i v-if="item.protected" class="shield alternate icon"></i>
                        <div class="ui fitted child checkbox" v-else-if="!item.processing_operation_requests.length">
                            <input type="checkbox" v-model="$refs.listTable.isItemSelected[item.id]">
                            <label></label>
                        </div>
                    </td>
                    <td>
                        {{ item.unique_id }} <label v-if="item.attach_order === 0" class="ui mini green label">首选启动盘</label>
                    </td>
                    <td>
                        {{ item.capacity }} GiB
                    </td>
                    <td>
                        <volume-bus :volume="item"></volume-bus>
                    </td>
                    <td>
                        {{ item.created_at }}
                    </td>
                    <td>
                        <semantic-ui-dropdown-menu text="操作" v-bind:class="{loading: item.processing_operation_requests.length, disabled: item.processing_operation_requests.length}">
                            <div class="item" v-on:click="() => { volume2Change = item; $refs.resizeModal.show(); }"><i class="plus icon"></i> 扩容</div>
                            <div class="item" v-on:click="() => {volume2Change = item; $refs.advanceSettingModal.show();}"><i class="cogs icon"></i> 高级设置</div>
                            <div class="item" v-if="item.attach_order !== 0" v-on:click="togglePrimaryBootableDisk(item)"><i class="check circle icon"></i> 设为首选启动盘</div>
                            <div v-if="item.protected" class="item" v-on:click="toggleProtectMode(item)"><i class="unlock icon"></i> 解除保护</div>
                            <div v-else class="item" v-on:click="toggleProtectMode(item)"><i class="lock icon"></i> 保护模式</div>
                            <template v-if="!item.protected">
                                <div class="ui divider"></div>
                                <div class="item" v-on:click="detach(item)"><i class="unlink icon"></i> 分离</div>
                                <div class="item" v-on:click="release(item)"><i class="trash icon"></i> 释放</div>
                            </template>
                        </semantic-ui-dropdown-menu>
                    </td>
                </tr>
            </sortable-table>
        </div>

        <form-modal ref="resizeModal" class="small" :custom-header="(volume2Change ? volume2Change.unique_id : '') + ' - 扩容'" :no-stay-select="true" submit-button-text="提交" v-on:submit="() => { $refs.resizeForm.resize() }" :is-loading="modalLoading">
            <resize-form ref="resizeForm" :volume-price-per-gi-b-per-hour="instance.node.zone.storage_price_per_hour_per_gib" :volume="volume2Change" v-on:operationRequestCreated="() => {
            $refs.resizeModal.hide();
            filter();
}" v-on:beforeSubmit="modalLoading = true" v-on:complete="modalLoading = false"></resize-form>
        </form-modal>

        <form-modal ref="advanceSettingModal" class="small" :custom-header="(volume2Change ? volume2Change.unique_id : '') + ' - 高级设置'" :no-scroll="true" :no-stay-select="true" submit-button-text="提交" v-on:submit="() => {$refs.advanceSettingForm.changeBus();}" :is-loading="modalLoading">
            <advance-setting-form ref="advanceSettingForm" :volume="volume2Change" v-on:beforeSubmit="modalLoading = true" v-on:complete="modalLoading = false" v-on:operationRequestCreated="(operationRequest) => {$emit('operationRequestCreated', operationRequest)}" v-on:success="() => {filter(); $refs.advanceSettingModal.hide()}" v-on:saved="(bus) => {volume2Change.bus = bus;}"></advance-setting-form>
        </form-modal>

        <form-modal ref="newVolumeModal" class="small" custom-header="创建卷" :no-scroll="true" :no-stay-select="true" submit-button-text="提交" v-on:submit="() => { $refs.newVolumeForm.newVolume() }" :is-loading="modalLoading">
            <new-volume-form ref="newVolumeForm" :target-instance="instance" :unit-price="instance.node.zone.storage_price_per_hour_per_gib" v-on:beforeSubmit="modalLoading = true" v-on:complete="modalLoading = false" v-on:success="() => {filter(); $refs.newVolumeModal.hide();}"></new-volume-form>
        </form-modal>
    </div>
</template>

<script>
    import indexOperation from "./../../../ModelIndex/IndexOperation";
    import pageOperation from "./../../../ModelIndex/PageOperation";
    import VolumeBus from "./VolumeBus";
    import ResizeForm from "../../../Volume/ResizeForm";
    import OperationRequestQuery from "./../../../OperationRequestQuery"
    import AdvanceSettingForm from "../../../Volume/AdvanceSettingForm";
    import NewVolumeForm from "../../../Volume/NewVolumeForm";
    import ToggleProtectMode from "./../../../Volume/ToggleProtectMode";
    import TogglePrimaryBootableDisk from "./../../../Volume/TogglePrimaryBootableDisk";
    import OnOperationFinished from "./../../../Volume/OnOperationFinished";

    export default {
        name: "Volumes",
        components: {NewVolumeForm, AdvanceSettingForm, ResizeForm, VolumeBus},
        mixins: [indexOperation, pageOperation, OperationRequestQuery, ToggleProtectMode, TogglePrimaryBootableDisk, OnOperationFinished],
        props: ["instance", "operationRoutePrefix"],
        data: function () {
            return {
                columns: {
                    unique_id: "ID",
                    capacity: "容量",
                    bus: "总线",
                    created_at: "创建于",
                },

                indexRouteName: this.operationRoutePrefix + "volumes.index.by.computeInstance",
                massDestroyRouteName: this.operationRoutePrefix + "volumes.massRelease",

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
            additionalFilterArguments: function (filterArguments) {
                filterArguments.computeInstance = this.instance.id;
                return filterArguments;
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
            }
        },
        computed: {
            operationRequestList: function () {
                var operationRequestList = [];
                for (var itemIndex in this.items) {
                    for (var operationIndex in this.items[itemIndex].processing_operation_requests)
                        operationRequestList.push(this.items[itemIndex].processing_operation_requests[operationIndex])
                }
                return operationRequestList;
            },
        }
    }
</script>

<style scoped>

</style>