<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header"><template v-if="trafficShareGroup">#{{ trafficShareGroup.id }} - </template>流量共享组详情</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui secondary pointing menu">
                <router-link class="item teal" :to="{name: 'trafficShareGroups.zones.index', params: {id: this.groupId}}" active-class="active">
                    可用区
                </router-link>
                <router-link class="item teal" :to="{name: 'trafficShareGroups.edit', params: {id: this.groupId}}" active-class="active">
                    编辑
                </router-link>
            </div>
        </div>

        <div class="sixteen wide column">
            <slide-fade-transition>
                <router-view v-if="trafficShareGroup" ref="child" :traffic-share-group="trafficShareGroup" v-on:retrieveData="(data) => {
                    trafficShareGroup = data.trafficShareGroup;
                }"></router-view>
                <semantic-ui-loader v-else :is-active="true" style="margin-top: 100px;"></semantic-ui-loader>
            </slide-fade-transition>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Show",
        data: function () {
            return {
                isLoading: false,
                trafficShareGroup: null,
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.isLoading = true;
                this.$axiosGet(route("trafficShareGroups.show", this.groupId), (data) => {
                    this.trafficShareGroup = data.trafficShareGroup;
                }, () => {
                    this.isLoading = false;
                })
            }
        },
        computed: {
            groupId: function () {
                return this.$router.currentRoute.params.id;
            }
        }
    }
</script>

<style scoped>

</style>