<template>
    <login-form-container>
        <div class="ui segment">
            <form class="ui form login-form" v-on:submit.prevent="submitChangingPasswordRequest">
                <div class="login-form-title">
                    <h3>重置密码</h3>
                </div>

                <div class="ui field">
                    <div class="ui left icon input">
                        <i class="mail icon"></i>
                        <input type="email" name="email" placeholder="邮箱" value="" required
                               autofocus v-model="email">
                    </div>
                </div>

                <div class="ui field">
                    <button type="submit" class="ui fluid teal submit button" v-bind:class="{ loading: isLoading }" :disabled="isLoading">提交</button>
                </div>

                <semantic-ui-message type="negative" v-bind:messages="errors"></semantic-ui-message>
                <semantic-ui-message type="positive" v-bind:messages="positiveMessages"></semantic-ui-message>
            </form>
        </div>
    </login-form-container>
</template>

<script>
    export default {
        name: "PasswordResetRequest",
        data: function () {
            return {
                isLoading: false,
                email: "",
                errors: [],
                positiveMessages: [],
            };
        },
        methods: {
            submitChangingPasswordRequest: function () {
                this.isLoading = true;
                this.errors = [];
                this.positiveMessages = [];
                axios.post(route('password.email'), {email: this.email}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.email = "";
                            this.positiveMessages = data.message;
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