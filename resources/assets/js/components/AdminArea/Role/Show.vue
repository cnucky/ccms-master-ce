<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">角色详情</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui secondary pointing menu">
                <router-link class="item teal" :to="{name: 'admin.roles.edit', params: {id: roleId}}" active-class="active">
                    权限
                </router-link>
                <router-link class="item teal" :to="{name: 'admin.roles.admins.index', params: {id: roleId}}" active-class="active">
                    管理员
                </router-link>
            </div>
        </div>

        <div class="sixteen wide column">
            <slide-fade-transition v-if="roleData">
                <router-view ref="child" :role-data="roleData" v-on:retrieveData="retrieveData"></router-view>
            </slide-fade-transition>
            <semantic-ui-loader v-else :is-active="true"></semantic-ui-loader>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Show",
        data: function () {
            return {
                roleData: null,
            };
        },
        created: function () {
            this.loadRole();
        },
        methods: {
            loadRole: function () {
                this.$axiosGet(route("admin.roles.show", [this.roleId]), (data) => {
                    this.retrieveData(data);
                });
            },
            retrieveData: function (data) {
                this.roleData = data.roleData;
            }
        },
        computed: {
            roleId: function () {
                return this.$router.currentRoute.params.id;
            }
        }
    }
</script>

<style scoped>

</style>