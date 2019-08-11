<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">货币列表</h1>
        </div>

        <div class="sixteen wide column">
            <refresh-button v-on:click="load"></refresh-button>
            <create-model-button model-name="货币" v-on:click="$router.push({name: 'currencies.create'})"></create-model-button>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader :is-active="isLoading"></semantic-ui-loader>
            <table class="ui unstackable table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>代码</th>
                    <th>前缀</th>
                    <th>后缀</th>
                    <th>汇率</th>
                    <th class="three column wide"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(currency, index) in currencies">
                    <td>{{ currency.id }}</td>
                    <td>{{ currency.code }}</td>
                    <td>{{ currency.prefix }}</td>
                    <td>{{ currency.suffix }}</td>
                    <td>{{ currency.exchange_rate }}</td>
                    <td>
                        <router-link class="ui tiny button" :to="{name: 'currencies.edit', params: {id: currency.id}}"><i class="edit icon"></i> 编辑</router-link>
                        <label v-if="currency.id === 1" class="ui label">默认货币</label>
                        <button v-else class="ui tiny red button" v-on:click="destroy(currency, index)"><i class="trash icon"></i> 删除</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import CreateModelButton from "../../ModelIndex/CreateModelButton";
    import RefreshButton from "../../ModelIndex/RefreshButton";
    export default {
        name: "Index",
        components: {RefreshButton, CreateModelButton},
        data: function () {
            return {
                isLoading: false,
                currencies: [],
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.isLoading = true;
                axios.get(route("currencies.index"), {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.currencies = data.currencies;
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
            destroy: function (currency, index) {
                this.confirmModal()
                    .init()
                    .withHeader("提示")
                    .withContent("确定删除货币["+ currency.code +"]？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.delete(route("currencies.destroy", [currency.id]), {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.positiveFloatingMessage("删除成功");
                                    this.currencies.splice(index, 1);
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