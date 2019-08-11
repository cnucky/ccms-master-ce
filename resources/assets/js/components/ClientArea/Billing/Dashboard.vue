<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">{{ $t("common.billingDashboard") }}</h1>
        </div>

        <div class="sixteen wide column" v-if="$store.getters.user.credit[0] === '-'">
            <div class="ui negative message">
                您的账号当前处于欠费状态，请尽快充值，以免影响您的正常使用。
            </div>
        </div>

        <div class="eight wide column">
            <div class="ui padded no-shadow segment">
                <div class="ui two column grid">
                    <div class="column">
                        <h3 class="ui header">可用余额</h3>
                    </div>
                    <div class="right aligned column">
                        <h3><amount :amount="$store.getters.user.credit"></amount></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="eight wide column">
            <div class="ui padded no-shadow segment">
                <div class="ui two column grid">
                    <div class="column">
                        <h3 class="ui header">冻结余额</h3>
                    </div>
                    <div class="right aligned column">
                        <h3>{{ $store.getters.defaultCurrency.prefix }} {{ $store.getters.user.frozen_credit }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <router-link class="ui tiny green fluid button" :to="{name: 'billing.addCredit'}">充值</router-link>
        </div>

        <div class="sixteen wide column">
            <div class="ui divider"></div>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader :is-active="isLoading"></semantic-ui-loader>
            <h3 class="ui header">近24小时消费情况</h3>
            <table class="ui unstackable fixed table">
                <thead>
                <tr>
                    <th>时间</th>
                    <th>金额</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="recentlyCharge in recentlyChargeHourly">
                    <td>{{ $toLocalMoment(recentlyCharge.time).format("YYYY-MM-DD HH") }}时</td>
                    <td><amount :amount="recentlyCharge.total_amount"></amount></td>
                </tr>
                </tbody>
            </table>

            <h3 class="ui header">近30天消费情况</h3>
            <table class="ui unstackable fixed table">
                <thead>
                <tr>
                    <th>时间</th>
                    <th>金额</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="recentlyCharge in recentlyChargeDaily">
                    <td>{{ recentlyCharge.time }}</td>
                    <td><amount :amount="recentlyCharge.total_amount"></amount></td>
                </tr>
                </tbody>
            </table>

            <h3 class="ui header">近12月消费情况</h3>
            <table class="ui unstackable fixed table">
                <thead>
                <tr>
                    <th>时间</th>
                    <th>金额</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="recentlyCharge in recentlyChargeMonthly">
                    <td>{{ recentlyCharge.time }}</td>
                    <td><amount :amount="recentlyCharge.total_amount"></amount></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import Amount from "../../CreditRecord/Amount";
    export default {
        name: "Dashboard",
        components: {Amount},
        data: function () {
            return {
                isLoading: false,
                recentlyChargeHourly: [],
                recentlyChargeDaily: [],
                recentlyChargeMonthly: [],
            };
        },
        created: function () {
            this.isLoading = true;
            axios.get(route("billing.dashboard"), {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.recentlyChargeHourly = data.recentlyChargeHourly;
                        this.recentlyChargeDaily = data.recentlyChargeDaily;
                        this.recentlyChargeMonthly = data.recentlyChargeMonthly;
                        this.$store.commit("userCredit", data.credit);
                        this.$store.commit("userFrozenCredit", data.frozenCredit);
                    } else {
                        this.$globalErrnoHandler(data);
                    }
                })
                .catch(this.$axiosCatchError2Console)
                .then(() => {
                    this.isLoading = false;
                })
            ;
        }
    }
</script>

<style scoped>

</style>