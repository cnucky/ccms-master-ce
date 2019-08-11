<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">
                {{ $t('common.availablePaymentModule') }}
            </h1>
        </div>
        <div class="sixteen wide column">
            <semantic-ui-loader v-if="isLoading" :is-active="isLoading"></semantic-ui-loader>
            <table class="ui unstackable striped table">
                <thead>
                <tr>
                    <th>内部名称</th>
                    <th>名称</th>
                    <th>版本</th>
                    <th>描述</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="availableModule in availableModules">
                    <td>{{ availableModule.internalName }}</td>
                    <td>{{ availableModule.name }}</td>
                    <td>{{ availableModule.version }}</td>
                    <td>{{ availableModule.description }}</td>
                    <td class="two column wide">
                        <button class="ui green tiny button" v-on:click="showCreatePaymentModuleForm(availableModule)">新建配置</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <form-modal ref="formModal" custom-header="新建模块配置" :is-loading="isSubmitting" :no-stay-select="true" v-on:submit="createPaymentModule">
            <form ref="newPaymentModuleForm" class="ui form" v-on:submit.prevent="createPaymentModule">
                <div class="ui two fields">
                    <div class="ui field" v-if="basicPaymentModule">
                        <label>模块</label>
                        <input type="text" :value="basicPaymentModule.internalName + ' - ' + basicPaymentModule.name" readonly>
                    </div>
                    <div class="ui field">
                        <label>显示名称</label>
                        <input type="text" v-model="paymentModuleForm.name">
                    </div>
                </div>
                <div class="ui field">
                    <label>备注</label>
                    <textarea rows="3" v-model="paymentModuleForm.description"></textarea>
                </div>
                <button class="hidden" type="submit" :disabled="isSubmitting">提交</button>
            </form>
        </form-modal>
    </div>
</template>

<script>
    export default {
        name: "AvailableModule",
        data: function () {
            return {
                isLoading: false,
                isSubmitting: false,
                availableModules: [],

                basicPaymentModule: null,
                paymentModuleForm: {
                    basic_payment_module: "",
                    name: "",
                    description: "",
                },
            };
        },
        created: function () {
            this.isLoading = true;
            axios.get(route("paymentModules.availableModules"), {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.availableModules = data.availableModules;
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
        methods: {
            showCreatePaymentModuleForm: function (basicPaymentModule) {
                this.basicPaymentModule = basicPaymentModule;
                this.paymentModuleForm.basic_payment_module = basicPaymentModule.internalName;
                this.$refs.formModal.show();
            },
            createPaymentModule: function () {
                this.isSubmitting = true;
                axios.post(route("paymentModules.store"), this.paymentModuleForm, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("创建成功");
                            this.$router.push({name: 'paymentModules.edit', params: {id: data.paymentModule.id}});
                            this.$refs.formModal.hide();
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmitting = false;
                    })
                ;
            }
        }
    }
</script>

<style scoped>

</style>