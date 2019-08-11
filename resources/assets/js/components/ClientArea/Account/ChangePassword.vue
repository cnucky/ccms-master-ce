<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <form class="ui form" v-on:submit.prevent="changePassword">
                    <div class="ui field">
                        <label>现密码</label>
                        <input type="password" minlength="6" v-model="form.current_password" required>
                    </div>
                    <div class="ui two fields">
                        <div class="ui field">
                            <label>新密码</label>
                            <input type="password" minlength="6" v-model="form.new_password" required>
                        </div>
                        <div class="ui field">
                            <label>确认新密码</label>
                            <input type="password" minlength="6" v-model="form.new_password_confirmation" required>
                        </div>
                    </div>

                    <div class="ui field" style="margin-top: 30px;">
                        <button type="submit" class="ui small fluid teal button" v-bind:class="{loading: isLoading}" :disabled="isLoading">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ChangePassword",
        data: function () {
            return {
                isLoading: false,
                form: {
                    current_password: "",
                    new_password: "",
                    new_password_confirmation: "",
                },
            };
        },
        methods: {
            changePassword: function () {
                this.isLoading = true;
                axios.patch(route("account.changePassword"), this.form, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.form = {
                                current_password: "",
                                new_password: "",
                                new_password_confirmation: "",
                            };
                            this.positiveFloatingMessage("密码更改成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            }
        },
    }
</script>

<style scoped>

</style>