<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">创建货币</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isSubmitting}">
                <form class="ui form" v-on:submit.prevent="store">
                    <div class="ui two fields">
                        <div class=" field">
                            <label>ISO 4217代码</label>
                            <input type="text" minlength="3" maxlength="3" placeholder="CNY" v-model="currencyForm.currency_code" required>
                        </div>
                        <div class="ui field">
                            <label>汇率</label>
                            <input type="number" step="0.000001" v-model="currencyForm.exchange_rate" required>
                        </div>
                    </div>
                    <div class="ui two fields">
                        <div class="ui field">
                            <label>前缀</label>
                            <input type="text" maxlength="8" placeholder="￥" v-model="currencyForm.prefix">
                        </div>
                        <div class="ui field">
                            <label>后缀</label>
                            <input type="text" maxlength="8" v-model="currencyForm.suffix">
                        </div>
                    </div>

                    <div class="ui field">
                        <button class="ui tiny fluid teal button">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "New",
        data: function () {
            return {
                isSubmitting: false,
                currencyForm: {
                    currency_code: "",
                    exchange_rate: "",
                    prefix: "",
                    suffix: "",
                },
            };
        },
        methods: {
            store: function () {
                this.isSubmitting = true;
                axios.post(route("currencies.store"), this.currencyForm, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$router.push({name: "currencies.index"});
                            this.positiveFloatingMessage("货币创建成功");
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