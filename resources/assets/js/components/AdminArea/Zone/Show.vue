<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 v-if="data" class="ui header">
                {{ data.zone.region.name }} - {{ data.zone.name }}
            </h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui secondary pointing menu">
                <router-link class="item teal" :to="{name: 'zones.dashboard', params: {id: zoneId}}" exact-active-class="active">
                    概览
                </router-link>
                <router-link class="item teal" :to="{name: 'zones.computeNodes', params: {id: zoneId}}" active-class="active">
                    计算节点
                </router-link>
                <router-link class="item teal" :to="{name: 'zones.packages.index', params: {id: zoneId}}" active-class="active">
                    计算实例规格
                </router-link>
            </div>
        </div>

        <div class="sixteen wide column">
            <slide-fade-transition v-if="data">
                <router-view ref="child" :data="data"></router-view>
            </slide-fade-transition>
            <semantic-ui-loader v-else :is-active="true"></semantic-ui-loader>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Show",
        data: function () {
            return {
                isInitLoading: true,
                data: null,
            };
        },
        created: function () {
            this.loadZone();
        },
        methods: {
            loadZone: function () {
                axios.get(route("zones.show", [this.zoneId]), {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.data = data.data;
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isInitLoading = false;
                    })
                ;
            }
        },
        computed: {
            zoneId: function () {
                return this.$router.currentRoute.params.id;
            }
        }
    }
</script>

<style scoped>

</style>