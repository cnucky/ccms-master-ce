<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <admin-form
                        :is-submitting="isSubmitting"
                        :name="temporaryAdminData.admin.name"
                        :email="temporaryAdminData.admin.email"
                        :phone="temporaryAdminData.admin.phone"
                        :status="temporaryAdminData.admin.status"
                        :role-id="temporaryAdminData.admin.assigned_role_id"
                        :password="temporaryAdminData.admin.password"
                        :password-confirmation="temporaryAdminData.admin.password_confirmation"
                        :available-roles="adminData.availableRoles"
                        v-on:name="(value) => { temporaryAdminData.admin.name = value }"
                        v-on:email="(value) => { temporaryAdminData.admin.email = value }"
                        v-on:phone="(value) => { temporaryAdminData.admin.phone = value }"
                        v-on:status="(value) => { temporaryAdminData.admin.status = value }"
                        v-on:roleId="(value) => { temporaryAdminData.admin.assigned_role_id = value }"
                        v-on:password="(value) => { temporaryAdminData.admin.password = value }"
                        v-on:passwordConfirmation="(value) => { temporaryAdminData.admin.password_confirmation = value }"
                        v-on:submit="update"
                ></admin-form>
            </div>
        </div>
    </div>
</template>

<script>
    import AdminForm from "./AdminForm";
    export default {
        name: "Edit",
        components: {AdminForm},
        props: ["adminData"],
        data: function () {
            var admin = _.cloneDeep(this.adminData.admin);
            admin.assigned_role_id = this.adminData.assignedRole ? this.adminData.assignedRole.id : null;
            return {
                isSubmitting: false,
                temporaryAdminData: {
                    admin: admin,
                },
            };
        },
        methods: {
            update: function () {
                this.isSubmitting = true;
                this.$axiosPatch(route("admins.update", [this.adminData.admin.id]), this.temporaryAdminData.admin, (data) => {
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