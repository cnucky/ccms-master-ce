<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1>区域</h1>
        </div>

        <div class="eight wide column">
            <button class="ui tiny icon button" v-on:click.prevent="loadRegions" v-bind:class="{ loading: isLoading }">
                <i class="sync icon"></i>
            </button>

            <router-link class="ui teal tiny button" :href="route('regions.create')" :to="route('regions.create')"><i class="plus icon"></i> 添加区域</router-link>

            <button class="ui red inverted tiny button" :disabled="selectedItemCount() <= 0"><i class="delete icon"></i> 删除</button>

            <div class="ui mini icon input">
                <input type="text" placeholder="Search..." v-model="filterKey">
                <i class="search icon"></i>
            </div>
        </div>

        <div class="eight wide column"></div>

        <div class="sixteen wide column">
            <sortable-table class="ui sortable middle aligned table" :data="items" :filter-key="filterKey" :selectable="true" :columns="columns" :column-components="{
            id: 'show-value',
            name: 'show-value',
            description: 'show-value',
            created_at: 'show-value',
            }" :operable="true" operation-component="operation" :is-loading="isLoading" ref="listTable"></sortable-table>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import DropdownMenu from "../../SemanticUI/DropdownMenu";
    import SortableTable from "../../JavascriptSortableTable";
    Vue.component('operation', require('./Operation'));
    export default {
        name: "Index",
        components: {SortableTable, DropdownMenu},
        data: function () {
            var columns = {
                id: "ID",
                name: "名称",
                description: "描述",
                created_at: "创建于",
            };

            var sortOrders = {};
            Object.keys(columns).forEach(function (key) {
                sortOrders[key] = 1
            });

            return {
                items: [],
                selected: [],
                positive: {},
                negative: {},
                isLoading: false,
                sortKey: "",
                filterKey: "",
                sortOrders: sortOrders,
                columns: columns,
                columnComponents: {
                    id: "show-value",
                    name: "show-value",
                    description: "show-value",
                    create_at: "show-value",
                },
            }
        },
        created: function () {
            this.loadZones();
        },
        mounted: function() {
            this.$nextTick(function () {
                $(this.$refs.listTable.$refs.master_checkbox).checkbox({
                    // check all children
                    onChecked: () => {
                        var
                            $childCheckbox  = $(this.$refs.listTable.$refs.tbody.$el).find('.child.checkbox')
                        ;
                        $childCheckbox.checkbox('check');
                    },
                    // uncheck all children
                    onUnchecked: () => {
                        var
                            $childCheckbox  = $(this.$refs.listTable.$refs.tbody.$el).find('.child.checkbox')
                        ;
                        $childCheckbox.checkbox('uncheck');
                    }
                });
            })
        },
        updated: function() {
            $(".ui.child.checkbox").checkbox();
        },
        methods: {
            loadZones: function () {
                this.isLoading = true;
                axios.get(route('regions.index'))
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            var regions = data.regions;

                            // var selected = {};

                            // for (let i = 0; i < regions.length; i++) {
                            //     selected[regions[i].id] = false;
                            // }

                            // this.selected = selected;
                            this.items = regions;
                        }
                    })
                    .catch(() => {})
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
            }
        },
        computed: {
        }
    }
</script>

<style scoped>
    .ui.table td {
        vertical-align: middle;
    }

    .ui.table .checkbox {
        margin: 0 0 0 0;
        width: 17px;
        height: 17px;
        display: block;
    }
</style>