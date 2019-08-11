<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui field">
            <label>名称</label>
            <input type="text" :value="name" v-on:input="$emit('name', $event.target.value)">
        </div>

        <div class="ui section divider"></div>

        <div class="ui field">
            <label>权限</label>
        </div>

        <div class="ui four columns grid">
            <div v-for="(permissionName, permissionId) in availablePermissions" class="column">
                <div class="ui checkbox permission">
                    <input type="checkbox" v-model="grantedPermissions[permissionId]">
                    <label>{{ $t("adminPermission." + permissionName) }}</label>
                </div>
            </div>
        </div>

        <div style="text-align: right; margin-top: 20px;"><a href="#" v-on:click.prevent="checkAll">全选</a>&nbsp;|&nbsp;<a v-on:click.prevent="uncheckAll" href="#">全取消</a></div>

        <div class="ui field" style="margin-top: 20px;">
            <button type="submit" class="ui small teal fluid button" v-bind:class="{loading: isSubmitting}" :disabled="isSubmitting">提交</button>
        </div>
    </form>
</template>

<script>
    export default {
        name: "RoleForm",
        props: [
            "isSubmitting",
            "name",
            "grantedPermissions",
            "availablePermissions",
        ],
        mounted: function () {
            $(".ui.checkbox.permission").checkbox();
        },
        methods: {
            checkAll: function () {
                for (var i in this.availablePermissions) {
                    this.$set(this.grantedPermissions, i, true);
                }
            },
            uncheckAll: function () {
                for (var i in this.availablePermissions) {
                    this.$set(this.grantedPermissions, i, false);
                }
            }
        }
    }
</script>

<style scoped>

</style>