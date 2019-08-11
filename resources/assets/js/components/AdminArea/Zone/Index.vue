<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1>{{ $t('common.zone') }}</h1>
        </div>

        <div class="ten wide middle aligned column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

            <model-index-create-button v-on:click="() => { $refs.zoneFormModal.create() }"></model-index-create-button>

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
                    table-row-component="region-table-row"
                    :operable="true"
                    operation-component="operation"
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

        <zone-form-modal ref="zoneFormModal" :options="availableRegions" v-on:item-created="itemCreated" v-on:item-updated="itemUpdated"></zone-form-modal>
    </div>
</template>

<script>
    import Vue from 'vue';
    import ZoneFormModal from "./ZoneFormModal";
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";
    import RegionSelect from "./RegionSelect";
    Vue.component('operation', require('./Operation'));
    Vue.component('region-name-with-icon-column', require('./../Region/NameWithIcon'));
    Vue.component('zone-index-link-column', require('./Column/LinkColumn'));
    Vue.component('zone-available-memory-column', require('./Column/MemoryColumn'));
    Vue.component('zone-available-disk-capacity-column', require('./Column/DiskColumn'));
    export default {
        name: "Index",
        components: {RegionSelect, ZoneFormModal},
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    name: "名称",
                    description: "描述",
                    region: {
                        name: this.$t("common.region")
                    },
                    memoryCapacity: "可用内存",
                    diskCapacity: "可用储存",
                    compute_nodes_count: "计算节点",
                    created_at: "创建于",
                },
                columnComponents: {
                    id: "zone-index-link-column",
                    name: "zone-index-link-column",
                    description: "show-value",
                    region: {
                        name: "region-name-with-icon-column",
                    },
                    memoryCapacity: "zone-available-memory-column",
                    diskCapacity: "zone-available-disk-capacity-column",
                    compute_nodes_count: "show-value",
                    created_at: "local-time-column",
                },
                availableRegions: [],

                selectedRegion: "",

                indexRouteName: "zones.index",
                massDestroyRouteName: "zones.massDestroy",
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
                var items = data.zones;
                this.items = items.data;
                this.paginator = items;
                this.availableRegions = data.availableRegions;
            },
            tableCreated: function () {
                // this.filter();
            },
            editItem: function (item) {
                this.$refs.zoneFormModal.edit(item);
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