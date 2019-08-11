<template>
    <div class="ui very padded no-shadow segment">
        <user-quota-form :is-submitting="isSubmitting" :user-quota="temporaryUserQuota" v-on:submit="update"></user-quota-form>
    </div>
</template>

<script>
    import UserQuotaForm from "./UserQuotaForm";
    export default {
        name: "Edit",
        components: {UserQuotaForm},
        props: ["userQuotaData"],
        data: function () {
            return {
                isSubmitting: false,
                temporaryUserQuota: _.cloneDeep(this.userQuotaData.userQuota),
            };
        },
        methods: {
            update: function () {
                this.isSubmitting = true;
                this.$axiosPatch(route("userQuotas.update", [this.userQuotaData.userQuota.id]), this.temporaryUserQuota, (data) => {
                    this.$emit("retrieveData", data);
                    this.positiveFloatingMessage("保存成功");
                }, () => {
                    this.isSubmitting = false;
                })
            }
        }
    }
</script>

<style scoped>

</style>