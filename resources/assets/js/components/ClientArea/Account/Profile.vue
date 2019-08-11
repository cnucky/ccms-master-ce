<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <form class="ui form" v-on:submit.prevent="storeProfile">
                    <div class="ui two fields">
                        <div class="ui field">
                            <label class="required">姓名</label>
                            <div class="ui left icon input">
                                <i class="user icon"></i>
                                <input type="text" v-model="user.name" required>
                            </div>
                        </div>
                        <div class="ui field">
                            <label class="required">邮箱</label>
                            <div class="ui left icon input">
                                <i class="mail icon"></i>
                                <input type="email" v-model="user.email" required>
                            </div>
                        </div>
                    </div>

                    <div class="ui two fields">
                        <div class="ui field">
                            <label class="required">联系电话</label>
                            <div class="ui left action input">
                                <country-and-area-code-select class="basic floating scrolling button" v-model="user.country"></country-and-area-code-select>
                                <input type="tel" :value="user.phone" required>
                            </div>
                        </div>

                        <div class="ui field">
                            <label>首选语言</label>
                            <select ref="languageSelect" class="ui dropdown">
                                <option value="zh-CN" selected>简体中文</option>
                            </select>
                        </div>
                    </div>

                    <div class="ui field" style="margin-top: 30px;">
                        <button type="submit" class="ui small fluid teal button" v-bind:class="{loading: isSubmitting}" :disabled="isSubmitting">保存</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <h3 class="ui header">注销帐号</h3>
                <div>注销帐号将会释放帐号下的所有资源，且无法恢复！</div>
                <button class="ui inverted red button" style="display: block; margin-left: auto;" disabled>注销</button>
            </div>
        </div>
    </div>
</template>

<script>
    import CountryAndAreaCodeSelect from "../../CountryAndAreaCodeSelect";
    import Column from "../ComputeInstance/Show/Information/Column";
    import LocalTime from "../../LocalTime";
    export default {
        name: "Basic",
        components: {LocalTime, Column, CountryAndAreaCodeSelect},
        data: function () {
            return {
                isSubmitting: false,
                user: _.cloneDeep(this.$store.getters.user),
            };
        },
        mounted: function () {
            $(this.$refs.languageSelect).dropdown();
        },
        methods: {
            storeProfile: function () {
                this.isSubmitting = true;
                axios.patch(route("account.changeProfile"), this.user, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("保存成功");
                            this.$store.commit("setUser", data.user);
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmitting = false;
                    })
                ;
            }
        }
    }
</script>

<style scoped>

</style>