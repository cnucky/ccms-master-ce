<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui two fields">
            <div class="ui field">
                <label>名称</label>
                <input :value="name" v-on:input="$emit('name', $event.target.value)" type="text" maxlength="32" required>
            </div>
            <div class="ui field">
                <label>顺序（小优先）</label>
                <input :value="order" v-on:input="$emit('order', $event.target.value)" type="number">
            </div>
        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label>状态</label>
                <select :value="status" v-on:change="$emit('status', $event.target.value)" class="ui dropdown">
                    <option value="0">隐藏</option>
                    <option value="1">显示</option>
                </select>
            </div>
            <div class="ui field">
                <label>显示相关服务选项</label>
                <select :value="showRelativeServiceSelect" v-on:change="$emit('showRelativeServiceSelect', $event.target.value)" class="ui dropdown">
                    <option value="0">否</option>
                    <option value="1">是</option>
                </select>
            </div>
        </div>

        <div class="ui field">
            <label>描述</label>
            <textarea :value="description" v-on:change="$emit('description', $event.target.value)" rows="3" maxlength="255"></textarea>
        </div>

        <div class="ui field">
            <button class="ui fluid tiny teal button" v-bind:class="{loading: isSubmitting}" :disabled="isSubmitting">保存</button>
        </div>
    </form>
</template>

<script>
    export default {
        name: "DepartmentForm",
        props: ["isSubmitting", "name", "order", "status", "showRelativeServiceSelect", "description"],
        mounted: function () {
            $(".ui.dropdown").dropdown();
        },
    }
</script>

<style scoped>

</style>