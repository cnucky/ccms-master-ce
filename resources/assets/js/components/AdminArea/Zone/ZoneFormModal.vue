<template>
    <form-modal ref="modal" class="small" v-on:submit="$refs.form.$refs.submitButton.click()" :model-name="$t('common.zone')" :item-name="itemName" :is-loading="isLoading" :is-editing="isEditing">
        <zone-form ref="form" v-on:submit="submit" :options="options" :is-editing="isEditing"></zone-form>
    </form-modal>
</template>

<script>
    import ZoneForm from "./ZoneForm";
    export default {
        name: "ZoneFormModal",
        components: {ZoneForm},
        props: ["options"],
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
                this.$refs.form.create();
                this.$refs.modal.show();
            },
            edit: function (item) {
                this.isEditing = true;
                this.itemName = item.name;
                this.$refs.form.edit(item);
                this.$refs.modal.show();
            },
            submit: function () {
                this.$refs.modal.init();
                this.isLoading = true;

                var finallyCallback;

                var axiosConfig = {vueInstance: this.$refs.modal};

                if (this.isEditing) {
                    finallyCallback = axios.put(route("zones.update", [this.$refs.form.item.id]), this.$refs.form.temporaryItem, axiosConfig).then((response) => {
                        if (response.data.result) {
                            this.$refs.modal.positiveMessage("保存成功");
                            this.$emit("item-updated", response.data.item);
                            this.$refs.modal.hideIfNotStay();
                        }
                    }).catch((error) => {
                        console.log(error)
                    })
                } else {
                    finallyCallback = axios.post(route("zones.store"), this.$refs.form.temporaryItem, axiosConfig).then((response) => {
                        if (response.data.result) {
                            this.$refs.modal.positiveMessage("保存成功");
                            this.$emit("item-created", response.data.item);
                            this.$refs.form.create();
                            this.$refs.modal.hideIfNotStay();
                        }
                    }).catch((error) => {
                        console.log(error);
                    });
                }

                finallyCallback.then(() => {
                    this.isLoading = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>