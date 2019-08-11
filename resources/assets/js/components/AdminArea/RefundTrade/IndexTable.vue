<template>
    <div class="ui grid">
        <div class="ten wide column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>

            <div ref="statusSelect" class="ui mini selection dropdown" v-bind:class="{ loading: isLoading, disabled: isLoading }">
                <input type="hidden" v-bind:value="status" v-on:change="filter">
                <i class="dropdown icon"></i>
                <div v-if="status === '0'" class="text">退款中</div>
                <div v-else-if="status === '1'" class="text">已取消</div>
                <div v-else-if="status === '2'" class="text">失败</div>
                <div v-else-if="status === '3'" class="text">成功</div>
                <div v-else-if="status === '-1'" class="text">所有</div>
                <div class="menu">
                    <div class="item" v-on:click="status = '-1'">所有</div>
                    <div class="item" v-on:click="status = '0'">退款中</div>
                    <div class="item" v-on:click="status = '1'">已取消</div>
                    <div class="item" v-on:click="status = '2'">失败</div>
                    <div class="item" v-on:click="status = '3'">所有</div>
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
                    class="sortable unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="false"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="true"
                    operation-component="refund-trade-index-operation-column"
                    :is-loading="isLoading"
                    :use-slot-row="false"
                    :sort-disabled="{
                        trade_id: true,
                        fee_in_default_currency: true,
                        user: true,
                        status: true,
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

    Vue.component("refund-trade-index-trade-column", require("./Column/TradeColumn"));
    Vue.component("refund-trade-index-user-column", require("./Column/UserColumn"));
    Vue.component("refund-trade-index-status-column", require("./Column/StatusColumn"));
    Vue.component("refund-trade-index-operation-column", require("./Column/OperationColumn"));

    export default {
        name: "PaymentTradeIndexTable",
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    trade_id: "充值订单",
                    no: "订单号",
                    fee_in_default_currency: "金额",
                    user: "用户",
                    status: "状态",
                    refunded_at: "退款于",
                    created_at: "创建于",
                },
                columnComponents: {
                    id: "show-value",
                    trade_id: "refund-trade-index-trade-column",
                    no: "show-value",
                    fee_in_default_currency: "amount-column",
                    user: "refund-trade-index-user-column",
                    status: "refund-trade-index-status-column",
                    refunded_at: "local-time-column",
                    created_at: "local-time-column",
                },

                indexRouteName: "admin.paymentTradeRefunds.index",

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
                var items = data.refundTrades;
                this.items = items.data;
                this.paginator = items;
            },
            additionalFilterArguments: function (filterArguments) {
                if (this.status === "0" || this.status === "1" || this.status === "2" || this.status === "3") {
                    filterArguments.status = this.status;
                }
                return filterArguments;
            },
        }
    }
</script>

<style scoped>

</style>