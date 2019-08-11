<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">管理员详情</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui secondary pointing menu">
                <router-link class="item teal" :to="{name: 'admins.edit', params: {id: adminId}}" active-class="active">
                    编辑
                </router-link>
            </div>
        </div>

        <div class="sixteen wide column">
            <slide-fade-transition v-if="adminData">
                <router-view ref="child" :admin-data="adminData" v-on:retrieveData="retrieveData"></router-view>
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
                adminData: null,
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                this.$axiosGet(route("admins.show", this.adminId), (data) => {
                    this.retrieveData(data);
                });
            },
            retrieveData: function (data) {
                this.adminData = data.adminData;
            }
        },
        computed: {
            adminId: function () {
                return this.$router.currentRoute.params.id;
            }
        }
    }
</script>

<style scoped>

</style>