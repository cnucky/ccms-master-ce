<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="two fields">
            <div class="ui field">
                <label class="required">{{ $t('common.name') }}</label>
                <input type="text" placeholder="" v-model="temporaryItem.name" required autofocus>
            </div>

            <div class="ui field">
                <label class="required">{{ $t('common.computeInstancePackageCategory') }}</label>
                <select class="ui search dropdown" v-model="temporaryItem.category_id" ref="select">
                    <option value="">请选择{{ $t('common.computeInstancePackageCategory') }}</option>
                    <option v-for="option in options" :value="option.id">{{ option.name }}</option>
                </select>
            </div>
        </div>

        <div class="three fields">
            <div class="ui field">
                <label>顺序</label>
                <input type="number" v-model="temporaryItem.order" placeholder="值大者靠前">
            </div>

            <div class="ui field">
                <label class="required">状态</label>
                <status-select ref="statusSelect" v-model="temporaryItem.status"></status-select>
            </div>

            <div class="ui field">
                <label>{{ $t('common.pricePerHour') }}</label>
                <input type="number" step="0.0001" max="9999.9999" min="0" placeholder="如：0.1235，最多可有四位整数与四位小数" v-model="temporaryItem.price_per_hour">
            </div>
        </div>

        <div class="ui section divider"></div>

        <div class="two fields">
            <div class="ui field">
                <label class="required">vCPU数量</label>
                <input type="number" min="1" v-model="temporaryItem.vCPU" placeholder="不可大于节点逻辑核心数量" required>
            </div>

            <div class="ui field">
                <label class="required">物理内存</label>
                <div class="ui right labeled input">
                    <input type="number" min="1" v-model="temporaryItem.memory" placeholder="单位：MiB" required>
                    <div class="ui basic label">
                        MiB
                    </div>
                </div>
            </div>
        </div>

        <div class="ui section divider"></div>

        <div class="four fields">
            <div class="ui field">
                <label>公网IPv4数量</label>
                <input type="number" min="0" v-model="temporaryItem.public_ipv4">
            </div>

            <div class="ui field">
                <label>公网IPv4网段大小</label>
                <input type="number" min="0" max="32" v-model="temporaryItem.public_ipv4_block_size">
            </div>

            <div class="ui field">
                <label>公网IPv6网段数量</label>
                <input type="number" min="0" v-model="temporaryItem.public_ipv6">
            </div>

            <div class="ui field">
                <label>公网IPv6网段大小</label>
                <input type="number" min="0" max="128" v-model="temporaryItem.public_ipv6_block_size">
            </div>
        </div>

        <div class="ui section divider"></div>

        <div class="ui field">
            <label>月流量</label>
            <div class="ui right labeled input">
                <input type="number" v-model="temporaryItem.traffic" placeholder="留空即为无限制，如有单独设置入网出网流量限制，将使用入网出网流量限制">
                <div class="ui basic label">
                    GiB
                </div>
            </div>
        </div>

        <div class="ui horizontal divider">
            Or
        </div>

        <div class="two fields">
            <div class="ui field">
                <label>入网月流量</label>
                <div class="ui right labeled input">
                    <input type="number" v-model="temporaryItem.inbound_traffic" placeholder="留空即无限制">
                    <div class="ui basic label">
                        GiB
                    </div>
                </div>
            </div>

            <div class="ui field">
                <label>出网月流量</label>
                <div class="ui right labeled input">
                    <input type="number" v-model="temporaryItem.outbound_traffic" placeholder="留空即无限制">
                    <div class="ui basic label">
                        GiB
                    </div>
                </div>
            </div>
        </div>

        <div class="two fields">
            <div class="ui field">
                <label>入网带宽</label>

                <div class="ui right labeled input">
                    <input type="number" v-model="temporaryItem.inbound_bandwidth" placeholder="为0即无限制">
                    <div class="ui basic label">
                        Mbps
                    </div>
                </div>
            </div>

            <div class="ui field">
                <label>出网带宽</label>
                <div class="ui right labeled input">
                    <input type="number" v-model="temporaryItem.outbound_bandwidth" placeholder="为0即无限制">
                    <div class="ui basic label">
                        Mbps
                    </div>
                </div>
            </div>
        </div>

        <div class="ui section divider"></div>

        <div class="ui field">
            <label>I/O权重</label>
            <input type="number" min="10" max="1000" placeholder="范围：10-1000，留空使用默认值" v-model="temporaryItem.io_weight">
        </div>

        <div class="two fields">
            <div class="ui field">
                <label>读取速度上限</label>

                <div class="ui right labeled input">
                    <input type="number" v-model="temporaryItem.read_bytes_sec" placeholder="为0即无限制">
                    <div class="ui basic label">
                        Bps
                    </div>
                </div>
            </div>

            <div class="ui field">
                <label>每秒读取操作上限</label>

                <div class="ui right labeled input">
                    <input type="number" v-model="temporaryItem.read_iops_sec" placeholder="为0即无限制">
                    <div class="ui basic label">
                        操作/秒
                    </div>
                </div>
            </div>
        </div>

        <div class="two fields">
            <div class="ui field">
                <label>写入速度上限</label>

                <div class="ui right labeled input">
                    <input type="number" v-model="temporaryItem.write_bytes_sec" placeholder="为0即无限制">
                    <div class="ui basic label">
                        Bps
                    </div>
                </div>
            </div>

            <div class="ui field">
                <label>每秒写入操作上限</label>
                <div class="ui right labeled input">
                    <input type="number" v-model="temporaryItem.write_iops_sec" placeholder="为0即无限制">
                    <div class="ui basic label">
                        操作/秒
                    </div>
                </div>
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
                    public_ipv4_block_size: 32,
                };
                return this;
            },
            edit: function (item) {
                this.clearDropdown();
                this.item = item;
                this.setDropdownValue(item.category_id.toString());
                this.$refs.statusSelect.setValue(item.status !== null ? item.status.toString() : null);
                this.temporaryItem = _.cloneDeep(item);
            },
        }
    }
</script>

<style scoped>

</style>