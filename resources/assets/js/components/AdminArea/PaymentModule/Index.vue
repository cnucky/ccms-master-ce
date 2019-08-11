<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">
                模块配置列表
            </h1>
        </div>

        <div class="sixteen wide column">
            <refresh-button v-on:click="load"></refresh-button>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader :is-active="isLoading"></semantic-ui-loader>
            <table class="ui unstackable table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>基础模块</th>
                    <th>显示名称</th>
                    <th>显示顺序</th>
                    <th>创建于</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(paymentModule, index) in paymentModules">
                    <td>{{ paymentModule.id }}</td>
                    <td>
                        <template v-if="typeof basicPaymentModules === 'object' && basicPaymentModules.hasOwnProperty(paymentModule.basic_payment_module)">
                            {{ basicPaymentModules[paymentModule.basic_payment_module].internalName }} - {{ basicPaymentModules[paymentModule.basic_payment_module].name }}
                        </template>
                        <template v-else>
                            <span style="color: red;"><i class="exclamation icon"></i> 丢失</span>
                        </template>
                    </td>
                    <td>{{ paymentModule.name }}</td>
                    <td>{{ paymentModule.order }}</td>
                    <duration-column :entry="paymentModule" key-name="created_at"></duration-column>
                    <td class="three two wide column">
                        <router-link class="ui tiny button" :to="{name: 'paymentModules.edit', params: {id: paymentModule.id}}"><i class="edit icon"></i> 详情</router-link>
                        <button class="ui tiny red button" v-on:click="confirmDestroy(paymentModule, index)"><i class="trash icon"></i> 删除</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import RefreshButton from "../../ModelIndex/RefreshButton";
    export default {
        name: "Index",
        components: {RefreshButton},
        data: function () {
            return {
                isLoading: false,
                basicPaymentModules: {},
                paymentModules: [],
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.isLoading = true;
                axios.get(route("paymentModules.index"), {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.basicPaymentModules = data.basicPaymentModules;
                            this.paymentModules = data.paymentModules;
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
            confirmDestroy: function (paymentModule, index) {
                this.confirmModal()
                    .init()
                    .withHeader("提示")
                    .withContent("确定删除模块配置#"+ paymentModule.id +"？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.delete(route("paymentModules.destroy", [paymentModule.id]), {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.paymentModules.splice(index, 1);
                                    this.positiveFloatingMessage("删除成功");
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