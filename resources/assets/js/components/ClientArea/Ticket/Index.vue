<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">我的工单</h1>
        </div>

        <div class="ten wide middle aligned column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

            <router-link class="ui teal tiny button" :to="{name: 'tickets.create'}"><i class="plus icon"></i> 创建工单</router-link>
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
                    class="unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="false"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="true"
                    operation-component="ticket-operation-column"
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
    import Vue from 'vue';
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";

    Vue.component('ticket-link-column', require("./Column/LinkColumn"));
    Vue.component('ticket-department-column', require("./Column/DepartmentColumn"));
    Vue.component('ticket-operation-column', require("./Column/Operation"));
    Vue.component('ticket-status-column', require("./../../Ticket/StautsColumn"));

    export default {
        name: "Index",
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                isLoading: false,
                columns: {
                    id: "ID",
                    department_id: "部门",
                    title: "标题",
                    status: "状态",
                    replied_at: "最后回复于",
                },
                columnComponents: {
                    id: "ticket-link-column",
                    department_id: "ticket-department-column",
                    title: "ticket-link-column",
                    status: "ticket-status-column",
                    replied_at: "duration-column",
                },

                indexRouteName: "tickets.index",
            };
        },
        mounted: function () {
            this.$nextTick(() => {
                this.filter();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                var items = data.tickets;
                this.items = items.data;
                this.paginator = items;
            },
        },
    }
</script>

<style scoped>

</style>