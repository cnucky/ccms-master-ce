<template>
    <div class="ui grid">
        <div class="ten wide column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

            <div ref="statusSelect" class="ui mini selection dropdown" v-bind:class="{ loading: isLoading, disabled: isLoading }">
                <input type="hidden" v-bind:value="status" v-on:change="filter">
                <i class="dropdown icon"></i>
                <div v-if="status === '0'" class="text">未支付</div>
                <div v-else-if="status === '1'" class="text">已支付</div>
                <div v-else-if="status === '2'" class="text">已取消</div>
                <div v-else-if="status === '-1'" class="text">所有</div>
                <div class="menu">
                    <div class="item" v-on:click="status = '-1'">所有</div>
                    <div class="item" v-on:click="status = '0'">未支付</div>
                    <div class="item" v-on:click="status = '1'">已支付</div>
                    <div class="item" v-on:click="status = '2'">已取消</div>
                </div>
            </div>

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
                    id="attached-volume-table"
                    class="sortable very compact unstackable small"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="false"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="true"
                    operation-component="admin-trade-index-operation-column"
                    :is-loading="isLoading"
                    :use-slot-row="false"
                    :sort-disabled="{
                        transaction_id: true,
                        basic_payment_module: true,
                    }"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
            >
            </sortable-table>
        </div>
    </div>
</template>

<script>
    import Vue from "vue";

    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";

    Vue.component("admin-record-index-user-column", require("./../ComputeInstance/Column/UserColumn"));
    Vue.component("admin-record-index-amount-column", require("./../../CreditRecord/AmountColumn"));
    Vue.component("admin-trade-index-operation-column", require("./Column/OperationColumn"));
    Vue.component("admin-trade-index-amount-column", require("./../../ClientArea/Billing/Column/AmountColumn"));
    Vue.component("admin-trade-index-payment-module-column", require("./Column/PaymentModuleColumn"));
    Vue.component("admin-trade-index-status-column", require("./../../PaymentTrade/StatusColumn"));

    export default {
        name: "PaymentTradeIndexTable",
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    no: "订单号",
                    // transaction_id: "交易号",
                    basic_payment_module: "付款方式",
                    fee_in_default_currency: "金额",
                    user_id: "用户",
                    status: "状态",
                    paid_at: "支付于",
                    created_at: "创建于",
                },
                columnComponents: {
                    id: "show-value",
                    no: "show-value",
                    // transaction_id: "show-value",
                    basic_payment_module: "admin-trade-index-payment-module-column",
                    fee_in_default_currency: "admin-trade-index-amount-column",
                    user_id: "admin-record-index-user-column",
                    status: "admin-trade-index-status-column",
                    paid_at: "local-time-column",
                    created_at: "local-time-column",
                },

                indexRouteName: "admin.paymentTrades.index",

                status: "-1"
            };
        },
        created: function () {
            if (typeof this.$router.currentRoute.query.status === "string") {
                this.status = this.$router.currentRoute.query.status;
            }
        },
        mounted: function () {
            this.$nextTick(() => {
                this.filter();
                $(this.$refs.statusSelect).dropdown();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                var items = data.paymentTrades;
                this.items = items.data;
                this.paginator = items;
            },
            additionalFilterArguments: function (filterArguments) {
                filterArguments.status = this.status;
                return filterArguments;
            },
        }
    }
</script>

<style scoped>

</style>