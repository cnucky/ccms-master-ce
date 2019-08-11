<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="two fields">
            <div class="ui field">
                <label>名称</label>
                <input type="text" placeholder="如：Debian" v-model="temporaryItem.name">
            </div>

            <div class="ui field">
                <label>{{ $t('common.iconClass') }}</label>
                <input type="text"  placeholder="如：fl-debian" v-model="temporaryItem.icon_class">
            </div>
        </div>

        <div class="two fields">
            <div class="ui field">
                <label>{{ $t('common.order') }}</label>
                <input type="number" placeholder="值大者靠前" v-model="temporaryItem.order">
            </div>

            <div class="ui field">
                <label>{{ $t('common.status') }}</label>
                <status-select ref="statusSelect" v-model="temporaryItem.status"></status-select>
            </div>
        </div>

        <button ref="submitButton" type="submit" class="ui hidden"></button>
    </form>
</template>

<script>
    import StatusSelect from "../Image/StatusSelect";
    export default {
        name: "PublicFloppyCategoryForm",
        components: {StatusSelect},
        props: ["isEditing"],
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
                this.$refs.statusSelect.clearValue();
            },
            setDropdownValue: function (value) {
                this.$refs.statusSelect.setValue(value);
            },
            create: function () {
                this.item = {};
                this.clearDropdown();
                this.temporaryItem = {
                    status: "0"
                };
                this.setDropdownValue("0");
                return this;
            },
            edit: function (item) {
                this.item = item;
                this.setDropdownValue(item.status === null ? null : item.status.toString());
                this.temporaryItem = _.cloneDeep(item);
            },
        }
    }
</script>

<style scoped>

</style>