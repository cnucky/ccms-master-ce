<template>
    <!-- Quick Actions -->
    <ul class="nav navbar-nav quick-actions">
        <li class="dropdown divided user" id="current-user">
            <div class="profile-photo">
                <img :src="this.$store.getters.avatar()" alt/>
            </div>
            <a class="dropdown-toggle options" data-toggle="dropdown" href="#">
                {{ this.$store.getters.name }} <i class="fa fa-caret-down"></i>
            </a>

            <ul class="dropdown-menu arrow settings">
                <li>
                    <router-link :to="{name: 'billing.dashboard'}"><i class="money bill alternate icon"></i><amount :amount="$store.getters.user.credit"></amount></router-link>
                </li>

                <li class="divider"></li>

                <li>
                    <router-link :to="{name: 'account.profile'}"><i class="user icon"></i> 帐号信息</router-link>
                </li>

                <li>
                    <router-link :to="{name: 'account.password'}"><i class="key icon"></i> 更改密码</router-link>
                </li>

                <li class="divider"></li>

                <li>
                    <a href="#" v-on:click.prevent="logout"><i class="fa fa-power-off"></i> 注销</a>
                </li>
            </ul>
        </li>

        <li>
            <router-link :to="{name: 'tickets.index'}"><i class="fa fa-comments"></i></router-link>
        </li>
    </ul>
    <!-- /Quick Actions -->
</template>

<script>
    import Amount from "../CreditRecord/Amount";
    export default {
        name: "AuthenticatedUserQuickAction",
        components: {Amount},
        methods: {
            logout: function () {
                axios.post(route('logout')).then((response) => {
                    refreshCSRFToken();
                    this.$store.commit('update');
                    // updateCSRFToken(response.data.token);
                    if (response.data.result) {
                        this.$router.push({ name: 'login' })
                    }
                }).catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>

<style scoped>

</style>