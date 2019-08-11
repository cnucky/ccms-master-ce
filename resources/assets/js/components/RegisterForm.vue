<template>
    <div>
        <div class="ui top attached segment">
            <form class="ui form login-form" method="POST" :action="route('register')"
                  @submit.prevent="submit_register_form">
                <csrf-field></csrf-field>

                <div class="login-form-title">
                    <h2>加入Cloud Computing，体验云计算之美</h2>
                </div>

                <div class="ui field" :class="{ error: 'name' in errors }">
                    <label class="required">姓名</label>
                    <div class="ui left icon input">
                        <i class="user icon"></i>
                        <input type="text" name="name" placeholder="姓名" v-model="form_data.name" required
                               autofocus>
                    </div>
                </div>

                <div class="ui field" :class="{ error: 'email' in errors }">
                    <label class="required">邮箱</label>
                    <div class="ui left icon input">
                        <i class="mail icon"></i>
                        <input type="email" name="email" placeholder="邮箱" v-model="form_data.email" required>
                    </div>
                </div>

                <div class="ui field" :class="{ error: 'phone' in errors }">
                    <label class="required">联系电话</label>
                    <div class="ui left action input">
                        <country-and-area-code-select class="basic floating scrolling button" v-model="form_data.country"></country-and-area-code-select>
                        <input type="tel" name="phone" placeholder="联系电话" v-model="form_data.phone" required>
                    </div>
                </div>

                <div class="ui two fields">

                    <div class="ui field" :class="{ error: 'password' in errors }">
                        <label class="required">密码</label>
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input type="password" name="password" placeholder="密码" v-model="form_data.password"
                                   required>
                        </div>
                    </div>

                    <div class="ui field" :class="{ error: 'password' in errors }">
                        <label class="required">确认密码</label>
                        <div class="ui left icon input">
                            <i class="lock icon"></i>
                            <input type="password" name="password" placeholder="确认密码"
                                   v-model="form_data.password_confirmation" required>
                        </div>
                    </div>
                </div>

                <div class="ui field">
                    <linked-label-checkbox id="accept_tos" label='已阅读并同意<a href="#">服务条款</a>' name="accept_tos" required
                                           v-model="form_data.accept_tos"></linked-label-checkbox>
                </div>

                <button v-if="form_data.accept_tos" type="submit" class="ui fluid teal submit button"
                        :class="{ loading: isLoading }">注册
                </button>
                <button v-else type="submit" class="ui fluid teal submit disabled button">您必须阅读并同意服务条款</button>

                <semantic-ui-message type="negative" v-bind:messages="errors"></semantic-ui-message>
            </form>
        </div>

        <div class="ui bottom attached message">
            <p style="text-align: center;">已有账号？
                <router-link :to="{ name: 'login' }">{{ $t('clientarea.login') }}</router-link>
            </p>
        </div>
    </div>
</template>

<script>
    import CountryAndAreaCodeSelect from "./CountryAndAreaCodeSelect";
    export default {
        name: "RegisterForm",
        components: {CountryAndAreaCodeSelect},
        data: function () {
            return {
                form_data: {
                    name: "",
                    email: "",
                    phone: "",
                    password: "",
                    password_confirmation: "",
                    accept_tos: "",
                    country: "cn",
                },
                errors: {},
                isLoading: false,
            }
        },
        methods: {
            submit_register_form: function () {
                this.errors = {};
                this.isLoading = true;
                axios
                    .post(route('register', this.form_data))
                    .then((response) => {
                        if (response.data.result) {
                            this.$store.commit('update');
                            this.$router.push({name: 'dashboard'});
                        }
                    })
                    .catch((error) => {
                        try {
                            if (error.response.data.hasOwnProperty("errors"))
                                this.errors = error.response.data.errors;
                            else if (error.response.data.hasOwnProperty("message")) {
                                this.errors = {0: error.response.data.message};
                            }
                        } catch (e) {
                        }
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