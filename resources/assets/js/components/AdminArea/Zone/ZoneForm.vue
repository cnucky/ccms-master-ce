<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui field">
            <label class="required">名称</label>
            <input type="text" v-model="temporaryItem.name" required autofocus>
        </div>

        <div class="ui field">
            <label>储存单价</label>
            <div class="ui right labeled input">
                <input type="number" step="0.0001" max="9999.9999" min="0" placeholder="如：0.1235，最多可有四位整数与四位小数" v-model="temporaryItem.storage_price_per_hour_per_gib">
                <label class="ui basic label">/hour*Gib</label>
            </div>
        </div>

        <div class="ui field">
            <label class="required">{{ $t('common.region') }}</label>
            <select class="ui search dropdown" v-model="temporaryItem.region_id" ref="select">
                <option value="">请选择地域</option>
                <option v-for="option in options" :value="option.id"><i :class="option.icon_class"></i> #{{ option.id }} {{ option.name }}</option>
            </select>
        </div>

        <div class="ui field">
            <label>描述</label>
            <textarea v-model="temporaryItem.description" maxlength="255"></textarea>
        </div>

        <button ref="submitButton" type="submit" class="ui hidden"></button>
    </form>
</template>

<script>
    export default {
        name: "ZoneForm",
        props: ["options", "isEditing"],
        data: function () {
            return {
                item: {},
                temporaryItem: {},
            }
        },
        mounted: function () {
            $(this.$refs.select).dropdown({
                fullTextSearch: true,
                clearable: true,
            });
        },
        methods: {
            clearDropdown: function () {
                $(this.$refs.select).dropdown("clear");
            },
            setDropdownValue: function (value) {
                $(this.$refs.select).dropdown("set selected", value);
            },
            create: function () {
                this.item = {};
                this.temporaryItem = {};
                this.clearDropdown();
                return this;
            },
            edit: function (item) {
                this.item = item;
                this.setDropdownValue(item.region_id);
                this.temporaryItem = _.cloneDeep(item);
            },
        }
    }
</script>

<style scoped>

</style>