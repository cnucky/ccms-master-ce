<template>
    <form class="ui form" method="post" v-on:submit.prevent="$emit('submit')">
        <div class="ui two fields">
            <div class="ui field">
                <label class="required">名称</label>
                <input type="text" v-model="temporaryItem.name">
            </div>

            <div class="ui field">
                <label class="required">Host</label>
                <input type="text" v-model="temporaryItem.host">
            </div>
        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label class="required">{{ $t('common.region') }}</label>
                <select ref="regionSelect" class="ui search dropdown" v-model="region_id" v-on:change="regionChange">
                    <option value="">{{ $t('common.region') }}</option>
                    <option v-for="(availableRegion, index) in availableRegions" :value="availableRegion.id">{{ availableRegion.name }}</option>
                </select>
            </div>

            <div class="ui field">
                <label class="required">{{ $t('common.zone') }}</label>
                <select ref="zoneSelect" class="ui search dropdown" v-model="temporaryItem.zone_id">
                    <option value="">{{ $t('common.zone') }}</option>
                    <option v-for="availableZone in currentRegionZones" :value="availableZone.id">{{ availableZone.name }}</option>
                </select>
            </div>
        </div>

        <div class="ui field">
            <label class="required">客户端私钥</label>
            <input v-if="isEditing && !editingPrivateKey" placeholder="如需修改私钥，请点击此处" @focus="editPrivateKey">
            <autofocus-textarea ref="privateKeyTextarea" v-else v-model="temporaryItem.private_key" @blur="editedPrivateKey"></autofocus-textarea>
        </div>

        <div class="ui field">
            <label class="required">客户端证书</label>
            <input v-if="isEditing && !editingCertificate && !temporaryItem.certificate" placeholder="如需修改证书，请点击此处" @focus="editCertificate">
            <autofocus-textarea ref="certificateTextarea" v-else v-model="temporaryItem.certificate" @blur="editedCertificate"></autofocus-textarea>
        </div>

        <div class="ui field">
            <label>描述</label>
            <textarea v-model="temporaryItem.description"></textarea>
        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label>保留内存（默认保留2048MiB）</label>
                <div class="ui right labeled input">
                    <input type="number" placeholder="2048" min="1024" v-model="temporaryItem.reserved_memory_capacity_in_mib_unit">
                    <div class="ui basic label">
                        MiB
                    </div>
                </div>
            </div>
            <div class="ui field">
                <label>保留储存空间（默认保留4GiB）</label>
                <div class="ui right labeled input">
                    <input type="number" placeholder="4" min="4" v-model="temporaryItem.reserved_disk_capacity_in_gib_unit">
                    <div class="ui basic label">
                        GiB
                    </div>
                </div>
            </div>
        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label>实例上限（留空将由系统自动根据节点的资源情况进行决定）</label>
                <input type="number" min="0" placeholder="自动" v-model="temporaryItem.max_instance">
            </div>
            <div class="ui field">
                <label>状态</label>
                <select ref="statusSelect" class="ui dropdown" v-model="temporaryItem.status">
                    <option value="1">启用</option>
                    <option value="2">停用</option>
                </select>
            </div>
        </div>

        <div ref="regenerateToken" v-show="isEditing" class="ui checkbox">
            <label>更新Token</label>
            <input type="checkbox" v-model="temporaryItem.regenerateToken">
        </div>

        <div v-if="showSubmitButton" class="ui field" style="margin-top: 20px;">
            <button class="ui small teal fluid button" type="submit" v-bind:class="{loading: isSubmitting}" :disalbed="isSubmitting">提交</button>
        </div>
        <button ref="submitButton" type="submit" class="hidden" :disalbed="isSubmitting"></button>
    </form>
</template>

<script>
    import AutofocusTextarea from "./AutofocusTextarea";
    export default {
        name: "ComputeNodeForm",
        components: {AutofocusTextarea},
        props: ["isSubmitting", "computeNode2Edit", "availableRegions", "availableZones", "isEditing", "showSubmitButton"],
        data: function () {
            return {
                computeNode: {
                },
                temporaryItem: {},
                region_id: null,
                editingPrivateKey: false,
                editingCertificate: false,
            }
        },
        mounted: function () {
            $(this.$refs.regionSelect).dropdown();
            $(this.$refs.zoneSelect).dropdown();
            $(this.$refs.statusSelect).dropdown();
            $(this.$refs.regenerateToken).checkbox();
            if (this.computeNode2Edit) {
                this.edit(this.computeNode2Edit);
            }
        },
        computed: {
            currentRegionZones: function () {
                var foundZones = [];
                var regionId = parseInt(this.region_id);
                if (!Number.isNaN(regionId)) {
                    for (var i in this.availableZones) {
                        if (this.availableZones[i].region_id === regionId)
                            foundZones.push(this.availableZones[i]);
                    }
                }

                return foundZones;
            }
        },
        methods: {
            create: function () {
                this.computeNode = {};
                this.temporaryItem = {};
                $(this.$refs.regionSelect).dropdown("clear");
                $(this.$refs.zoneSelect).dropdown("clear");
                $(this.$refs.statusSelect).dropdown("set selected", "1");
            },
            edit: function (item) {
                this.editingPrivateKey = false;
                this.editingCertificate = false;
                var zone_id = item.zone_id;
                if (this.availableZones.hasOwnProperty(zone_id)) {
                    var zone = this.availableZones[zone_id];
                    this.region_id = zone.region_id;

                    $(this.$refs.regionSelect).dropdown("set selected", this.region_id);
                    $(this.$refs.zoneSelect).dropdown("set selected", zone_id);
                } else {
                    this.region_id = null;
                }

                this.temporaryItem = _.cloneDeep(item);
                this.computeNode = item;
                if (item) {
                    $(this.$refs.statusSelect).dropdown("set selected", item.status !== null ? item.status.toString() : "");
                }
            },
            editPrivateKey: function () {
                this.editingPrivateKey = true;
                // this.$refs.privateKeyTextarea.focus();
            },
            editedPrivateKey: function () {
                if (!this.temporaryItem.private_key)
                    this.editingPrivateKey = false;
            },
            editCertificate: function () {
                this.editingCertificate = true;
                // this.$refs.certificateTextarea.focus();
            },
            editedCertificate: function () {
                if (!this.temporaryItem.certificate)
                    this.editingCertificate = false;
            },
            regionChange: function () {
                $(this.$refs.zoneSelect).dropdown('clear');
            },
        }
    }
</script>

<style scoped>

</style>