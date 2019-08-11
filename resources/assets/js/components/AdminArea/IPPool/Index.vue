<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">{{ modelName }}管理</h1>
        </div>

        <div class="ten wide column">
            <model-index-refresh-button v-on:click="filter()"></model-index-refresh-button>
            <model-index-create-button v-on:click="$router.push({name: modelInternalName + '.create'})" :model-name="modelName"></model-index-create-button>
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
                    id="ip-pool-table"
                    class="selectable sortable unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="true"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="true"
                    operation-component="ip-pool-operation-column"
                    :is-loading="isLoading"
                    :sort-disabled="{
                        usage: true,
                        usage_percentage: true,
                    }"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
                    v-on:table-updated="tableUpdated"
            >
            </sortable-table>
        </div>
    </div>
</template>

<script>
    import Vue from "vue";

    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";

    Vue.component("ip-pool-show-id-with-link-column", require("./Column/ShowIdWithLink"));
    Vue.component("ip-pool-usage-column", require("./Column/Usage"));
    Vue.component("ip-pool-percentage-column", require("./Column/UsagePercentage"));
    Vue.component("ip-pool-type-column", require("./Column/Type"));
    Vue.component("ip-pool-status-column", require("./Column/Status"));
    Vue.component("ip-pool-operation-column", require("./Column/Operation"));
    export default {
        name: "Index",
        mixins: [indexOperation, pageOperation],
        props: ["modelInternalName", "modelName"],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    human_readable_first_usable_ip: "首可用IP",
                    human_readable_last_usable_ip: "末可用IP",
                    subnet_network_bits: "子网长度",
                    type: "类型",
                    usage: "使用量",
                    usage_percentage: "使用率",
                    status: "状态"
                },
                columnComponents: {
                    id: "ip-pool-show-id-with-link-column",
                    human_readable_first_usable_ip: "show-value",
                    human_readable_last_usable_ip: "show-value",
                    subnet_network_bits: "show-value",
                    type: "ip-pool-type-column",
                    usage: "ip-pool-usage-column",
                    usage_percentage: "ip-pool-percentage-column",
                    status: "ip-pool-status-column",
                },

                indexRouteName: this.modelInternalName + ".index",
                massDestroyRouteName: this.modelInternalName + ".massDestroy",
            };
        },
        mounted: function () {
            this.$nextTick(() => {
                this.filter();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                var items = data.pools;
                this.items = items.data;
                this.paginator = items;
            },
        }
    }
</script>

<style scoped>

</style>