<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui two fields">
            <div class="ui field">
                <label class="required">姓名</label>
                <input type="text" :value="name" v-on:input="$emit('name', $event.target.value)">
            </div>

            <div class="ui field">
                <label class="required">Email</label>
                <input type="email" :value="email" v-on:input="$emit('email', $event.target.value)">
            </div>
        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label class="required">联系电话</label>
                <div class="ui left action input">
                    <country-and-area-code-select class="basic floating scrolling button" :value="country" v-on:input="(value) => { $emit('country', value) }"></country-and-area-code-select>
                    <input type="tel" name="phone" maxlength="16" :value="phone" v-on:input="$emit('phone', $event.target.value)" required>
                </div>
            </div>

            <div class="ui field">
                <label class="required">状态</label>
                <select ref="statusSelect" class="ui dropdown" :value="status" v-on:change="$emit('status', $event.target.value)">
                    <option value="0">待验证</option>
                    <option value="1">正常</option>
                    <option value="2">锁定</option>
                </select>
            </div>
        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label>禁用自动更改用户配额</label>
                <select ref="disableQuotaAutoUpgradeSelect" :value="disableQuotaAutoUpgrade" v-on:change="$emit('disableQuotaAutoUpgrade', $event.target.value)">
                    <option value="0">否</option>
                    <option value="1">是</option>
                </select>
            </div>

            <div class="ui field">
                <label>配额</label>
                <select ref="userQuotaSelect" :value="userQuotaId" v-on:change="$emit('userQuotaId', $event.target.value)">
                    <option v-for="availableUserQuota in availableUserQuotas" :value="availableUserQuota.id">{{ availableUserQuota.name }}</option>
                </select>
            </div>
        </div>

        <div class="ui field">
            <label v-bind:class="{required: requirePassword}">密码</label>
            <input type="password" min="6" :required="requirePassword" :placeholder="!requirePassword ? '无需更改密码请留空' : ''" :value="password" v-on:input="$emit('password', $event.target.value)">
        </div>

        <div class="ui field">
            <button type="submit" class="ui small teal fluid button" v-bind:class="{loading: isSubmitting}" :disabled="isSubmitting">保存</button>
        </div>
    </form>
</template>

<script>
    import CountryAndAreaCodeSelect from "../../CountryAndAreaCodeSelect";
    export default {
        name: "UserForm",
        components: {CountryAndAreaCodeSelect},
        props: ["isSubmitting", "availableUserQuotas", "requirePassword", "name", "email", "country", "phone", "status", "disableQuotaAutoUpgrade", "userQuotaId", "password"],
        mounted: function () {
            $(this.$refs.statusSelect).dropdown();
            $(this.$refs.disableQuotaAutoUpgradeSelect).dropdown();
            $(this.$refs.userQuotaSelect).dropdown();
        }
    }
</script>

<style scoped>

</style>