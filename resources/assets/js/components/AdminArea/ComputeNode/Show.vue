<template>
    <div v-bind:class="{'init-loading': isInitLoading}">
        <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
        <semantic-ui-loader v-if="isInitLoading" v-bind:is-active="isInitLoading" v-bind:loader-class="'large'">
            Loading...
        </semantic-ui-loader>
        <div v-else class="ui grid">
            <div class="sixteen wide column" style="
    display: flex;
    flex-direction: row;
    -webkit-box-align: center;
    align-items: center;">
                <online-status :last-communicated-at="computeNode.last_communicated_at" :server-time="serverTime"></online-status>
                <div style="display: inline-block;">
                    <h2 class="ui header">计算节点：{{ computeNode.name }}</h2>
                </div>
            </div>

            <div class="sixteen wide column">
                <div class="ui secondary pointing menu">
                    <router-link class="item teal" :to="{name: 'computeNodes.show', params: {id: computeNode.id}}" exact-active-class="active">
                        概览
                    </router-link>
                    <router-link class="item teal" :to="{name: 'computeNodes.statistics', params: {id: computeNode.id}}" exact-active-class="active">资源统计图</router-link>
                    <router-link class="item teal" :to="{name: 'computeNodes.computeInstances', params: {id: computeNode.id}}" active-class="active">计算实例</router-link>
                    <router-link class="item teal" :to="{name: 'computeNodes.localVolumes', params: {id: computeNode.id}}" active-class="active">本地卷</router-link>
                    <router-link class="item teal" :to="{name: 'computeNodes.showNOVNCBasicSetting', params: {id: computeNode.id}}" active-class="active">noVNC设置</router-link>
                    <router-link class="item teal" :to="{name: 'computeNodes.edit', params: {id: computeNode.id}}" active-class="active">编辑</router-link>
                </div>
            </div>

            <div class="sixteen wide column">
                <slide-fade-transition>
                    <router-view ref="child" :compute-node="computeNode" :trusted-certificate-information="trustedCertificateInformation" :client-certificate-information="clientCertificateInformation" :node-status="nodeStatus" :server-time="serverTime" :show-component="getVueInstance" v-on:item-updated="updateComputeNodeInformation" v-on:data-updated="retrieveData"></router-view>
                </slide-fade-transition>
            </div>
        </div>
    </div>
</template>

<script>
    import OnlineStatus from "./OnlineStatus";
    export default {
        name: "Show",
        components: {OnlineStatus},
        data: function () {
            return {
                isInitLoading: true,
                isLoading: false,
                computeNode: {},
                trustedCertificateInformation: {},
                clientCertificateInformation: {},
                nodeStatus: {},
                serverTime: null,

                currentTab: "",
            };
        },
        created: function () {
            this.updateComputeNodeInformation();
        },
        methods: {
            updateComputeNodeInformation: function (onComplete = undefined) {
                axios.get(route("computeNodes.show", [this.$router.currentRoute.params.id]))
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.retrieveData(data);
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isInitLoading = false;
                        if (typeof onComplete === "function")
                            onComplete();
                    })
                ;
            },
            retrieveData: function (data) {
                this.computeNode = data.computeNode;
                this.trustedCertificateInformation = data.trustedCertificateInformation;
                this.clientCertificateInformation = data.clientCertificateInformation;
                this.nodeStatus = data.nodeStatus;
                this.serverTime = data.serverTime;
            },
            getVueInstance: function () {
                return this;
            },
        }
    }
</script>

<style scoped>
    .ui.secondary >>> a.item.teal:hover {
        color: #00b5ad!important;
    }
</style>