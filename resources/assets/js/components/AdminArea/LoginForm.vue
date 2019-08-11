<template>
    <div class="ui segment">
        <form class="ui form login-form" method="POST" :action="route('admin.login')" v-on:submit.prevent="login">
            <csrf-field></csrf-field>

            <div class="login-form-title">
                <h1>管理员登录</h1>
                <p>Cloud Computing Management System</p>
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
            
            <semantic-ui-message type="negative" v-bind:messages="errors"></semantic-ui-message>
        </form>
    </div>
</template>

<script>
    export default {
        name: "AdminLoginForm",
        data: function () {
            return {
                form_data: {
                    email: "",
                    password: "",
                    remember: "",
                },
                errors: {},
                isLoading: false,
            }
        },
        methods: {
            login: function () {
                this.isLoading = true;
                axios
                    .post(route('admin.login'), this.form_data, {vueInstance: this})
                    .then((response) => {
                        this.$store.commit('update');
                        this.$router.push({name: 'admin.dashboard'});
                    }).catch((error) => {
                        console.log(error);
                    }).then(() => {
                        this.isLoading = false;
                    })
                ;
            }
        }
    }
</script>

<style scoped>

</style>