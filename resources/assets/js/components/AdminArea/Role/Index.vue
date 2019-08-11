<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">
                角色管理
            </h1>
        </div>

        <div class="sixteen wide column">
            <model-index-refresh-button :is-loading="isLoading" v-on:click="loadRoles"></model-index-refresh-button>
            <model-index-create-button model-name="角色" v-on:click="$router.push({name: 'admin.roles.create'})"></model-index-create-button>
        </div>

        <div class="sixteen wide column">
            <table class="ui unstackable table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>名称</th>
                    <th>管理员数量</th>
                    <th class="three wide column"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="role in roles">
                    <td>{{ role.id }}</td>
                    <td>{{ role.name }}</td>
                    <td>{{ role.admins_count }}</td>
                    <td>
                        <router-link class="ui tiny button" :to="{name: 'admin.roles.edit', params: {id: role.id}}"><i class="edit icon"></i> 详情</router-link>
                        <a class="ui tiny red button" href="#" v-on:click.prevent="destroy(role)"><i class="trash icon"></i> 删除</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Index",
        data: function () {
            return {
                isLoading: false,
                roles: [],
            };
        },
        created: function () {
            this.loadRoles();
        },
        methods: {
            loadRoles: function () {
                this.isLoading = true;
                this.$axiosGet(route("admin.roles.index"), (data) => {
                    this.roles = data.roles;
                }, () => {
                    this.isLoading = false;
                })
            },
            destroy: function (role) {
                this.confirmModal()
                    .withHeader("提示")
                    .withTextContent("确定删除角色["+ role.name +"]？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        this.$axiosDelete(route("admin.roles.destroy", [role.id]), (data) => {
                            for (var i in this.roles) {
                                if (this.roles[i].id === role.id) {
                                    this.roles.splice(i, 1);
                                    break;
                                }
                            }
                            this.positiveFloatingMessage("角色删除成功");
                        }, () => {
                            this.isLoading = false;
                        });
                    })
                    .show()
                ;
            }
        }
    }
</script>

<style scoped>

</style>