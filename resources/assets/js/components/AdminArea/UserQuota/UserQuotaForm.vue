<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui field">
            <label class="required">名称</label>
            <input type="text" v-model="userQuota.name" maxlength="32" required>
        </div>
        <div class="ui two fields">
            <div class="ui field">
                <label>实例上限</label>
                <input type="number" placeholder="无限制" v-model="userQuota.max_instance">
            </div>
            <div class="ui field">
                <label>卷总容量上限</label>
                <div class="ui right labeled input">
                    <input type="number" placeholder="无限制" v-model="userQuota.max_storage_capacity_in_gib_unit">
                    <label class="ui basic label">GiB</label>
                </div>
            </div>

        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label>弹性IPv4上限</label>
                <input type="number" placeholder="无限制" v-model="userQuota.max_elastic_ipv4_block">
            </div>
            <div class="ui field">
                <label>弹性IPv6上限</label>
                <input type="number" placeholder="无限制" v-model="userQuota.max_elastic_ipv6_block">
            </div>
        </div>

        <div class="ui field">
            <label>自动更改用户配额，当净充值金额超过</label>
            <input type="number" placeholder="不自动为用户分配此配额" v-model="userQuota.auto_upgrade_after_net_income_more_than" :disabled="userQuota.id === 1">
        </div>

        <div class="ui field">
            <label>备注</label>
            <textarea rows="3" maxlength="255" v-model="userQuota.description"></textarea>
        </div>

        <div class="ui field">
            <button type="submit" class="ui small teal fluid button" v-bind:class="{loading: isSubmitting}" :disabled="isSubmitting">保存</button>
        </div>
    </form>
</template>

<script>
    export default {
        name: "UserQuotaForm",
        props: ["isSubmitting", "userQuota"],
    }
</script>

<style scoped>

</style>