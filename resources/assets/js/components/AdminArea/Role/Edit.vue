<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <role-form :is-submitting="isSubmitting" :name="temporaryRoleData.role.name" :granted-permissions="temporaryRoleData.grantedPermissions" :available-permissions="roleData.availablePermissions" v-on:name="(value) => { temporaryRoleData.role.name = value; }" v-on:submit="update"></role-form>
            </div>
        </div>
    </div>
</template>

<script>
    import RoleForm from "./RoleForm";
    export default {
        name: "Edit",
        components: {RoleForm},
        props: ["roleData"],
        data: function () {
            return {
                isSubmitting: false,
                temporaryRoleData: {
                    role: _.cloneDeep(this.roleData.role),
                    grantedPermissions: _.cloneDeep(this.roleData.grantedPermissions),
                }
            };
        },
        methods: {
            update: function () {
                this.isSubmitting = true;
                this.$axiosPatch(route("admin.roles.update", [this.roleData.role.id]), {name: this.temporaryRoleData.role.name, grantedPermissions: this.temporaryRoleData.grantedPermissions}, (data) => {
                    this.$emit("retrieveData", data);
                    this.positiveFloatingMessage("保存成功");
                }, () => {
                    this.isSubmitting = false;
                });
            }
        },
        watch: {
            roleData: function () {
                this.temporaryRoleData = {
                    role: _.cloneDeep(this.roleData.role),
                    grantedPermissions: _.cloneDeep(this.roleData.grantedPermissions),
                };
            }
        }
    }
</script>

<style scoped>

</style>