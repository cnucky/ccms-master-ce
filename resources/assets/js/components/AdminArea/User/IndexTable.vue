<template>
    <div class="ui grid">
        <div class="ten wide middle aligned column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

            <model-index-mass-destroy-button :selectedItemCount="selectedItemCount()" v-on:click="massDestroy"></model-index-mass-destroy-button>

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
                    id="region-table"
                    class="sortable selectable very compact unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="true"
                    :columns="columns"
                    :column-components="columnComponents"
                    table-row-component="region-table-row"
                    :operable="true"
                    operation-component="user-index-operation-column"
                    :is-loading="isLoading"
                    :sort-disabled="{
                        name: true,
                        phone: true,
                        email: true,
                        instances_count: true,
                        local_volumes_count: true,
                        ipv4s_count: true,
                        ipv6s_count: true,
                    }"
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
    import Vue from "vue";
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";

    Vue.component("user-index-link-column", require("./Column/LinkColumn"));
    Vue.component("user-index-phone-column", require("./Column/PhoneColumn"));
    Vue.component("user-index-quota-column", require("./Column/UserQuotaColumn"));
    Vue.component("user-index-status-column", require("./Column/StatusColumn"));
    Vue.component("user-index-operation-column", require("./Column/OperationColumn"));

    export default {
        name: "IndexTable",
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    name: "姓名",
                    phone: "电话",
                    email: "Email",
                    user_quota_id: "配额",
                    instances_count: "实例",
                    local_volumes_count: "本地卷",
                    ipv4s_count: "IPv4",
                    ipv6s_count: "IPv6",
                    created_at: "注册于",
                    status: "状态",
                },
                columnComponents: {
                    id: "user-index-link-column",
                    name: "user-index-link-column",
                    phone: "user-index-phone-column",
                    email: "show-value",
                    user_quota_id: "user-index-quota-column",
                    instances_count: "show-value",
                    local_volumes_count: "show-value",
                    ipv4s_count: "show-value",
                    ipv6s_count: "show-value",
                    created_at: "local-time-column",
                    status: "user-index-status-column",
                },

                indexRouteName: "users.index",
                massDestroyRouteName: "users.massDestroy",
            };
        },
        mounted: function () {
            this.$nextTick(() => {
                this.filter();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                var items = data.users;
                this.items = items.data;
                this.paginator = items;
            },
            additionalFilterArguments: function (filterArguments) {
                return filterArguments;
            },
        },
        computed: {
        }
    }
</script>

<style scoped>

</style>