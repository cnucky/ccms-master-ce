<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui two fields">
            <div class="ui field">
                <label class="required">姓名</label>
                <input type="text" :value="name" v-on:input="$emit('name', $event.target.value)" required>
            </div>

            <div class="ui field">
                <label class="required">Email</label>
                <input type="email" :value="email" v-on:input="$emit('email', $event.target.value)" required>
            </div>
        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label>联系电话</label>
                <input type="tel" name="phone" maxlength="16" :value="phone" v-on:input="$emit('phone', $event.target.value)">
            </div>

            <div class="ui field">
                <label class="required">状态</label>
                <select ref="statusSelect" class="ui dropdown" :value="status" v-on:change="$emit('status', $event.target.value)">
                    <option value="1">正常</option>
                    <option value="2">锁定</option>
                </select>
            </div>
        </div>

        <div class="ui field">
            <label class="required">角色</label>
            <select ref="roleSelect" :value="roleId" v-on:change="$emit('roleId', $event.target.value)">
                <option value=""></option>
                <option v-for="availableRole in availableRoles" :value="availableRole.id">{{ availableRole.name }}</option>
            </select>
        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label v-bind:class="{required: requirePassword}">密码</label>
                <input type="password" min="6" :required="requirePassword" :placeholder="!requirePassword ? '无需更改密码请留空' : ''" :value="password" v-on:input="$emit('password', $event.target.value)">
            </div>

            <div class="ui field">
                <label v-bind:class="{required: requirePassword}">确认密码</label>
                <input type="password" min="6" :required="requirePassword" :placeholder="!requirePassword ? '无需更改密码请留空' : ''" :value="passwordConfirmation" v-on:input="$emit('passwordConfirmation', $event.target.value)">
            </div>
        </div>

        <div class="ui field">
            <button type="submit" class="ui small teal fluid button" v-bind:class="{loading: isSubmitting}" :disabled="isSubmitting">保存</button>
        </div>
    </form>
</template>

<script>
    export default {
        name: "AdminForm",
        props: ["isSubmitting", "availableRoles", "requirePassword", "name", "email", "phone", "roleId", "status", "password", "passwordConfirmation"],
        mounted: function () {
            $(this.$refs.statusSelect).dropdown();
            $(this.$refs.roleSelect).dropdown();
        }
    }
</script>

<style scoped>

</style>