<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1>{{ trade.no }} - 自助退款</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <div v-if="trade.refundable_fee === '0.00'" class="ui warning message">
                    此订单当前不可退款，如有疑问，请提交工单联系客服。
                </div>
                <div class="ui one column grid">
                    <column name="交易号" :inline="true" :dt-style="{width: '100px'}">
                        {{ trade.transaction_id }}
                    </column>

                    <column name="订单金额" :inline="true" :dt-style="{width: '100px'}">
                        <amount :amount="trade.fee_in_default_currency"></amount>
                    </column>

                    <column name="可退款金额" :inline="true" :dt-style="{width: '100px'}">
                        <amount :amount="trade.refundable_fee"></amount>
                    </column>

                    <column name="帐号余额" :inline="true" :dt-style="{width: '100px'}">
                        <amount :amount="$store.getters.user.credit"></amount>
                    </column>

                    <column name="申请退款金额" :inline="true" :dt-style="{width: '100px'}">
                        <form class="ui form" v-on:submit.prevent="refund">
                            <div class="ui small labeled action input">
                                <div class="ui label">
                                    {{ $store.getters.defaultCurrency.prefix }}
                                </div>
                                <input style="min-width: 150px;" type="number" step="0.01" min="0.01" :max="trade.refundable_fee" v-model="refundFee" required>
                                <button type="submit" class="ui small red button" :disabled="!refundable || isLoading">
                                    提交
                                </button>
                            </div>
                        </form>
                    </column>
                </div>
            </div>

            <div class="ui very padded no-shadow segment">
                <h3>订单退款记录</h3>
                <table class="ui unstackable very basic table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>订单号</th>
                        <th>交易号</th>
                        <th>金额</th>
                        <th>申请于</th>
                        <th>退款结果</th>
                    </tr>
                    </thead>
                    <tbody v-if="refundRecords.length">
                    <tr v-for="refundRecord in refundRecords">
                        <td>{{ refundRecord.id }}</td>
                        <td>{{ refundRecord.no }}</td>
                        <td>{{ refundRecord.transaction_id }}</td>
                        <td><amount :amount="refundRecord.fee_in_default_currency"></amount></td>
                        <td><local-time :time="refundRecord.created_at"></local-time></td>
                        <td>
                            <label v-if="refundRecord.status === 0" class="ui mini label">处理中</label>
                            <label v-else-if="refundRecord.status === 1" class="ui mini black label">已取消</label>
                            <label v-else-if="refundRecord.status === 2" class="ui mini red label">失败</label>
                            <template v-else-if="refundRecord.status === 3">
                                <label class="ui mini green label">成功</label>
                            </template>
                        </td>
                    </tr>
                    </tbody>
                    <tbody v-else>
                    <tr>
                        <td colspan="6" style="text-align: center">暂无记录</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "../ComputeInstance/Show/Information/Column";
    import Amount from "../../CreditRecord/Amount";
    import LocalTime from "../../LocalTime";
    export default {
        name: "PaymentTradeRefundIndex",
        components: {LocalTime, Amount, Column},
        data: function () {
            return {
                isLoading: false,
                trade: {},
                refundRecords: [],

                refundFee: 0,
            };
        },
        created: function () {
            axios.get(route("paymentTrades.refunds.index", [this.tradeId]))
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.retrieveTradeDetails(data);
                        this.refundFee = data.paymentTrade.refundable_fee;
                        if (data.hasOwnProperty("userCredit")) {
                            this.$store.commit("userCredit", data.userCredit);
                            this.refundFee = Math.max(Math.min(parseFloat(data.userCredit), parseFloat(data.paymentTrade.refundable_fee)), 0).toFixed(2);
                        }
                    } else {
                        this.$globalErrnoHandler(data);
                    }
                })
            ;
        },
        methods: {
            retrieveTradeDetails: function (data) {
                this.trade = data.paymentTrade;
                this.refundRecords = data.refundRecords;
            },
            refund: function () {
                this.isLoading = true;
                axios.post(route("paymentTrades.refunds.store", [this.tradeId]), {fee: this.refundFee}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.hasOwnProperty("paymentTrade")) {
                            this.retrieveTradeDetails(data);
                        }
                        if (data.result) {
                            if (data.refundStatus) {
                                this.positiveFloatingMessage("退款成功");
                            } else {
                                this.positiveFloatingMessage("退款请求正在处理中，请稍后查询结果，如长时间未退款，请提交工单联系客服");
                            }
                        } else {
                            this.$globalErrnoHandler(data, () => {
                                this.negativeFloatingMessage("申请退款失败");
                            });
                        }
                        if (data.hasOwnProperty("userCredit")) {
                            this.$store.commit("userCredit", data.userCredit);
                        }
                    })
                    .catch(() => {
                        this.negativeFloatingMessage("申请退款失败");
                    })
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            }
        },
        computed: {
            refundable: function () {
                try {
                    var refundableFee = new Decimal(this.trade.refundable_fee);
                    var userCredit = new Decimal(this.$store.getters.user.credit);
                    var inputFee = new Decimal(this.refundFee);
                } catch (e) {
                    return false;
                }
                return !(refundableFee.equals(0) || userCredit.lessThan(inputFee) || inputFee.greaterThan(refundableFee));
            },
            tradeId: function () {
                return this.$router.currentRoute.params.id;
            }
        },
    }
</script>

<style scoped>

</style>