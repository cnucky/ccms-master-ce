<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <div v-if="consumption" class="ui one column grid">
                    <column name="历史消费总额" :inline="true"><amount :amount="consumption.total_consumption"></amount></column>
                </div>
                <table v-if="consumption" class="ui very basic table">
                    <tbody>
                    <tr v-for="consumptionRecord in consumption.total_consumption_group_by_type">
                        <type-column :entry="consumptionRecord" key-name="type"></type-column>
                        <amount-column :entry="consumptionRecord" key-name="total_amount"></amount-column>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import Column from "../../../../ClientArea/ComputeInstance/Show/Information/Column";
    import Amount from "../../../../CreditRecord/Amount";
    import TypeColumn from "../../../../CreditRecord/TypeColumn";
    import AmountColumn from "../../../../CreditRecord/AmountColumn";
    export default {
        name: "Dashboard",
        components: {AmountColumn, TypeColumn, Amount, Column},
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
                this.$axiosGet(route("users.consumption", [this.userData.user.id]), (data) => {
                    this.consumption = data.data;
                }, () => {
                    this.isLoading = false;
                });
            }
        },
    }
</script>

<style scoped>

</style>