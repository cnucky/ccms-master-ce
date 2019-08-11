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
                    <router-link :to="{name: 'admins.edit', params: {id: $store.getters.currentUser.id}}"><i class="user icon"></i> 帐号信息</router-link>
                </li>

                <li class="divider"></li>

                <li>
                    <a href="#" v-on:click.prevent="logout"><i class="fa fa-power-off"></i> 注销</a>
                </li>
            </ul>
        </li>

        <li>
            <router-link :to="{name: 'admin.tickets.index'}"><i class="fa fa-comments"></i></router-link>
        </li>
    </ul>
    <!-- /Quick Actions -->
</template>

<script>
    export default {
        name: "AuthenticatedUserQuickAction",
        methods: {
            logout: function () {
                axios.post(route('admin.logout')).then((response) => {
                    refreshCSRFToken();
                    this.$store.commit('update');
                    // updateCSRFToken(response.data.token);
                    if (response.data.result) {
                        this.$router.push({ name: 'admin.login' })
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