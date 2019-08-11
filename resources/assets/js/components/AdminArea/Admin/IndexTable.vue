<template>
    <div class="ui grid">
        <div class="ten wide middle aligned column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

            <model-index-create-button v-on:click="$router.push({name: 'admins.create'})"></model-index-create-button>

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
                    class="sortable selectable unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="false"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="true"
                    :operation-component="operationColumn"
                    :is-loading="isLoading"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
            ></sortable-table>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";

    Vue.component('admin-index-table-link-column', require("./Column/LinkColumn"));
    Vue.component('admin-index-table-role-column', require("./Column/RoleColumn"));
    Vue.component('admin-index-table-status-column', require("./../../ColumnComponent/StatusLabelColumn"));
    Vue.component('admin-index-table-operation-column', require("./Column/OperationColumn"));

    export default {
        name: "Index",
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    name: "姓名",
                    email: "邮箱",
                    phone: "联系电话",
                    role: "角色",
                    status: "状态",
                    created_at: "创建于",
                },
                columnComponents: {
                    id: "admin-index-table-link-column",
                    name: "admin-index-table-link-column",
                    email: "show-value",
                    phone: "show-value",
                    role: "admin-index-table-role-column",
                    status: "admin-index-table-status-column",
                    created_at: "local-time-column",
                },

                operationColumn: "admin-index-table-operation-column",

                indexRouteName: "admins.index",
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
                var items = data.admins;
                this.items = items.data;
                this.paginator = items;
            },
        },
        computed: {
        }
    }
</script>

<style scoped>

</style>