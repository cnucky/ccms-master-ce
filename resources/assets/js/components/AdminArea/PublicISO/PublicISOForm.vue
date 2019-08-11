<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui field">
            <label class="required">{{ $t('common.imageCategory') }}</label>
            <select class="ui search dropdown" v-model="temporaryItem.category_id" ref="select">
                <option value="">请选择{{ $t('common.publicISOCategory') }}</option>
                <option v-for="option in options" :value="option.id">{{ option.name }}</option>
            </select>
        </div>

        <div class="two fields">
            <div class="ui field">
                <label class="required">{{ $t('common.name') }}</label>
                <input type="text" placeholder="如：9.8.0 amd 64 network install" v-model="temporaryItem.name" required autofocus>
            </div>

            <div class="ui field">
                <label class="required">{{ $t('common.internalName') }}</label>
                <input type="text" placeholder="如：debian-9.8.0-amd64-netinst.iso" v-model="temporaryItem.internal_name" required>
            </div>
        </div>

        <div class="two fields">
            <div class="ui field">
                <label>{{ $t('common.order') }}</label>
                <input type="number" placeholder="值大者靠前" v-model="temporaryItem.order">
            </div>

            <div class="ui field">
                <label class="required">{{ $t('common.status') }}</label>
                <status-select ref="statusSelect" v-model="temporaryItem.status"></status-select>
            </div>
        </div>

        <button ref="submitButton" type="submit" class="ui hidden"></button>
    </form>
</template>

<script>
    import StatusSelect from "./../../CommonStatusSelect";
    export default {
        name: "PublicISOForm",
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
                this.setDropdownValue(item.category_id);
                this.$refs.statusSelect.setValue(item.status !== null ? item.status.toString() : null);
                this.temporaryItem = _.cloneDeep(item);
            },
        }
    }
</script>

<style scoped>

</style>