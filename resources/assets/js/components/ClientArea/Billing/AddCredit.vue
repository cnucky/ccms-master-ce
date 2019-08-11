<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">自助充值</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" style="padding-left: 1.5em; padding-right: 1.5em;" v-bind:class="{loading: isLoading || isPaying}">

                <div>
                    <div style="width: 80px; text-align: right; float: left; line-height: 35px;">金额</div>
                    <div style="margin-left: 115px;">
                        <div class="ui small labeled input">
                            <div class="ui label">
                                {{ $store.getters.defaultCurrency.prefix }}
                            </div>
                            <input type="number" step="0.01" min="0.01" v-model="payForm.fee">
                        </div>
                    </div>
                </div>

                <div style="margin-top: 50px;">
                    <div style="width: 80px; text-align: right; float: left; line-height: 55px;">付款方式</div>
                    <div style="margin-left: 115px;">
                        <div class="ui grid">
                            <div class="four wide column" v-for="availablePaymentModule in availablePaymentModules">
                                <div class="payment-module-selection" v-bind:class="{selected: payForm.paymentModule === availablePaymentModule.id}" v-on:click="payForm.paymentModule = availablePaymentModule.id">{{ availablePaymentModule.name }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="ui small fluid teal green button" style="margin-top: 50px;" :disabled="isLoading || isPaying" v-on:click="pay">支付</button>

                <form ref="hiddenForm" class="hidden">
                </form>
            </div>
        </div>

        <q-r-code-modal ref="qrCodeModal" v-if="qrCodeText" :trade-id="tradeId" class="tiny" v-on:tradeStatusChanged="tradeStatusChanged">
            <i class="close icon"></i>
            <div class="header">
                Scan QR Code
            </div>
            <div class="content" style="text-align: center">
                <p style="color: gray;">请扫描以下二维码，并完成支付</p>
                <q-r-code :text="qrCodeText" :width="256" :height="256"></q-r-code>
            </div>
            <div class="actions">
                <div class="ui ok right labeled icon button">
                    关闭
                    <i class="checkmark icon"></i>
                </div>
            </div>
        </q-r-code-modal>
    </div>
</template>

<script>
    import QRCode from "./QRCode";
    import QRCodeModal from "./QRCodeModal";

    export default {
        name: "AddCredit",
        components: {QRCodeModal, QRCode},
        data: function () {
            return {
                isPaying: false,
                isLoading: false,
                availablePaymentModules: [],
                payForm: {
                    paymentModule: null,
                    fee: "",
                },

                tradeId: null,

                qrCodeText: null,
            };
        },
        created: function () {
            this.load();
        },
        mounted: function () {
            let recaptchaScript = document.createElement('script');
            recaptchaScript.setAttribute('src', '/static/js/qrcode.min.js');
            this.$el.appendChild(recaptchaScript);
        },
        methods: {
            load: function () {
                this.isLoading = true;
                axios.get(route("billing.addCredit"))
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.availablePaymentModules = data.available;
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
            pay: function () {
                if (this.payForm.paymentModule === null) {
                    this.negativeFloatingMessage("请选择付款方式");
                    return;
                }

                this.isLoading = true;
                this.qrCodeText = null;
                axios.post(route("paymentModules.pay", [this.payForm.paymentModule]), this.payForm, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.tradeId = data.tradeId;
                            switch (data.modulePayResponse.type) {
                                case 1:
                                    break;
                                case 2:
                                    this.isPaying = true;
                                    window.location.href = data.modulePayResponse.response;
                                    break;
                                case 3:
                                    this.qrCodeText = data.modulePayResponse.response;
                                    this.positiveFloatingMessage("请扫描二维码完成支付");
                                    break;
                                default:
                                    this.negativeMessage("未知回应类型")
                            }
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
            tradeStatusChanged: function (data) {
                var status = data.status;
                this.$refs.qrCodeModal.hide();
                if (status === 1) {
                    this.$store.commit("userCredit", data.credit);
                    this.positiveFloatingMessage("充值成功，当前余额：" + data.credit);
                } else {
                    this.negativeFloatingMessage("订单已取消");
                }
            }
        },
        watch: {
            availablePaymentModules: function () {
                $(this.$refs.paymentModuleSelect).dropdown();
            }
        }
    }
</script>

<style scoped>
    .payment-module-selection {
        border: 1px solid rgba(34, 36, 38, .15);
        text-align: center;
        height: 115px;
        line-height: 111px;
        cursor: pointer;
        font-size: 20px;
    }

    .payment-module-selection:hover {
        box-shadow: rgb(238, 238, 255) 0 0 8px 2px;
    }

    .payment-module-selection.selected {
        border-color: #00c1de !important;
        cursor: default;
    }
</style>