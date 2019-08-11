<template>
    <login-form-container>
        <div class="ui segment">
            <form class="ui form login-form" v-on:submit.prevent="resetPassword">
                <div class="login-form-title">
                    <h1>重置密码</h1>
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
                        <input type="password" name="password" placeholder="新密码" v-model="form_data.password" required>
                    </div>
                </div>
                <div class="ui field" v-bind:class="{ error: 'password' in errors }">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input type="password" name="password_confirmation" placeholder="确认新密码" v-model="form_data.password_confirmation" required>
                    </div>
                </div>

                <button type="submit" class="ui fluid teal submit button" v-bind:class="{ loading: isLoading }" :disabled="isLoading">提交</button>

                <semantic-ui-message type="negative" v-bind:messages="errors"></semantic-ui-message>
            </form>
        </div>
    </login-form-container>
</template>

<script>
    export default {
        name: "PasswordReset",
        data: function () {
            return {
                isLoading: false,
                form_data: {
                    token: this.$router.currentRoute.params.token,
                    email: "",
                    password: "",
                    password_confirmation: "",
                },
                errors: [],
            };
        },
        methods: {
            resetPassword: function () {
                this.isLoading = true;
                this.errors = [];
                axios.post(route("password.request"), this.form_data, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$store.commit("update");
                            this.$router.push({name: "dashboard"});
                            this.positiveFloatingMessage("密码重置成功");
                        } else {
                            this.$globalErrnoHandler(data, () => {
                                this.errors = [data.message];
                            });
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            }
        }
    }
</script>

<style scoped>

</style>