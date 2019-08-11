<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">
                {{ this.pool.human_readable_first_usable_ip }}-{{ this.pool.human_readable_last_usable_ip }} <type-label :entry="this.pool"></type-label>
            </h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui secondary pointing menu">
                <router-link class="item teal" :to="{name: modelInternalName + '.show', params: {id: $router.currentRoute.params.id}}" exact-active-class="active">
                    概览
                </router-link>
                <router-link class="item teal" :to="{name: modelInternalName + '.zoneAssignment', params: {id: $router.currentRoute.params.id}}" active-class="active">
                    可用区分配
                </router-link>
                <router-link class="item teal" :to="{name: modelInternalName + '.nodeAssignment', params: {id: $router.currentRoute.params.id}}" active-class="active">
                    节点分配
                </router-link>
                <router-link class="item teal" :to="{name: modelInternalName + '.assignments.index', params: {id: $router.currentRoute.params.id}}" active-class="active">
                    分配详情
                </router-link>
                <div class="right menu">
                    <router-link class="item teal" :to="{name: modelInternalName + '.edit', params: {id: $router.currentRoute.params.id}}" active-class="active">
                        <i class="edit icon"></i> 编辑
                    </router-link>
                </div>
            </div>
        </div>

        <div class="sixteen wide column" v-if="!isInitLoading">
            <slide-fade-transition>
                <router-view :model-internal-name="modelInternalName" :model-name="modelName" :pool="pool" :show-page="getVueInstance"></router-view>
            </slide-fade-transition>
        </div>
    </div>
</template>

<script>
    import TypeLabel from "./TypeLabel";
    export default {
        name: "Show",
        components: {TypeLabel},
        props: ["modelInternalName", "modelName"],
        data: function () {
            return {
                isInitLoading: false,
                pool: {},
                total_subnet: 0,
                assigned_count: 0,
            };
        },
        created: function () {
            this.loadPool();
        },
        methods: {
            loadPool: function () {
                this.isInitLoading = true;
                axios.get(route(this.modelInternalName + ".show", [this.$router.currentRoute.params.id]))
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.pool = data.pool;
                            this.total_subnet = data.pool.total_subnet;
                            this.assigned_count = data.assigned_count;
                        } else {
                            this.negativeFloatingMessage(data.message);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.isInitLoading = false;
                    })
                ;
            },
            getVueInstance: function () {
                return this;
            }
        }
    }
</script>

<style scoped>

</style>