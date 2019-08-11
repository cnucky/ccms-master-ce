<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="two fields">
            <div class="ui field">
                <label class="required">{{ $t('common.name') }}</label>
                <input type="text" v-model="temporaryItem.name" required autofocus>
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
    import StatusSelect from "./StatusSelect";
    export default {
        name: "CategoryForm",
        components: {StatusSelect},
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
                this.$refs.statusSelect.clearValue();
                $(this.$refs.select).dropdown("clear");
            },
            setDropdownValue: function (value) {
                $(this.$refs.select).dropdown("set selected", value);
            },
            create: function () {
                this.item = {};
                this.clearDropdown();
                this.$refs.statusSelect.setValue("0");
                this.temporaryItem = {
                    status: 0,
                };
                return this;
            },
            edit: function (item) {
                this.clearDropdown();
                this.item = item;
                this.setDropdownValue(item.image_category_id);
                this.$refs.statusSelect.setValue(item.status !== null ? item.status.toString() : null);
                this.temporaryItem = _.cloneDeep(item);
            },
        }
    }
</script>

<style scoped>

</style>