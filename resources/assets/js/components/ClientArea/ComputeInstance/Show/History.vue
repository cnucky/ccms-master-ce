<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">操作历史</h3>
        </div>

        <div class="ten wide middle aligned column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>
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
                    class="selectable unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="false"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="false"
                    :is-loading="isLoading"
                    :sort-disabled="true"
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
    import indexOperation from "./../../../ModelIndex/IndexOperation";
    import pageOperation from "./../../../ModelIndex/PageOperation";

    Vue.component("operation-request-index-type-column", require("./Column/OperationRequestTypeColumn"));
    Vue.component("operation-request-index-status-column", require("./Column/OperationRequestStatusColumn"));

    export default {
        name: "History",
        props: ["instance", "operationRoutePrefix"],
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    type: "类型",
                    operation_status: "状态",
                    created_at: "时间",
                },
                columnComponents: {
                    id: "show-value",
                    type: "operation-request-index-type-column",
                    operation_status: "operation-request-index-status-column",
                    created_at: "duration-column",
                },

                indexRouteName: this.operationRoutePrefix + "computeInstances.operationRequests",
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
                var items = data.operationRequests;
                this.items = items.data;
                this.paginator = items;
            },
            additionalFilterArguments: function (filterArguments) {
                filterArguments.computeInstance = this.instance.id;
                return filterArguments;
            },
        },
    }
</script>

<style scoped>

</style>