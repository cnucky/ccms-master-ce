<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1>{{ $t('common.region') }}</h1>
        </div>

        <div class="eight wide middle aligned column">
            <div v-if="true || !searchInputFocusing" style="display: inline;">
                <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

                <model-index-create-button v-on:click="$refs.regionFormModal.create()" v-bind:model-name="$t('common.region')"></model-index-create-button>

                <model-index-mass-destroy-button :selectedItemCount="selectedItemCount()" v-on:click="massDestroy"></model-index-mass-destroy-button>
            </div>

            <index-table-search-input v-model="filterKey" v-bind:is-loading="isLoading" v-on:search="filter"></index-table-search-input>
        </div>

        <div class="eight wide middle right aligned column">
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
                    operation-component="region-index-operation-column"
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

        <region-form-modal class="small" ref="regionFormModal" v-on:item-created="itemCreated" v-on:item-updated="itemUpdated"></region-form-modal>
    </div>
</template>

<script>
    import Vue from 'vue';
    import RegionFormModal from "./RegionFormModal";
    Vue.component('region-index-operation-column', require('./Operation'));
    Vue.component('region-table-row', require('./TableRow'));
    Vue.component('region-name-with-icon-column', require('./NameWithIcon'));
    export default {
        name: "Index",
        components: {RegionFormModal},
        data: function () {
            return {
                items: [],
                paginator: {},
                positive: {},
                negative: {},
                isLoading: false,
                filterKey: "",
                columns: {
                    id: "ID",
                    name: "名称",
                    description: "描述",
                    created_at: "创建于",
                },
                columnComponents: {
                    id: "show-value",
                    name: "region-name-with-icon-column",
                    description: "show-value",
                    created_at: "show-value",
                },
                item: {
                    test: "test",
                },
                searchInputFocusing: false,
                page: 1,
                prePageItem: 15,
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
            loadRegions: function (filterArguments) {
                this.isLoading = true;
                axios.get(route('regions.index', filterArguments))
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            var regions = data.regions;

                            // var selected = {};

                            // for (let i = 0; i < regions.length; i++) {
                            //     selected[regions[i].id] = false;
                            // }

                            // this.selected = selected;
                            this.items = regions.data;
                            this.paginator = regions;
                        }
                    })
                    .catch(this.$errorToNegativeFloatingMessage)
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            },
            selectedItemCount: function () {
                if (this.$refs.listTable) {
                    return this.$refs.listTable.selectedItemCount;
                }
                return 0;
            },
            massDestroy: function () {
                this.$root.confirmModal()
                    .withHeader("注意")
                    .withContent("确定要删除选中的项目吗？此操作不可恢复！")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        var items = this.$refs.listTable.selectedItemList;
                        axios
                            .post(route('regions.massDestroy'), { items: items })
                            .then((response) => {
                                if (response.data.result) {
                                    items.forEach((key) => {
                                        this.$refs.listTable.destroyByKey(key);
                                    });
                                    this.positiveFloatingMessage("成功删除" + response.data.deletedItemCount + "个项目");
                                }
                            })
                            .catch(this.$errorToNegativeFloatingMessage)
                            .then(() => {
                                this.isLoading = false;
                            })
                        ;
                    })
                    .show()
                ;
            },
            tableCreated: function () {
                // this.filter();
            },
            tableUpdated: function () {
                $(".popup-table-row").popup({
                    delay: {
                        show: 50,
                        hide: 100
                    },
                    hoverable: true,
                    distanceAway: 42,
                    offset: 100,
                });
            },
            itemCreated: function (item) {
                this.items.push(item);
            },
            itemUpdated: function (item) {
                var index = this.$refs.listTable.findItemIndex(item);
                if (index >= 0) {
                    this.items.splice(index, 1, item);
                }
            },
            editItem: function (item) {
                this.$refs.regionFormModal.edit(item);
            },
            pageInputChange: function () {
                console.log(this.page);
            },
            prevPage: function () {
                --this.page;
                this.filter();
            },
            nextPage: function () {
                ++this.page;
                this.filter();
            },
            jumpToPage: function (page) {
                this.page = page;
                this.filter();
            },
            sortBy: function (key, isAsc) {
                this.filter();
            },
            filter: function () {
                var filterArguments = {
                    page: this.page,
                    prePage: this.prePageItem,
                };

                if (this.filterKey && this.filterKey.trim().length) {
                    filterArguments.search = this.filterKey.trim();
                }

                if (this.$refs.listTable.sortKey.length) {
                    filterArguments.sortKey = this.$refs.listTable.sortKey;
                    filterArguments.isAsc = this.$refs.listTable.sortOrders[filterArguments.sortKey];
                }

                this.$router.push({name: 'regions.index', query: filterArguments});

                this.$refs.listTable.unselectAllItem();
                this.loadRegions(filterArguments);
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