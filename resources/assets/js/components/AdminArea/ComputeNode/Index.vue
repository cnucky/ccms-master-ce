<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1>{{ $t('common.computeNode') }}</h1>
        </div>

        <div class="ten wide middle aligned column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

            <model-index-create-button v-on:click="$refs.formModal.create()"></model-index-create-button>

            <model-index-mass-destroy-button :selectedItemCount="selectedItemCount()" v-on:click="massDestroy"></model-index-mass-destroy-button>

            <index-table-search-input v-model="filterKey" v-bind:is-loading="isLoading" v-on:search="filter"></index-table-search-input>

            <region-select :available-regions="availableRegions" v-model="selectedRegion" :is-loading="isLoading" v-on:region-selected="regionSelected"></region-select>
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
                    id="region-table"
                    class="sortable selectable unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="true"
                    :columns="columns"
                    :column-components="columnComponents"
                    table-row-component="compute-node-table-row"
                    :operable="true"
                    operation-component="compute-node-operation"
                    :is-loading="isLoading"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:table-created="tableCreated"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
                    v-on:edit-item="editItem"
                    v-on:table-updated="tableUpdated"
            ></sortable-table>
        </div>

        <compute-node-form-modal ref="formModal" :available-regions="availableRegions" :available-zones="availableZones" v-on:item-created="filter" v-on:item-updated="itemUpdated"></compute-node-form-modal>
    </div>
</template>

<script>
    import Vue from 'vue';
    import computeNodeFormModal from "./ComputeNodeFormModal";
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";
    import RegionSelect from "./RegionSelect";
    import regionColumn from "./RegionColumn";

    Vue.component('compute-node-operation', require('./Operation'));
    Vue.component("compute-node-region-column", regionColumn);
    Vue.component("compute-node-table-row", require("./TableRow"));
    Vue.component("compute-node-double-column", require("./Column/Double"));
    Vue.component("compute-node-disk-space-usage", require("./Column/DiskSpaceUsage"));
    Vue.component("compute-node-memory-usage", require("./Column/MemoryUsage"));
    Vue.component("compute-node-status-label-column", require("./../../ColumnComponent/StatusLabelColumn"));

    Vue.component("compute-node-id-column", require("./Column/Id2ShowColumn"));
    Vue.component("compute-node-name-column", require("./Column/NameColumn"));

    export default {
        name: "Index",
        components: {computeNodeFormModal, RegionSelect},
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    name: "名称",
                    host: "Host",
                    zone_id: "区域",
                    cpu_utilization: "CPU使用",
                    current_disk_free_in_gib_unit: "可用储存",
                    current_memory_free_in_mib_unit: "可用内存",
                    status: "状态",
                },
                columnComponents: {
                    id: "compute-node-id-column",
                    name: "compute-node-name-column",
                    host: "show-value",
                    zone_id: "compute-node-region-column",
                    cpu_utilization: "compute-node-double-column",
                    current_disk_free_in_gib_unit: "compute-node-disk-space-usage",
                    current_memory_free_in_mib_unit: "compute-node-memory-usage",
                    status: "compute-node-status-label-column",
                },

                availableRegions: {},
                availableZones: {},
                computeNodes: [],

                serverTime: null,

                selectedRegion: "",

                indexRouteName: "computeNodes.index",
                massDestroyRouteName: "computeNodes.massDestroy",
            }
        },
        created: function () {
        },
        mounted: function () {
            this.$nextTick(() => {
                this.filter();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                this.serverTime = data.serverTime;
                var items = data.computeNodes;
                this.items = items.data;
                this.paginator = items;

                this.availableRegions = data.availableRegions;
                this.availableZones = data.availableZones;

                regionColumn.availableRegions = this.availableRegions;
                regionColumn.availableZones = this.availableZones;
            },
            tableCreated: function () {
                // this.filter();
            },
            editItem: function (item) {
                this.$refs.formModal.edit(item);
            },
            additionalFilterArguments: function (filterArguments) {
                if (this.selectedRegion.length)
                    filterArguments.region = this.selectedRegion;

                return filterArguments;
            },
            regionSelected: function () {
                this.filter();
            }
        },
        computed: {
        }
    }
</script>

<style scoped>
    #region-table >>> tbody > tr {
        /* cursor: pointer; */
    }
</style>