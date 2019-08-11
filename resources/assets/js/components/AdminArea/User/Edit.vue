<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoadingAvailableUserQuota}">
                <user-form
                        :is-submitting="isSubmitting"
                        :available-user-quotas="userData.availableUserQuotas"
                        :name="temporaryUser.name"
                        :email="temporaryUser.email"
                        :country="temporaryUser.country"
                        :phone="temporaryUser.phone"
                        :status="temporaryUser.status"
                        :disable-quota-auto-upgrade="temporaryUser.disable_quota_auto_upgrade"
                        :user-quota-id="temporaryUser.user_quota_id"
                        :password="temporaryUser.password"
                        v-on:name="(value) => { temporaryUser.name = value }"
                        v-on:email="(value) => { temporaryUser.email = value }"
                        v-on:country="(value) => { temporaryUser.country = value }"
                        v-on:phone="(value) => { temporaryUser.phone = value }"
                        v-on:status="(value) => { temporaryUser.status = value }"
                        v-on:disableQuotaAutoUpgrade="(value) => { temporaryUser.disable_quota_auto_upgrade = value }"
                        v-on:userQuotaId="(value) => { temporaryUser.user_quota_id = value }"
                        v-on:password="(value) => { temporaryUser.password = value }"
                        v-on:submit="update"
                ></user-form>
            </div>
        </div>
    </div>
</template>

<script>
    import UserForm from "./UserForm";
    export default {
        name: "Edit",
        components: {UserForm},
        props: ["userData"],
        data: function () {
            return {
                isSubmitting: false,
                isLoadingAvailableUserQuota: false,
                temporaryUser: _.cloneDeep(this.userData.user),
                // availableUserQuotas: null,
            };
        },
        created: function () {
            /*
            this.isLoadingAvailableUserQuota = true;
            this.$axiosGet(route("userQuotas.index"), (data) => {
                this.availableUserQuotas = data.userQuotas;
            }, () => {
                this.isLoadingAvailableUserQuota = false;
            })
            */
        },
        methods: {
            update: function () {
                this.isSubmitting = true;
                this.$axiosPatch(route("users.update", this.userData.user.id), this.temporaryUser, (data) => {
                    this.$set(this.userData, "user", data.user);
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