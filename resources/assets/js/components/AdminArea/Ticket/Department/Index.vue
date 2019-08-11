<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1>工单部门管理</h1>
        </div>

        <div class="sixteen wide column">
            <model-index-refresh-button v-on:click="load"></model-index-refresh-button>
            <model-index-create-button model-name="部门" v-on:click="$router.push({name: 'ticketDepartments.create'})"></model-index-create-button>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader :is-active="isLoading"></semantic-ui-loader>
            <table class="ui unstackable table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>名称</th>
                    <th>描述</th>
                    <th>显示</th>
                    <th>相关服务</th>
                    <th class="three wide column"></th>
                </tr>
                </thead>
                <tbody>
                <template v-if="departments.length">
                    <tr v-for="(department, index) in departments">
                        <td>{{ department.id }}</td>
                        <td>{{ department.name }}</td>
                        <td>{{ department.description }}</td>
                        <td>{{ department.status ? '是' : '否' }}</td>
                        <td>{{ department.show_relative_service_select ? '是' : '否' }}</td>
                        <td>
                            <router-link class="ui tiny button" :to="{name: 'ticketDepartments.edit', params: {id: department.id}}"><i class="edit icon"></i> 编辑</router-link>
                            <button class="ui tiny red button" v-on:click="destroy(department, index)">删除</button>
                        </td>
                    </tr>
                </template>
                <tr v-else>
                    <td colspan="6" style="text-align: center;">无记录</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Department",
        data: function () {
            return {
                isLoading: false,
                departments: [],
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.isLoading = true;
                axios.get(route("ticketDepartments.index"))
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.departments = data.departments;
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            },
            destroy: function (department, index) {
                this.confirmModal().init()
                    .withHeader("提示")
                    .withContent("确定删除所选的部门#"+ department.id +"？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.delete(route("ticketDepartments.destroy", [department.id]), {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.departments.splice(index, 1);
                                    this.positiveFloatingMessage("工单部门删除成功");
                                } else {
                                    this.$globalErrnoHandler(data);
                                }
                            })
                            .catch(this.$axiosCatchError2Console)
                            .then(() => {
                                this.isLoading = false;
                            })
                        ;
                    })
                    .show()
                ;
            }
        }
    }
</script>

<style scoped>

</style>