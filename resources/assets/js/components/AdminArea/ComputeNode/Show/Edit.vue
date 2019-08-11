<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">编辑</h3>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <compute-node-form ref="computeNodeForm" :is-submitting="isSubmitting" v-on:submit="update" :compute-node2-edit="temporaryComputeNode"
                                   :available-regions="availableRegions" :available-zones="availableZones"
                                   v-if="!isLoading" :is-editing="true" :show-submit-button="true"></compute-node-form>
            </div>
        </div>
    </div>
</template>

<script>
    import ComputeNodeForm from "../ComputeNodeForm";
    import CertificatePromote from "./../CertificatePromote";

    export default {
        name: "Edit",
        mixins: [CertificatePromote],
        components: {ComputeNodeForm},
        props: ["computeNode", "trustedCertificateInformation", "clientCertificateInformation", "serverTime", "nodeStatus"],
        data: function () {
            return {
                isLoading: true,
                isSubmitting: false,
                temporaryComputeNode: _.cloneDeep(this.computeNode),
                availableRegions: [],
                availableZones: [],
            };
        },
        created: function () {
            axios.get(route("computeNodes.listRegions"), {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.availableRegions = data.availableRegions;
                        this.availableZones = data.availableZones;
                    } else {
                        this.$globalErrnoHandler(data);
                    }
                })
                .catch(this.$axiosCatchError2Console)
                .then(() => {
                    this.isLoading = false;
                })
            ;
        },
        methods: {
            update: function () {
                this.isSubmitting = true;
                axios.patch(route("computeNodes.update", [this.computeNode.id]), this.$refs.computeNodeForm.temporaryItem, {vueInstance: this})
                    .then((response) => {
                        if (response.data.result) {
                            this.positiveFloatingMessage("保存成功");
                            this.$emit("item-updated", response.data.item);
                        } else if (this.isNeedPromoteForTrust(response.data)) {
                            this.promoteForTrust(response.data);
                        } else {
                            this.$globalErrnoHandler(response.data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmitting = false;
                    })

                ;
            }
        }
    }
</script>

<style scoped>

</style>