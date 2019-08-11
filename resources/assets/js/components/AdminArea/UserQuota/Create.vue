<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">添加用户配额</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <user-quota-form :is-submitting="isSubmitting" :user-quota="userQuota" v-on:submit="store"></user-quota-form>
            </div>
        </div>
    </div>
</template>

<script>
    import UserQuotaForm from "./UserQuotaForm";
    export default {
        name: "Create",
        components: {UserQuotaForm},
        data: function () {
            return {
                isSubmitting: false,
                userQuota: {},
            };
        },
        methods: {
            store: function () {
                this.isSubmitting = true;
                this.$axiosPost(route("userQuotas.store"), this.userQuota, (data) => {
                    this.$router.push({name: "userQuotas.edit", params: {id: data.userQuota.id}});
                    this.positiveFloatingMessage("用户配额创建成功");
                }, () => {
                    this.isSubmitting = false;
                })
            }
        }
    }
</script>

<style scoped>

</style>