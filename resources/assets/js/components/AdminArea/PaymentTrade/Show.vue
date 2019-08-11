<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">充值订单详情</h1>
        </div>

        <div class="sixteen wide column" v-if="!paymentTradeData">
            <div class="ui very padded no-shadow segment loading"></div>
        </div>

        <template v-else>
            <div class="sixteen wide column">
                <div class="ui very padded no-shadow segment">
                    <h3 class="ui header">订单信息</h3>
                    <div class="ui one column grid">
                        <column name="用户" :inline="true" :dt-style="dtStyle"><router-link :to="{name: 'users.show', params: {id: paymentTradeData.user.id}}">#{{ paymentTradeData.user.id }} {{ paymentTradeData.user.name }}</router-link></column>
                        <column name="订单号" :inline="true" :dt-style="dtStyle">{{ paymentTradeData.paymentTrade.no }}</column>
                        <column v-if="paymentTradeData.paymentTrade.transaction_id" name="交易号" :inline="true" :dt-style="dtStyle">{{ paymentTradeData.paymentTrade.transaction_id }}</column>
                        <column name="模块" :inline="true" :dt-style="dtStyle">
                            <router-link v-if="paymentTradeData.module" :to="{name: 'paymentModules.edit', params: {id: paymentTradeData.paymentTrade.payment_module_id}}">{{ paymentTradeData.module.name }}</router-link>
                            <template v-else>{{ paymentTradeData.paymentTrade.basic_payment_module }}</template>
                        </column>
                        <column name="状态" :inline="true" :dt-style="dtStyle">
                            <status-select :is-submitting="isChangingStatus" :value="paymentTradeData.paymentTrade.status" v-on:input="changeStatus"></status-select>
                        </column>
                        <div class="column">
                            <div class="ui four column grid">
                                <column name="创建时间" :inline="true" :dt-style="dtStyle"><local-time :time="paymentTradeData.paymentTrade.created_at"></local-time></column>
                                <column name="支付时间" :inline="true" :dt-style="dtStyle"><local-time :time="paymentTradeData.paymentTrade.paid_at"></local-time></column>
                            </div>
                        </div>
                        <div class="column">
                            <div class="ui four column grid">
                                <column name="金额" :inline="true" :dt-style="dtStyle"><amount :amount="paymentTradeData.paymentTrade.fee_in_default_currency"></amount></column>
                                <column name="可退款金额" :inline="true" :dt-style="dtStyle"><amount :amount="paymentTradeData.paymentTrade.refundable_fee"></amount></column>
                                <column name="已退款金额" :inline="true" :dt-style="dtStyle"><amount :amount="paymentTradeData.totalRefundedFee"></amount></column>
                                <column name="退款中金额" :inline="true" :dt-style="dtStyle"><amount :amount="paymentTradeData.totalRefundingFee"></amount></column>
                            </div>
                        </div>
                    </div>

                    <div class="ui section divider"></div>

                    <div class="ui one column grid">
                        <column v-if="paymentTradeData.paymentTrade.status === 1" name="退款" :dt-style="dtStyle" :dd-style="{marginLeft: '100px'}">
                            <form class="ui form" v-on:submit.prevent="refund">
                                <div class="ui two fields">
                                    <div class="ui field">
                                        <label class="required" style="width: 80px;">退款方式</label>
                                        <refund-type-select v-model="refundForm.type"></refund-type-select>
                                    </div>
                                    <slide-fade-transition>
                                        <div v-if="refundForm.type === '1'" class="ui field">
                                            <label style="width: 80px;">退款交易号</label>
                                            <input type="text" v-model="refundForm.transactionId">
                                        </div>
                                    </slide-fade-transition>
                                </div>
                                <div class="ui two fields">
                                    <div class="ui field">
                                        <label class="required" style="width: 80px;">退款金额</label>
                                        <div class="ui small labeled action input">
                                            <div class="ui label">
                                                {{ $store.getters.defaultCurrency.prefix }}
                                            </div>
                                            <input style="min-width: 150px !important;" type="number" step="0.01" min="0.01" :max="paymentTradeData.paymentTrade.refundable_fee" v-model="refundForm.fee" required>
                                            <button type="submit" class="ui small red button" :disabled="!refundable || isLoading">
                                                发放退款
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </column>
                        <column v-if="paymentTradeData.paymentTrade.status === 1" name="回滚入账" :inline="true" :dt-style="dtStyle">
                            <button class="ui tiny button" v-on:click="removeTransaction">取消订单并从除用户余额中扣除本订单的金额</button>
                        </column>
                        <column v-if="paymentTradeData.paymentTrade.status === 0" name="入账" :inline="true" :dt-style="dtStyle">
                            <form class="ui form" v-on:submit.prevent="addTransaction">
                                <div class="ui small right labeled action input">
                                    <input type="text" placeholder="交易号" v-model="addTransactionForm.transactionId">
                                    <add-transaction-type-select v-model="addTransactionForm.type"></add-transaction-type-select>
                                    <button type="submit" class="ui small button" :disabled="isLoading">
                                        提交
                                    </button>
                                </div>
                            </form>
                        </column>
                    </div>
                </div>
            </div>
        </template>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <h3 class="ui header">退款订单</h3>
                <table class="ui unstackable very basic table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>订单号</th>
                        <th>交易号</th>
                        <th>金额</th>
                        <th>申请于</th>
                        <th>退款结果</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody v-if="paymentTradeData.refunds.length">
                    <tr v-for="refundRecord in paymentTradeData.refunds">
                        <td>{{ refundRecord.id }}</td>
                        <td>{{ refundRecord.no }}</td>
                        <td>{{ refundRecord.transaction_id }}</td>
                        <td><amount :amount="refundRecord.fee_in_default_currency"></amount></td>
                        <td><local-time :time="refundRecord.created_at"></local-time></td>
                        <td>
                            <refund-status :value="refundRecord.status" v-on:input="(newStatus) => { changeRefundStatus(refundRecord, newStatus); }"></refund-status>
                        </td>
                        <td>
                            <semantic-ui-dropdown-menu v-if="refundRecord.status === 0 || refundRecord.status === 3 || refundRecord.status === 0" :disabled="isLoading" text="操作">
                                <div v-if="refundRecord.status === 0 || refundRecord.status === 3" class="item" v-on:click="rollbackRefund(refundRecord)"><i class="undo icon"></i> 取消并回滚</div>
                                <div v-if="refundRecord.status === 0" class="item" v-on:click="commitRefund(refundRecord)"><i class="check icon"></i> 执行完成操作</div>
                            </semantic-ui-dropdown-menu>
                        </td>
                    </tr>
                    </tbody>
                    <tbody v-else>
                    <tr>
                        <td colspan="7" style="text-align: center">暂无记录</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "../../ClientArea/ComputeInstance/Show/Information/Column";
    import Amount from "../../CreditRecord/Amount";
    import LocalTime from "../../LocalTime";
    import StatusLabel from "../../PaymentTrade/StatusLabel";
    import RefundTypeSelect from "./RefundTypeSelect";
    import AddTransactionTypeSelect from "./AddTransactionTypeSelect";
    import StatusSelect from "./StatusSelect";
    import RefundStatus from "./RefundStatus";
    export default {
        name: "Show",
        components: {
            RefundStatus,
            StatusSelect, AddTransactionTypeSelect, RefundTypeSelect, StatusLabel, LocalTime, Amount, Column},
        data: function () {
            return {
                dtStyle: {
                    width: "90px",
                    textAlign: "right",
                },
                isLoading: false,
                isChangingStatus: false,
                paymentTradeData: null,
                refundForm: {
                    type: "0",
                    transactionId: "",
                    fee: 0,
                },
                addTransactionForm: {
                    transactionId: "",
                    type: "0",
                },
            };
        },
        created: function () {
            this.load();
        },
        mounted: function () {
        },
        methods: {
            load: function () {
                this.$axiosGet(route("admin.paymentTrades.show", [this.paymentTradeId]), (data) => {
                    this.retrieveData(data);
                });
            },
            retrieveData: function (data) {
                this.paymentTradeData = data.data;
            },
            changeStatus: function (newStatus) {
                if (newStatus === this.paymentTradeData.paymentTrade.status)
                    return;
                this.confirmModal()
                    .withHeader("提示")
                    .withTextContent("确定更改订单的状态？通过此处更改订单状态不会完成其它相关操作（如增加或扣除用户余额），如非必要，请不要通过此处更改订单状态。")
                    .withOnApprove(() => {
                        this.isChangingStatus = true;
                        this.$axiosPatch(route("admin.paymentTrades.changeStatus", [this.paymentTradeId]), {status: newStatus}, (data) => {
                            this.retrieveData(data);
                            this.positiveFloatingMessage("状态更改成功");
                        }, () => {
                            this.isChangingStatus = false;
                        });
                    })
                    .show()
                ;
            },
            refund: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withTextContent("确定发放退款？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        this.$axiosPost(route("admin.paymentTrades.refund", [this.paymentTradeId]), this.refundForm, (data) => {
                            this.retrieveData(data);
                            if (data.status === 1) {
                                this.positiveFloatingMessage("退款成功");
                            } else {
                                this.negativeFloatingMessage("退款结果未知，请手动更改退款记录状态");
                            }
                        }, () => {
                            this.isLoading = false;
                        });
                    })
                    .show()
                ;
            },
            addTransaction: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withTextContent("确定入账？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        this.$axiosPost(route("admin.paymentTrades.addTransaction", [this.paymentTradeId]), this.addTransactionForm, (data) => {
                            this.retrieveData(data);
                            this.positiveFloatingMessage("成功");
                        }, () => {
                            this.isLoading = false;
                        })
                    })
                    .show()
                ;
            },
            removeTransaction: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withTextContent("确定回滚入账操作？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        this.$axiosDelete(route("admin.paymentTrades.removeTransaction", [this.paymentTradeId]), (data) => {
                            this.retrieveData(data);
                            this.positiveFloatingMessage("成功");
                        }, () => {
                            this.isLoading = false;
                        })
                    })
                    .show()
                ;
            },
            rollbackRefund: function (refundRecord) {
                this.confirmModal()
                    .withHeader("提示")
                    .withContent("确定取消退款？<br><br>警告：系统仅能更改此退款订单的状态，并修正订单可退款金额、用户余额及其冻结余额，<span style='color: red;'>对于已通过付款平台发放的退款，系统无法收回！</span>")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        this.$axiosPatch(route("admin.paymentTradeRefunds.cancel", [refundRecord.id]), null, (data) => {
                            this.retrieveData(data);
                            this.positiveFloatingMessage("成功");
                        }, () => {
                            this.isLoading = false;
                        })
                    })
                    .show()
                ;
            },
            commitRefund: function (refundRecord) {
                this.confirmModal()
                    .withHeader("提示")
                    .withTextContent("确定为退款订单["+ refundRecord.no +"]执行完成操作？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        this.$axiosPatch(route("admin.paymentTradeRefunds.commit", [refundRecord.id]), null, (data) => {
                            this.retrieveData(data);
                            this.positiveFloatingMessage("成功");
                        }, () => {
                            this.isLoading = false;
                        })
                    })
                    .show()
                ;
            },
            changeRefundStatus: function (refundRecord, newStatus) {
                if (refundRecord.status === newStatus)
                    return;
                if (refundRecord.status !== 1 && refundRecord.status !== 2 || newStatus !== 1 && newStatus !== 2) {
                    this.negativeFloatingMessage("订单状态仅允许在[已取消]与[失败]之间切换");
                    return;
                }
                this.confirmModal()
                    .withHeader("提示")
                    .withTextContent("确定直接更改退款的状态？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        this.$axiosPatch(route("admin.paymentTradeRefunds.changeStatus", [refundRecord.id]), {status: newStatus}, (data) => {
                            this.retrieveData(data);
                            this.positiveFloatingMessage("成功");
                        }, () => {
                            this.isLoading = false;
                        })
                    })
                    .show()
                ;
            }
        },
        computed: {
            paymentTradeId: function () {
                return this.$router.currentRoute.params.id;
            },
            refundable: function () {
                var trade = this.paymentTradeData.paymentTrade;
                if (trade.status !== 1)
                    return false;
                try {
                    var refundableFee = new Decimal(trade.refundable_fee);
                    var inputFee = new Decimal(this.refundForm.fee);
                } catch (e) {
                    return false;
                }
                return !(refundableFee.equals(0) || inputFee.greaterThan(refundableFee));
            }
        }
    }
</script>

<style scoped>

</style>