<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <template v-if="statisticsData">
                    <month-input v-if="useMonthInput" v-on:query="query"></month-input>
                    <date-input v-else v-on:query="query"></date-input>

                    <div class="ui section divider"></div>

                    <charge-chart :date-list="dateList" :statistics-data="statisticsData" :incoming-key-by-date="incomingKeyByDate" :height="150"></charge-chart>

                    <div class="ui section divider"></div>

                    <table class="ui very basic table">
                        <thead>
                        <tr>
                            <th>时间</th>
                            <th>计算实例</th>
                            <th>本地卷</th>
                            <th>弹性IPv4</th>
                            <th>弹性IPv6</th>
                            <th>上行流量</th>
                            <th>总计</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="date in dateList">
                            <td>{{ date }}</td>
                            <td><amount :amount="$propertyValueOrZero(statisticsData.instanceTotalCharged, date)"></amount></td>
                            <td><amount :amount="$propertyValueOrZero(statisticsData.localVolumeTotalCharged, date)"></amount></td>
                            <td><amount :amount="$propertyValueOrZero(statisticsData.elasticIPv4TotalCharged, date)"></amount></td>
                            <td><amount :amount="$propertyValueOrZero(statisticsData.elasticIPv6TotalCharged, date)"></amount></td>
                            <td><amount :amount="$propertyValueOrZero(statisticsData.txTrafficTotalCharged, date)"></amount></td>
                            <td><amount :amount="incomingKeyByDate[date]"></amount></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><b>总计</b></th>
                            <th><amount :amount="instanceTotalCharged"></amount></th>
                            <th><amount :amount="localVolumeTotalCharged"></amount></th>
                            <th><amount :amount="elasticIPv4TotalCharged"></amount></th>
                            <th><amount :amount="elasticIPv6TotalCharged"></amount></th>
                            <th><amount :amount="txTrafficTotalCharged"></amount></th>
                            <th><amount :amount="totalCharged"></amount></th>
                        </tr>
                        </tfoot>
                    </table>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import StatisticsUtils from "./Utils";
    import MonthInput from "./MonthInput";
    import DateInput from "./DateInput";
    import Amount from "../../../CreditRecord/Amount";
    import ChargeChart from "../Chart/ChargeChart";

    export default {
        name: "ChargeStatistics",
        components: {ChargeChart, Amount, DateInput, MonthInput},
        mixins: [StatisticsUtils],
        data: function () {
            return {
                useMonthInput: false,
                isLoading: false,
                statisticsData: null,
            };
        },
        created: function () {
            this.query();
        },
        methods: {
            query: function (start = "", end = "") {
                this.statisticsData = null;
                this.isLoading = true;
                this.$axiosGet(this.$router.currentRoute.fullPath + "?start=" + start + "&end=" + end, (data) => {
                    this.statisticsData = data;
                }, () => {
                    this.isLoading = false;
                })
            }
        },
        computed: {
            dateList: function () {
                return this.$generateDateList([
                    this.statisticsData.instanceTotalCharged,
                    this.statisticsData.localVolumeTotalCharged,
                    this.statisticsData.elasticIPv4TotalCharged,
                    this.statisticsData.elasticIPv6TotalCharged,
                    this.statisticsData.txTrafficTotalCharged,
                ]);
            },
            incomingKeyByDate: function () {
                var incoming = {};
                for (var i in this.dateList) {
                    var date = this.dateList[i];
                    incoming[date] = (new Decimal(this.$propertyValueOrZero(this.statisticsData.instanceTotalCharged, date)))
                        .add(this.$propertyValueOrZero(this.statisticsData.localVolumeTotalCharged, date))
                        .add(this.$propertyValueOrZero(this.statisticsData.elasticIPv4TotalCharged, date))
                        .add(this.$propertyValueOrZero(this.statisticsData.elasticIPv6TotalCharged, date))
                        .add(this.$propertyValueOrZero(this.statisticsData.txTrafficTotalCharged, date))
                        .toString()
                    ;
                }

                return incoming;
            },
            instanceTotalCharged: function () {
                var total = new Decimal("0");
                for (var i in this.statisticsData.instanceTotalCharged) {
                    total = total.add(this.statisticsData.instanceTotalCharged[i]);
                }
                return total.toString();
            },
            localVolumeTotalCharged: function () {
                var total = new Decimal("0");
                for (var i in this.statisticsData.localVolumeTotalCharged) {
                    total = total.add(this.statisticsData.localVolumeTotalCharged[i]);
                }
                return total.toString();
            },
            elasticIPv4TotalCharged: function () {
                var total = new Decimal("0");
                for (var i in this.statisticsData.elasticIPv4TotalCharged) {
                    total = total.add(this.statisticsData.elasticIPv4TotalCharged[i]);
                }
                return total.toString();
            },
            elasticIPv6TotalCharged: function () {
                var total = new Decimal("0");
                for (var i in this.statisticsData.elasticIPv6TotalCharged) {
                    total = total.add(this.statisticsData.elasticIPv6TotalCharged[i]);
                }
                return total.toString();
            },
            txTrafficTotalCharged: function () {
                var total = new Decimal("0");
                for (var i in this.statisticsData.txTrafficTotalCharged) {
                    total = total.add(this.statisticsData.txTrafficTotalCharged[i]);
                }
                return total.toString();
            },
            totalCharged: function () {
                return new Decimal(this.instanceTotalCharged)
                    .add(this.localVolumeTotalCharged)
                    .add(this.elasticIPv4TotalCharged)
                    .add(this.elasticIPv6TotalCharged)
                    .add(this.txTrafficTotalCharged)
                    .toString()
                ;
            }
        }
    }
</script>

<style scoped>

</style>