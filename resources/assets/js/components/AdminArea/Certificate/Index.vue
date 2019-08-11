<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">证书列表</h1>
        </div>

        <div class="sixteen wide column">
            <model-index-refresh-button v-on:click="filter" :is-loading="isLoading"></model-index-refresh-button>
            <model-index-create-button model-name="证书" v-on:click="() => {$router.push({name: 'certificates.create'})}"></model-index-create-button>
            <model-index-mass-destroy-button :selectedItemCount="selectedItemCount()" v-on:click="massDestroy"></model-index-mass-destroy-button>
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
                    operation-component="certificate-index-operation-column"
                    :is-loading="isLoading"
                    :sort-disabled="{
                        name: true,
                        fingerprint: true,
                        subject: true,
                    }"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
                    v-on:table-updated="tableUpdated"
            ></sortable-table>
        </div>
    </div>
</template>

<script>
    import Vue from "vue";
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";

    Vue.component("certificate-index-operation-column", require("./Column/Operation"));

    export default {
        name: "Index",
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    name: "名称",
                    fingerprint: "指纹",
                    subject: "Subject",
                    valid_to: "有效至",
                    updated_at: "更新于",
                },
                columnComponents: {
                    id: "show-value",
                    name: "show-value",
                    fingerprint: "show-value",
                    subject: "show-value",
                    valid_to: "local-time-column",
                    updated_at: "duration-column",
                },

                indexRouteName: "certificates.index",
                massDestroyRouteName: "certificates.massDestroy",
            };
        },
        mounted: function () {
            this.$nextTick(() => {
                this.filter();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                var items = data.certificates;
                this.items = items.data;
                this.paginator = items;
            },
        }
    }
</script>

<style scoped>

</style>