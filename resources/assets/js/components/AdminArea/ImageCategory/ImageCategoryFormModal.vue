<template>
    <form-modal ref="modal" class="medium" v-on:submit="$refs.form.$refs.submitButton.click()" :model-name="$t('common.imageCategory')" :item-name="itemName" :is-loading="isLoading" :is-editing="isEditing">
        <image-form ref="form" v-on:submit="submit" :options="options" :is-editing="isEditing"></image-form>
    </form-modal>
</template>

<script>
    import ImageForm from "./ImageCategoryForm";
    export default {
        name: "ImageCategoryImageFormModal",
        components: {ImageForm},
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

                var axiosConfig = {vueInstance: this.$refs.modal, useFloatingMessage: true};

                if (this.isEditing) {
                    finallyCallback = axios.put(route("imageCategories.update", [this.$refs.form.item.id]), this.$refs.form.temporaryItem, axiosConfig).then((response) => {
                        if (response.data.result) {
                            this.$refs.modal.positiveMessage("保存成功");
                            this.$emit("item-updated", response.data.imageCategory);
                            this.$refs.modal.hideIfNotStay();
                        }
                    }).catch((error) => {
                        console.log(error)
                    })
                } else {
                    finallyCallback = axios.post(route("imageCategories.store"), this.$refs.form.temporaryItem, axiosConfig).then((response) => {
                        if (response.data.result) {
                            this.$refs.modal.positiveMessage("保存成功");
                            this.$emit("item-created", response.data.imageCategory);
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