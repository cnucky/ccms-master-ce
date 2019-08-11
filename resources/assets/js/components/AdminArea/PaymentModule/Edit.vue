<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">模块设置</h1>
        </div>

        <div v-if="initLoading" class="sixteen wide column">
            <div class="ui no-shadow segment" v-bind:class="{loading: initLoading}" style="height: 300px;">
            </div>
        </div>

        <template v-else>
            <div class="sixteen wide column">
                <h3 class="ui header">基础设置</h3>
                <div class="ui very padded no-shadow segment" v-bind:class="{loading: isSubmittingBasicSetting}">
                    <div v-if="basicPaymentModule === null" class="ui negative message">
                        基础模块未找到，请检查基础模块是否已实现所需接口，或已被删除
                    </div>
                    <form class="ui form" v-on:submit.prevent="storeBasicSetting">
                        <div class="ui field">
                            <label>基础模块</label>
                            <input type="text" :value="basicPaymentModule ? (basicPaymentModule.internalName + ' - ' + basicPaymentModule.name) : ''" readonly>
                        </div>

                        <div class="ui two fields">
                            <div class="ui field">
                                <label>显示名称</label>
                                <input type="text" v-model="moduleBasicSettings.name" maxlength="32" required>
                            </div>
                            <div class="ui field">
                                <label>显示优先级（小优先）</label>
                                <input type="number" min="-32768" max="32767" v-model="moduleBasicSettings.order">
                            </div>
                        </div>

                        <div class="ui two fields">
                            <div class="ui field">
                                <label>货币</label>
                                <select class="ui dropdown" v-model="moduleBasicSettings.currency_id">
                                    <option :value="null">自动</option>
                                    <option v-for="availableCurrency in availableCurrencies" :value="availableCurrency.id">{{ availableCurrency.code }}</option>
                                </select>
                            </div>
                            <div class="ui field">
                                <label>状态</label>
                                <select class="ui dropdown" v-model="moduleBasicSettings.status">
                                    <option value="0">关闭</option>
                                    <option value="1">启用</option>
                                </select>
                            </div>
                        </div>

                        <div class="ui field">
                            <label>备注</label>
                            <textarea rows="3" v-model="moduleBasicSettings.description"></textarea>
                        </div>

                        <div class="ui field">
                            <button type="submit" class="ui small teal fluid button" :disabled="isSubmittingBasicSetting">保存</button>
                        </div>
                    </form>
                </div>

                <h3 class="ui header">模块参数</h3>
                <div class="ui very padded no-shadow segment" v-bind:class="{loading: isSubmittingModuleSetting}">
                    <form class="ui form" v-on:submit.prevent="storeModuleSetting">
                        <template v-if="isDedicatedNotifiable">
                            <div class="ui field">
                                <label>固定支付结果异步回调URL</label>
                                <div class="ui fluid action input">
                                    <input type="text" :value="payNotifyURL" readonly>
                                    <button type="button" class="ui teal right labeled icon button" v-on:click="copyTextWithFloatingMessage(payNotifyURL)">
                                        <i class="copy icon"></i>
                                        Copy
                                    </button>
                                </div>
                            </div>
                            <div class="ui field">
                                <label>固定退款结果异步回调URL</label>
                                <div class="ui fluid action input" style="margin-bottom: 30px;">
                                    <input type="text" :value="refundNotifyURL" readonly>
                                    <button type="button" class="ui teal right labeled icon button" v-on:click="copyTextWithFloatingMessage(refundNotifyURL)">
                                        <i class="copy icon"></i>
                                        Copy
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div v-else class="ui field">
                            <label>固定异步回调URL</label>
                            <div class="ui fluid action input" style="margin-bottom: 30px;">
                                <input type="text" :value="notifyURL" readonly>
                                <button type="button" class="ui teal right labeled icon button" v-on:click="copyTextWithFloatingMessage(notifyURL)">
                                    <i class="copy icon"></i>
                                    Copy
                                </button>
                            </div>
                        </div>

                        <div v-for="(availableSetting, internalName) in basicPaymentModule.availableSettings" class="ui field">
                            <label>{{ availableSetting.displayName }}</label>
                            <template v-if="availableSetting.type === 'textarea'">
                                <textarea v-model="moduleSettings[internalName]" :placeholder="availableSetting.plcaeholder"></textarea>
                            </template>
                            <template v-else-if="availableSetting.type === 'password'">
                                <input type="password" v-model="moduleSettings[internalName]" :placeholder="availableSetting.plcaeholder">
                            </template>
                            <template v-else-if="availableSetting.type === 'dropdown'">
                                <select class="ui dropdown" v-model="moduleSettings[internalName]">
                                    <option :value="null">请选择选项</option>
                                    <option v-for="(displayName, internalValue) in availableSetting.options" :value="internalValue">{{ displayName }}</option>
                                </select>
                            </template>
                            <template v-else>
                                <input type="text" v-model="moduleSettings[internalName]" :placeholder="availableSetting.plcaeholder">
                            </template>
                        </div>

                        <div class="ui field">
                            <button type="submit" class="ui small teal fluid button" :disabled="isSubmittingModuleSetting">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: "Edit",
        data: function () {
            return {
                initLoading: true,
                availableCurrencies: [],
                basicPaymentModule: null,
                moduleBasicSettings: null,
                moduleSettings: {},
                moduleChannelSettings: null,
                isDedicatedNotifiable: false,
                notifyURLPrefix: null,

                isSubmittingBasicSetting: false,
                isSubmittingModuleSetting: false,
            };
        },
        created: function () {
            axios.get(route("paymentModules.edit", [this.$router.currentRoute.params.id]), null, {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.availableCurrencies = data.availableCurrencies;
                        this.basicPaymentModule = data.basicPaymentModule;
                        this.moduleBasicSettings = data.paymentModule;
                        this.moduleSettings = data.paymentModuleSettings;
                        this.isDedicatedNotifiable = data.isDedicatedNotifiable;
                        this.notifyURLPrefix = data.notifyURLPrefix;
                        if (Array.isArray(this.moduleSettings))
                            this.moduleSettings = {};
                    }
                })
                .catch(this.$axiosCatchError2Console)
                .then(() => {
                    this.initLoading = false;
                })
            ;
        },
        updated: function () {
            $(".ui.dropdown").dropdown({
                placeholder: false,
            });
        },
        methods: {
            copyTextWithFloatingMessage: function (text) {
                this.$copyText(text)
                    .then(() => {
                        this.positiveFloatingMessage("复制成功");
                    })
                ;
            },
            storeBasicSetting: function () {
                this.isSubmittingBasicSetting = true;
                axios.patch(route("paymentModules.update", [this.moduleBasicSettings.id]), this.moduleBasicSettings, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("保存成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmittingBasicSetting = false;
                    })
                ;
            },
            storeModuleSetting: function () {
                this.isSubmittingModuleSetting = true;
                axios.patch(route("paymentModules.updateSetting", [this.moduleBasicSettings.id]), this.moduleSettings, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("保存成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmittingModuleSetting = false;
                    })
                ;
            }
        },
        computed: {
            payNotifyURL: function () {
                if (this.notifyURLPrefix) {
                    return this.notifyURLPrefix + this.route('paymentModules.payNotify', [this.moduleBasicSettings.id], false);
                }
                return this.route('paymentModules.payNotify', [this.moduleBasicSettings.id]);
            },
            refundNotifyURL: function () {
                if (this.notifyURLPrefix) {
                    return this.notifyURLPrefix + this.route('paymentModules.refundNotify', [this.moduleBasicSettings.id], false);
                }
                return this.route('paymentModules.refundNotify', [this.moduleBasicSettings.id]);
            },
            notifyURL: function () {
                if (this.notifyURLPrefix) {
                    return this.notifyURLPrefix + this.route('paymentModules.notify', [this.moduleBasicSettings.id], false);
                }
                return this.route('paymentModules.notify', [this.moduleBasicSettings.id]);
            }
        },
    }
</script>

<style scoped>

</style>