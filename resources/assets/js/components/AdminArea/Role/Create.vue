<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">创建角色</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoadingAvailablePermissions}">
                <role-form :is-submitting="isSubmitting" :name="temporaryRoleData.name" :granted-permissions="temporaryRoleData.grantedPermissions" :available-permissions="availablePermissions" v-on:name="(value) => { temporaryRoleData.name = value; }" v-on:submit="store"></role-form>
            </div>
        </div>
    </div>
</template>

<script>
    import RoleForm from "./RoleForm";
    export default {
        name: "Create",
        components: {RoleForm},
        data: function () {
            return {
                isSubmitting: false,
                isLoadingAvailablePermissions: false,
                temporaryRoleData: {
                    name: "",
                    grantedPermissions: {},
                },
                availablePermissions: [],
            };
        },
        created: function () {
            this.isLoadingAvailablePermissions = true;
            this.$axiosGet(route("admin.availablePermissions"), (data) => {
                this.availablePermissions = data.availablePermissions;
            }, () => {
                this.isLoadingAvailablePermissions = false;
            })
        },
        methods: {
            store: function () {
                this.isSubmitting = true;
                this.$axiosPost(route("admin.roles.store"), this.temporaryRoleData, (data) => {
                    this.$router.push({name: "admin.roles.edit", params: {id: data.role.id}});
                    this.positiveFloatingMessage("角色创建成功");
                }, () => {
                    this.isSubmitting = false;
                })
            }
        }
    }
</script>

<style scoped>

</style>