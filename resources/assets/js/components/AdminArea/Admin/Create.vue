<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">添加管理员</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <admin-form
                        :is-submitting="isSubmitting"
                        :require-password="true"
                        :name="temporaryAdminData.admin.name"
                        :email="temporaryAdminData.admin.email"
                        :phone="temporaryAdminData.admin.phone"
                        :status="temporaryAdminData.admin.status"
                        :role-id="temporaryAdminData.admin.assigned_role_id"
                        :password="temporaryAdminData.admin.password"
                        :password-confirmation="temporaryAdminData.admin.password_confirmation"
                        :available-roles="availableRoles"
                        v-on:name="(value) => { temporaryAdminData.admin.name = value }"
                        v-on:email="(value) => { temporaryAdminData.admin.email = value }"
                        v-on:phone="(value) => { temporaryAdminData.admin.phone = value }"
                        v-on:status="(value) => { temporaryAdminData.admin.status = value }"
                        v-on:roleId="(value) => { temporaryAdminData.admin.assigned_role_id = value }"
                        v-on:password="(value) => { temporaryAdminData.admin.password = value }"
                        v-on:passwordConfirmation="(value) => { temporaryAdminData.admin.password_confirmation = value }"
                        v-on:submit="store"
                ></admin-form>
            </div>
        </div>
    </div>
</template>

<script>
    import AdminForm from "./AdminForm";
    export default {
        name: "Create",
        components: {AdminForm},
        data: function () {
            return {
                isSubmitting: false,
                isLoadingAvailableRoles: false,
                temporaryAdminData: {
                    admin: {
                        status: 1,
                    },
                },
                availableRoles: [],
            };
        },
        created: function () {
            this.isLoadingAvailableRoles = true;
            this.$axiosGet(route("admin.availableRoles"), (data) => {
                this.availableRoles = data.availableRoles;
            }, () => {
                this.isLoadingAvailableRoles = false;
            })
        },
        methods: {
            store: function () {
                this.isSubmitting = true;
                this.$axiosPost(route("admins.store"), this.temporaryAdminData.admin, (data) => {
                    this.positiveFloatingMessage("管理员添加成功");
                    this.$router.push({name: "admins.edit", params: {id: data.admin.id}});
                }, () => {
                    this.isSubmitting = false;
                })
            }
        }
    }
</script>

<style scoped>

</style>