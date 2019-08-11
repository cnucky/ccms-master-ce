<template>
    <div class="ui grid">
        <div class="ten wide middle aligned column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

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
                    :selectable="true"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="true"
                    :operation-component="operationComponent"
                    :is-loading="isLoading"
                    :sort-disabled="{
                        human_readable_first_usable: true,
                        human_readable_last_usable: true,
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

    Vue.component("ip-pool-assignment-index-user-column", require("./../ComputeInstance/Column/UserColumn"));
    Vue.component("ip-pool-assignment-index-instance-column", require("./../IPPool/Show/Column/InstanceColumn"));
    Vue.component("ip-pool-assignment-index-unbindable-column", require("./../IPPool/Show/Column/UnbindableColumn"));
    Vue.component("ip-assignment-index-operation-column", require("./Column/OperationColumn"));

    export default {
        name: "Assignment",
        props: ["modelInternalName", "modelName"],
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    human_readable_first_usable: "首",
                    human_readable_last_usable: "末",
                    user_id: "用户",
                    nic_id: "实例",
                    unbindable: "弹性",
                    assigned_at: "分配于",
                },
                columnComponents: {
                    id: "show-value",
                    human_readable_first_usable: "show-value",
                    human_readable_last_usable: "show-value",
                    user_id: "ip-pool-assignment-index-user-column",
                    nic_id: "ip-pool-assignment-index-instance-column",
                    unbindable: "ip-pool-assignment-index-unbindable-column",
                    assigned_at: "duration-column",
                },

                operationComponent: "ip-assignment-index-operation-column",

                indexRouteName: this.modelInternalName + ".assignments.index",
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
                var items = data.assignments;
                this.items = items.data;
                this.paginator = items;
            },
        },
    }
</script>

<style scoped>

</style>