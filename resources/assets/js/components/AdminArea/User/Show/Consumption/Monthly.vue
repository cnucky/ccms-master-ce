<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <template v-if="consumption">
                    <h3 class="ui header">总</h3>
                    <consumption-chart :consumption="consumption" :height="150"></consumption-chart>

                    <h3 class="ui header">计算实例</h3>
                    <consumption-chart :consumption="consumption" :height="150" :type="20"></consumption-chart>

                    <h3 class="ui header">本地卷</h3>
                    <consumption-chart :consumption="consumption" :height="150" :type="21"></consumption-chart>

                    <h3 class="ui header">IPv4</h3>
                    <consumption-chart :consumption="consumption" :height="150" :type="22"></consumption-chart>

                    <h3 class="ui header">IPv6</h3>
                    <consumption-chart :consumption="consumption" :height="150" :type="23"></consumption-chart>

                    <h3 class="ui header">上行流量</h3>
                    <consumption-chart :consumption="consumption" :height="150" :type="26"></consumption-chart>

                    <h3 class="ui header">其它</h3>
                    <consumption-chart :consumption="consumption" :height="150" :type="127"></consumption-chart>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import ConsumptionChart from "./Statistics/ConsumptionChart";
    export default {
        name: "Monthly",
        components: {ConsumptionChart},
        props: ["userData"],
        data: function () {
            return {
                isLoading: false,
                consumption: null,
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.isLoading = true;
                this.$axiosGet(route("users.monthlyConsumption", [this.userData.user.id]), (data) => {
                    this.consumption = data.data;
                }, () => {
                    this.isLoading = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>