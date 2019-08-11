<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <template v-if="tradeStatisticsData">
                    <month-input v-if="useMonthInput" v-on:query="load"></month-input>
                    <date-input v-else v-on:query="load"></date-input>
                    <div class="ui section divider"></div>

                    <trade-chart :date-list="dateList" :total-paid="tradeStatisticsData.totalPaid" :total-refunded="tradeStatisticsData.totalRefunded" :daily-net-incoming="netIncomingKeyByDate" :height="150"></trade-chart>

                    <div class="ui section divider"></div>

                    <table class="ui very basic table">
                        <thead>
                        <tr>
                            <th>时间</th>
                            <th>充值</th>
                            <th>退款</th>
                            <th>净收入</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="date in dateList">
                            <td>{{ date }}</td>
                            <td><amount :amount="tradeStatisticsData.totalPaid.hasOwnProperty(date) ? tradeStatisticsData.totalPaid[date] : '0'"></amount></td>
                            <td><amount :amount="tradeStatisticsData.totalRefunded.hasOwnProperty(date) ? tradeStatisticsData.totalRefunded[date] : '0'" force-color="red"></amount></td>
                            <td><amount :amount="netIncomingKeyByDate[date].toString()"></amount></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><b>总计</b></th>
                            <th><amount :amount="totalPaid"></amount></th>
                            <th><amount :amount="totalRefunded" force-color="red"></amount></th>
                            <th><amount :amount="totalNetIncoming"></amount></th>
                        </tr>
                        </tfoot>
                    </table>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import DateInput from "./DateInput";
    import Amount from "../../../CreditRecord/Amount";
    import TradeChart from "../Chart/TradeChart";
    import MonthInput from "./MonthInput";
    export default {
        name: "Trade",
        components: {MonthInput, TradeChart, Amount, DateInput},
        data: function () {
            return {
                isLoading: false,
                tradeStatisticsData: null,
                routeName: "",
                useMonthInput: false,
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function (start = "", end = "") {
                this.tradeStatisticsData = null;
                this.isLoading = true;
                this.$axiosGet(route(this.routeName, {start: start, end: end}), (data) => {
                    this.tradeStatisticsData = data;
                }, () => {
                    this.isLoading = false;
                });
            },
            netIncoming: function (date) {
                var paid = this.tradeStatisticsData.totalPaid.hasOwnProperty(date) ? this.tradeStatisticsData.totalPaid[date] : "0";
                var refunded = this.tradeStatisticsData.totalRefunded.hasOwnProperty(date) ? this.tradeStatisticsData.totalRefunded[date] : "0";
                return new Decimal(paid).sub(refunded);
            }
        },
        computed: {
            dateList: function () {
                var dateList = {};
                var i;
                for (i in this.tradeStatisticsData.totalPaid) {
                    dateList[this.$moment(i).unix()] = i;
                }
                for (i in this.tradeStatisticsData.totalRefunded) {
                    dateList[this.$moment(i).unix()] = i;
                }
                return dateList;
            },
            totalPaid: function () {
                var total = new Decimal("0.00");
                for (var i in this.tradeStatisticsData.totalPaid) {
                    total = total.add(this.tradeStatisticsData.totalPaid[i]);
                }
                return total.toString();
            },
            totalRefunded: function () {
                var total = new Decimal("0.00");
                for (var i in this.tradeStatisticsData.totalRefunded) {
                    total = total.add(this.tradeStatisticsData.totalRefunded[i]);
                }
                return total.toString();
            },
            netIncomingKeyByDate: function () {
                var daily = {};
                for (var i in this.dateList) {
                    daily[this.dateList[i]] = this.netIncoming(this.dateList[i]);
                }
                return daily;
            },
            totalNetIncoming: function () {
                var total = new Decimal("0.00");
                for (var i in this.dateList) {
                    total = total.add(this.netIncomingKeyByDate[this.dateList[i]]);
                }
                return total.toString();
            }
        }
    }
</script>

<style scoped>

</style>