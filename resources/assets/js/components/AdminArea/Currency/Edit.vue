<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">货币编辑</h1>
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
                            <label>汇率<template v-if="currencyForm.id === 1">（默认货币不可更改）</template></label>
                            <input v-if="currencyForm.id === 1" type="number" :value="'1.000000'" readonly>
                            <input v-else type="number" step="0.000001" v-model="currencyForm.exchange_rate" required>
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
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.isSubmitting = true;
                axios.get(route("currencies.edit", [this.$router.currentRoute.params.id]), {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.currencyForm = data.currency;
                            this.currencyForm.currency_code = data.currency.code;
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmitting = false;
                    })
                ;
            },
            store: function () {
                this.isSubmitting = true;
                axios.patch(route("currencies.update", [this.currencyForm.id]), this.currencyForm, {vueInstance: this})
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
                        this.isSubmitting = false;
                    })
                ;
            }
        }
    }
</script>

<style scoped>

</style>