<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui field">
            <label class="required">{{ $t('common.imageCategory') }}</label>
            <select class="ui search dropdown" v-model="temporaryItem.image_category_id" ref="select">
                <option value="">请选择{{ $t('common.imageCategory') }}</option>
                <option v-for="option in options" :value="option.id">{{ option.name }}</option>
            </select>
        </div>

        <div class="two fields">
            <div class="ui field">
                <label class="required">{{ $t('common.imageName') }}</label>
                <input type="text" placeholder="如：7.6 x86_64" v-model="temporaryItem.name" required autofocus>
            </div>

            <div class="ui field">
                <label class="required">{{ $t('common.imageInternalName') }}</label>
                <input type="text" placeholder="如：redHatEnterpriseLinux7.6" v-model="temporaryItem.internal_name" required>
            </div>
        </div>

        <div class="two fields">
            <div class="ui field">
                <label class="required">硬盘容量最低要求</label>
                <div class="ui right labeled input">
                    <input type="number" min="0" placeholder="为0为无要求" v-model="temporaryItem.min_disk" required>
                    <div class="ui basic label">
                        GiB
                    </div>
                </div>
            </div>

            <div class="ui field">
                <label class="required">物理内存容量最低要求</label>
                <div class="ui right labeled input">
                    <input type="number" min="0" placeholder="为0为无要求" v-model="temporaryItem.min_memory" required>
                    <div class="ui basic label">
                        MiB
                    </div>
                </div>
            </div>
        </div>

        <div class="two fields">
            <div class="ui field">
                <label class="required">芯片组</label>
                <select ref="machineTypeSelect" class="ui dropdown" v-model="temporaryItem.machine_type">
                    <option value="0">Q35 + ICH9</option>
                    <option value="1">i440FX + PIIX</option>
                </select>
            </div>

            <div class="ui field">
                <label class="required">引导程序：</label>
                <select ref="useLegacyBIOSSelect" class="ui dropdown" v-model="temporaryItem.use_legacy_bios">
                    <option value="0">UEFI</option>
                    <option value="1">BIOS</option>
                </select>
            </div>
        </div>

        <div class="two fields">
            <div class="ui field">
                <label>{{ $t('common.imageForceVersionLongName') }}</label>
                <input type="number" placeholder="留空自动使用最新版本" v-model="temporaryItem.force_version">
            </div>

            <div class="ui field">
                <label>{{ $t('common.order') }}</label>
                <input type="number" placeholder="值大者靠前" v-model="temporaryItem.order">
            </div>
        </div>

        <div class="two fields">
            <div class="ui field">
                <label>{{ $t('common.pricePerHour') }}</label>
                <input type="number" step="0.0001" max="9999.9999" min="0" placeholder="如：0.1235，最多可有四位整数与四位小数" v-model="temporaryItem.price_per_hour">
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
    import StatusSelect from "./StatusSelect";
    export default {
        name: "ImageForm",
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
            $(".ui.dropdown").dropdown();
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
                    min_disk: 20,
                    min_memory: 512,
                    machine_type: "0",
                    use_legacy_bios: "0",
                };
                return this;
            },
            edit: function (item) {
                this.clearDropdown();
                this.item = item;
                this.setDropdownValue(item.image_category_id);
                $(this.$refs.machineTypeSelect).dropdown("set selected", item.machine_type.toString());
                $(this.$refs.useLegacyBIOSSelect).dropdown("set selected", item.use_legacy_bios.toString());
                this.$refs.statusSelect.setValue(item.status !== null ? item.status.toString() : null);
                this.temporaryItem = _.cloneDeep(item);
            },
        }
    }
</script>

<style scoped>

</style>