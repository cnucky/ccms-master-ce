<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header"><template v-if="userData">#{{ userData.user.id }} - </template>用户详情</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui secondary pointing menu">
                <router-link class="item teal" :to="{name: 'users.show', params: {id: this.userId}}" exact-active-class="active">
                    概览
                </router-link>
                <router-link class="item teal" :to="{name: 'users.credit', params: {id: this.userId}}" active-class="active">
                    余额
                </router-link>
                <router-link class="item teal" :to="{name: 'users.consumption', params: {id: this.userId}}" active-class="active">
                    消费
                </router-link>
                <router-link class="item teal" :to="{name: 'users.computeInstances.index', params: {id: this.userId}}" active-class="active">
                    计算实例
                </router-link>
                <router-link class="item teal" :to="{name: 'users.localVolumes.index', params: {id: this.userId}}" active-class="active">
                    本地卷
                </router-link>
                <router-link class="item teal" :to="{name: 'users.ipv4s.index', params: {id: this.userId}}" active-class="active">
                    IPv4
                </router-link>
                <router-link class="item teal" :to="{name: 'users.ipv6s.index', params: {id: this.userId}}" active-class="active">
                    IPv6
                </router-link>
                <router-link class="item teal" :to="{name: 'admin.tickets.indexByUser', params: {id: this.userId}}" active-class="active">
                    工单
                </router-link>
                <router-link class="item teal" :to="{name: 'users.paymentTrades.index', params: {id: this.userId}}" active-class="active">
                    充值
                </router-link>

                <div class="right menu">
                    <router-link class="item teal" :to="{name: 'users.edit', params: {id: this.userId}}" active-class="active">
                        <i class="edit icon"></i> 编辑
                    </router-link>
                    <a v-if="$hasAnyPermissionTo('ADMIN_PERM_LOGIN_AS_USER')" class="item" :href="route('users.login', [this.userId])" target="_blank"><i class="external alternate icon"></i> 登录为此用户</a>
                    <template v-if="userData">
                        <a v-if="userData.user.status === 1" class="item" v-bind:class="{disabled: isSubmitting}" v-on:click="suspend">
                            <i class="lock icon"></i> 锁定帐号
                        </a>
                        <a v-if="userData.user.status === 2" class="item" v-bind:class="{disabled: isSubmitting}" v-on:click="unsuspend">
                            <i class="unlock icon"></i> 解锁帐号
                        </a>
                    </template>
                    <a class="item" style="color: red;" href="#" v-on:click.prevent="destroy" v-bind:class="{disabled: isSubmitting}">
                        <i class="trash icon"></i> 删除用户
                    </a>
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <slide-fade-transition mode="out-in">
                <router-view v-if="userData" ref="child" :user-data="userData"></router-view>
                <semantic-ui-loader v-else :is-active="true" style="margin-top: 100px;"></semantic-ui-loader>
            </slide-fade-transition>
        </div>
    </div>
</template>

<script>
    import FlexContainer from "../../FlexContainer";
    export default {
        name: "Show",
        components: {FlexContainer},
        data: function () {
            return {
                isSubmitting: false,
                userData: null,
            };
        },
        created: function () {
            this.loadUser();
        },
        methods: {
            loadUser: function () {
                this.$axiosGet(route("users.show", [this.userId]), (data) => {
                    this.userData = data.data;
                });
            },
            suspend: function () {
                this.$axiosPatch(route("users.suspend", [this.userId]), null, (data) => {
                    this.$set(this.userData.user, "status", 2);
                    this.positiveFloatingMessage("锁定成功");
                });
            },
            unsuspend: function () {
                this.$axiosPatch(route("users.unsuspend", [this.userId]), null, (data) => {
                    this.$set(this.userData.user, "status", 1);
                    this.positiveFloatingMessage("解锁成功");
                });
            },
            destroy: function () {
                this.confirmModal()
                    .withHeader("提示")
                    .withTextContent("确定删除用户？")
                    .withOnApprove(() => {
                        this.isSubmitting = true;
                        this.$axiosDelete(route("users.destroy", [this.userId]), () => {
                            this.$router.push({name: "users.index"});
                            this.positiveFloatingMessage("用户删除成功");
                        }, () => {
                            this.isSubmitting = false;
                        })
                    })
                    .show()
                ;
            },
        },
        computed: {
            userId: function () {
                return this.$router.currentRoute.params.id;
            }
        }
    }
</script>

<style scoped>

</style>