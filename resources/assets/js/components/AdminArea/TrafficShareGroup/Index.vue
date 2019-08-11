<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">流量共享组</h1>
        </div>

        <div class="sixteen wide column">
            <model-index-refresh-button v-on:click="load"></model-index-refresh-button>
            <router-link class="ui tiny teal button" :to="{name: 'trafficShareGroups.create'}"><i class="plus icon"></i> 创建</router-link>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader :is-active="isLoading"></semantic-ui-loader>
            <table class="ui unstackable fixed table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>名称</th>
                    <th>下行单价/GiB</th>
                    <th>上行单价/GiB</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="trafficShareGroup in trafficShareGroups">
                    <td>{{ trafficShareGroup.id }}</td>
                    <td>{{ trafficShareGroup.name }}</td>
                    <td><amount :amount="trafficShareGroup.price_per_rx_gib"></amount></td>
                    <td><amount :amount="trafficShareGroup.price_per_tx_gib"></amount></td>
                    <td>
                        <router-link class="ui tiny button" :to="{name: 'trafficShareGroups.show', params: {id: trafficShareGroup.id}}"><i class="edit icon"></i> 详情</router-link>
                        <a v-if="trafficShareGroup.id !== 1" class="ui tiny red button" href="#" v-on:click.prevent="destroy(trafficShareGroup)" v-bind:class="{disabled: isDeleting}"><i class="trash icon"></i> 删除</a>
                        <label v-else class="ui label">默认</label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import Amount from "../../CreditRecord/Amount";
    export default {
        name: "Index",
        components: {Amount},
        data: function () {
            return {
                isLoading: false,
                isDeleting: false,
                trafficShareGroups: [],
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.isLoading = true;
                this.$axiosGet(route("trafficShareGroups.index"), (data) => {
                    this.trafficShareGroups = data.trafficShareGroups;
                }, () => {
                    this.isLoading = false;
                })
            },
            destroy: function (trafficShareGroup) {
                this.confirmModal()
                    .init()
                    .withHeader("提示")
                    .withContent("确定删除流量组#"+ trafficShareGroup.id +"？该流量组下的可用区以及未结算的流量将自动归到默认流量组。")
                    .withOnApprove(() => {
                        this.isDeleting = true;
                        this.$axiosDelete(route("trafficShareGroups.destroy", [trafficShareGroup.id]), (data) => {
                            this.load();
                            this.positiveFloatingMessage("删除成功");
                        }, () => {
                            this.isDeleting = false;
                        })
                    })
                    .show()
                ;
            }
        },
    }
</script>

<style scoped>

</style>