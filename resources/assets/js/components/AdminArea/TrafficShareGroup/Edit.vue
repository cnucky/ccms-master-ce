<template>
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
                    v-on:submit="update"
            ></traffic-share-group-form>
        </div>
    </div>
</template>

<script>
    import TrafficShareGroupForm from "./TrafficShareGroupForm";
    export default {
        name: "Edit",
        components: {TrafficShareGroupForm},
        props: ["trafficShareGroup"],
        data: function () {
            return {
                isSubmitting: false,
                trafficShareGroupForm: _.cloneDeep(this.trafficShareGroup),
            };
        },
        methods: {
            update: function () {
                this.isSubmitting = true;
                this.$axiosPatch(route("trafficShareGroups.update", this.trafficShareGroup.id), this.trafficShareGroupForm, (data) => {
                    this.$emit("retrieveData", data);
                    this.positiveFloatingMessage("保存成功");
                }, () => {
                    this.isSubmitting = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>