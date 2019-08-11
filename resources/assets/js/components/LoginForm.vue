<template>
    <div class="ui segment">
        <form class="ui form login-form" method="POST" :action="route('login')" v-on:submit.prevent="login">
            <csrf-field></csrf-field>

            <div class="login-form-title">
                <h1>登录</h1>
                <p>你的Cloud Computing账号</p>
            </div>

            <div class="ui field" v-bind:class="{ error: 'email' in errors }">
                <div class="ui left icon input">
                    <i class="mail icon"></i>
                    <input type="email" name="email" placeholder="邮箱" value="" required
                           autofocus v-model="form_data.email">
                </div>
            </div>

            <div class="ui field" v-bind:class="{ error: 'password' in errors }">
                <div class="ui left icon input">
                    <i class="lock icon"></i>
                    <input type="password" name="password" placeholder="密码" v-model="form_data.password" required>
                </div>
            </div>

            <div class="ui field">
                <semantic-ui-checkbox label="记住我的登录状态" v-model="form_data.remember"
                                      name="remember"></semantic-ui-checkbox>
            </div>

            <button type="submit" class="ui fluid teal submit button" v-bind:class="{ loading: isLoading }" :disabled="isLoading">登录</button>

            <div class="login-form-additional">
                <router-link :to="{name: 'password.request'}">
                    忘记密码？
                </router-link>

                <span style="float: right">新用户？
                    <router-link :to="{ name: 'register' }">{{ $t('clientarea.register') }}</router-link>
                </span>
            </div>

            <semantic-ui-message type="negative" v-bind:messages="errors"></semantic-ui-message>
        </form>
    </div>
</template>

<script>
    export default {
        name: "LoginForm",
        data: function () {
            return {
                form_data: {
                    email: "",
                    password: "",
                    remember: false,
                },
                errors: {},
                isLoading: false,
            }
        },
        methods: {
            login: function () {
                this.errors = {};
                this.isLoading = true;
                axios.post(route('login'), this.$data.form_data, { vueInstance: this }).then((response) => {
                    var data = response.data;
                    this.$store.commit("update");
                    if (data.result) {
                        this.$router.push({ name: 'dashboard' });
                    }
                }).catch(() => {
                }).then(() => {
                    this.isLoading = false;
                });
            }
        },
        mounted: function () {
        }

    }
</script>

<style scoped>

</style>