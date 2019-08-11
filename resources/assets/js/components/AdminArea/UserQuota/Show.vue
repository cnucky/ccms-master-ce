<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">用户配额详情</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui secondary pointing menu">
                <router-link class="item teal" :to="{name: 'userQuotas.edit', params: {id: userQuotaId}}" active-class="active">
                    编辑
                </router-link>
                <router-link class="item teal" :to="{name: 'userQuotas.users.index', params: {id: userQuotaId}}" active-class="active">
                    用户
                </router-link>
            </div>
        </div>

        <div class="sixteen wide column">
            <slide-fade-transition v-if="userQuotaData">
                <router-view ref="child" :user-quota-data="userQuotaData" v-on:retrieveData="retrieveData"></router-view>
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
                userQuotaData: null,
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.$axiosGet(route("userQuotas.show", [this.userQuotaId]), (data) => {
                    this.retrieveData(data);
                })
            },
            retrieveData: function (data) {
                this.userQuotaData = data.data;
            },
        },
        computed: {
            userQuotaId: function () {
                return this.$router.currentRoute.params.id;
            }
        }
    }
</script>

<style scoped>

</style>