<template>
    <form-modal ref="modal" v-on:submit="$refs.computeNodeForm.$refs.submitButton.click()" :model-name="$t('common.computeNode')" :item-name="itemName" :is-loading="isLoading" :is-editing="isEditing">
        <compute-node-form ref="computeNodeForm" v-on:submit="submit" :available-regions="availableRegions" :available-zones="availableZones" :is-editing="isEditing"></compute-node-form>
    </form-modal>
</template>

<script>
    import computeNodeForm from "./ComputeNodeForm";
    import CertificatePromote from "./CertificatePromote";
    export default {
        name: "computeNodeFormModal",
        components: {computeNodeForm},
        mixins: [CertificatePromote],
        props: ["availableRegions", "availableZones"],
        data: function () {
            return {
                isEditing: false,
                itemName: "",
                isLoading: false,
            }
        },
        methods: {
            create: function () {
                this.isEditing = false;
                this.$refs.computeNodeForm.create();
                this.$refs.modal.show();
            },
            edit: function (item) {
                this.isEditing = true;
                this.itemName = item.name;
                this.$refs.computeNodeForm.edit(item);
                this.$refs.modal.show();
            },
            submit: function () {
                this.$refs.modal.init();
                this.isLoading = true;

                var finallyCallback;

                var axiosConfig = {vueInstance: this.$refs.modal, useFloatingMessage: true};

                if (this.isEditing) {
                    finallyCallback = axios.put(route("computeNodes.update", [this.$refs.computeNodeForm.computeNode.id]), this.$refs.computeNodeForm.temporaryItem, axiosConfig).then((response) => {
                        if (response.data.result) {
                            this.positiveFloatingMessage("保存成功");
                            this.$emit("item-updated", response.data.item);
                            this.$refs.modal.hideIfNotStay(null);
                        } else if (this.isNeedPromoteForTrust(response.data)) {
                            this.promoteForTrust(response.data);
                        } else {
                            this.$globalErrnoHandler(response.data);
                        }
                    }).catch((error) => {
                        console.log(error)
                    })
                } else {
                    finallyCallback = axios.post(route("computeNodes.store"), this.$refs.computeNodeForm.temporaryItem, axiosConfig).then((response) => {
                        if (response.data.result) {
                            this.positiveFloatingMessage("保存成功");
                            this.$emit("item-created", response.data.item);
                            this.$refs.computeNodeForm.create();
                            this.$refs.modal.hideIfNotStay(null);
                        } else if (this.isNeedPromoteForTrust(response.data)) {
                            this.promoteForTrust(response.data);
                        } else {
                            this.$globalErrnoHandler(response.data);
                        }
                    }).catch((error) => {
                        console.log(error);
                    });
                }

                finallyCallback.then(() => {
                    this.isLoading = false;
                });
            },
        }
    }
</script>

<style scoped>

</style>