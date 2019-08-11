<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">用户配额</h1>
        </div>

        <div class="sixteen wide column">
            <model-index-refresh-button v-on:click="load"></model-index-refresh-button>
            <model-index-create-button v-on:click="$router.push({name: 'userQuotas.create'})"></model-index-create-button>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader :is-active="isLoading"></semantic-ui-loader>
            <table class="ui unstackable table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>名称</th>
                    <th>描述</th>
                    <th class="three wide column"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="userQuota in userQuotas">
                    <td>{{ userQuota.id }}</td>
                    <td>{{ userQuota.name }}</td>
                    <long-text-column :entry="userQuota" key-name="description"></long-text-column>
                    <td>
                        <router-link :to="{name: 'userQuotas.edit', params: {id: userQuota.id}}" class="ui tiny button"><i class="edit icon"></i> 详情</router-link>
                        <a v-if="userQuota.id !== 1" class="ui tiny red button" href="#" v-on:click.prevent="destroy(userQuota)"><i class="trash icon"></i> 删除</a>
                        <label v-else class="ui label">默认</label>
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
                userQuotas: [],
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.isLoading = true;
                this.$axiosGet(route("userQuotas.index"), (data) => {
                    this.userQuotas = data.userQuotas;
                }, () => {
                    this.isLoading = false;
                })
            },
            destroy: function (userQuota) {
                this.confirmModal()
                    .withHeader("提示")
                    .withTextContent("确定删除用户配额["+ userQuota.name +"]？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        this.$axiosDelete(route("userQuotas.destroy", [userQuota.id]), () => {
                            for (var i in this.userQuotas) {
                                if (this.userQuotas[i].id === userQuota.id) {
                                    this.userQuotas.splice(i, 1);
                                    break;
                                }
                            }
                            this.positiveFloatingMessage("删除成功");
                        }, () => {
                            this.isLoading = false;
                        })
                    })
                    .show()
                ;
            }
        }
    }
</script>

<style scoped>

</style>