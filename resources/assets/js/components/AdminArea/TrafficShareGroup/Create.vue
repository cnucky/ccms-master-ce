<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">创建流量共享组</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <traffic-share-group-form
                        :is-submitting="isSubmitting"
                        :name="trafficShareGroupForm.name"
                        :description="trafficShareGroupForm.description"
                        :price-per-rx-gib="trafficShareGroupForm.price_per_rx_gib"
                        :price-per-tx-gib="trafficShareGroupForm.price_per_tx_gib"
                        v-on:name="(value) => {trafficShareGroupForm.name = value}"
                        v-on:description="(value) => {trafficShareGroupForm.description = value}"
                        v-on:pricePerRXGiB="(value) => {trafficShareGroupForm.price_per_rx_gib = value}"
                        v-on:pricePerTXGiB="(value) => {trafficShareGroupForm.price_per_tx_gib = value}"
                        v-on:submit="store"
                ></traffic-share-group-form>
            </div>
        </div>
    </div>
</template>

<script>
    import TrafficShareGroupForm from "./TrafficShareGroupForm";
    export default {
        name: "Create",
        components: {TrafficShareGroupForm},
        data: function () {
            return {
                isSubmitting: false,
                trafficShareGroupForm: {
                    name: "",
                    description: "",
                    price_per_rx_gib: "0",
                    price_per_tx_gib: "0",
                },
            };
        },
        methods: {
            store: function () {
                this.isSubmitting = true;
                this.$axiosPost(route("trafficShareGroups.store"), this.trafficShareGroupForm, (data) => {
                    this.$router.push({name: 'trafficShareGroups.show', params: {id: data.trafficShareGroup.id}})
                    this.positiveFloatingMessage("创建成功");
                }, () => {
                    this.isSubmitting = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>