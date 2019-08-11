<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">CD-ROM</h3>
            <table class="ui celled unstackable table">
                <thead>
                <tr>
                    <th class="one wide">ID</th>
                    <th class="thirteen wide">介质</th>
                    <th style="width: 200px; min-width: 200px;"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(cdrom, index) in instance.cdroms">
                    <td>{{ index + 1 }}</td>
                    <td v-if="changingCDROMIndex === index" style="text-align: center">
                        <div class="ui active small inline loader"></div>
                    </td>
                    <td v-else-if="cdrom.media">
                        <i :class="cdrom.media.category.icon_class"></i> {{ cdrom.media.category.name }} {{ cdrom.media.name }} <span style="color: gray;">[{{ cdrom.media.internal_name }}]</span>
                    </td>
                    <td v-else>
                        无
                    </td>
                    <td>
                        <button class="ui tiny blue button" v-on:click="showChangeCDROMMediaModal(index)" :disabled="isChangingMedia">更改介质</button>
                        <button class="ui tiny red button" v-on:click="ejectCDROMMedia(index)" :disabled="!cdrom.media || isChangingMedia">弹出介质</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="sixteen wide column">
            <div class="ui divider"></div>
        </div>

        <div class="sixteen wide column">
            <h3 class="ui header">Floppy</h3>
            <table class="ui celled unstackable table">
                <thead>
                <tr>
                    <th class="one wide">ID</th>
                    <th class="thirteen wide">介质</th>
                    <th style="width: 200px; min-width: 200px;"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(floppy, index) in instance.floppies">
                    <td>{{ index + 1 }}</td>
                    <td v-if="changingFloppyIndex === index" style="text-align: center">
                        <div class="ui active small inline loader"></div>
                    </td>
                    <td v-else-if="floppy.media">
                        <i :class="floppy.media.category.icon_class"></i> {{ floppy.media.category.name }} {{ floppy.media.name }} <span style="color: gray;">[{{ floppy.media.internal_name }}]</span>
                    </td>
                    <td v-else>
                        无
                    </td>
                    <td>
                        <button class="ui tiny blue button" v-on:click="showChangeFloppyMediaModal(index)" :disabled="isChangingMedia">更改介质</button>
                        <button class="ui tiny red button" v-on:click="ejectFloppyMedia(index)" :disabled="!floppy.media || isChangingMedia">弹出介质</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <form-modal ref="cdromFormModal" :is-loading="isLoading" customHeader="请选择CDROM介质" :no-scroll="true" v-on:submit="changeCDROMMedia">
            <form class="ui form" v-bind:class="{loading: isRefreshingMedia}" v-on:submit.prevent="changeCDROMMedia">
                <div ref="publicISOCategorySelector" class="ui two fields">
                    <div class="ui field">
                        <div class="ui selection dropdown">
                            <input type="hidden" v-on:change="selectedPublicISOCategoryChanged">
                            <i class="dropdown icon"></i>
                            <div class="default text">请选择类型</div>
                            <div class="menu">
                                <div v-for="(publicISOCategory, index) in availablePublicISOs" class="item" :data-value="index"><i :class="publicISOCategory.icon_class"></i> {{ publicISOCategory.name }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="ui field">
                        <div ref="publicISOSelector" class="ui selection dropdown">
                            <input type="hidden" v-on:change="selectedPublicISOChanged">
                            <i class="dropdown icon"></i>
                            <div class="default text">请选择介质</div>
                            <div class="menu">
                                <template v-if="availablePublicISOs && availablePublicISOs.hasOwnProperty(selectedPublicISOCategoryIndex)">
                                    <div v-for="publicISO in availablePublicISOs[selectedPublicISOCategoryIndex].public_isos" class="item" :data-value="publicISO.id">{{ publicISO.name }}</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" style="display: none;">提交</button>
            </form>
        </form-modal>

        <form-modal ref="floppyFormModal" :is-loading="isLoading" customHeader="请选择软盘介质" :no-scroll="true" v-on:submit="changeFloppyMedia">
            <form class="ui form" v-bind:class="{loading: isRefreshingMedia}" v-on:submit.prevent="changeFloppyMedia">
                <div ref="publicFloppyCategorySelector" class="ui two fields">
                    <div class="ui field">
                        <div class="ui selection dropdown">
                            <input type="hidden" v-on:change="selectedPublicFloppyCategoryChanged">
                            <i class="dropdown icon"></i>
                            <div class="default text">请选择类型</div>
                            <div class="menu">
                                <div v-for="(publicFloppyCategory, index) in availablePublicFloppies" class="item" :data-value="index"><i :class="publicFloppyCategory.icon_class"></i> {{ publicFloppyCategory.name }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="ui field">
                        <div ref="publicFloppySelector" class="ui selection dropdown">
                            <input type="hidden">
                            <i class="dropdown icon"></i>
                            <div class="default text">请选择介质</div>
                            <div class="menu">
                                <template v-if="availablePublicFloppies && availablePublicFloppies.hasOwnProperty(selectedPublicFloppyCategoryIndex)">
                                    <div v-for="publicFloppy in availablePublicFloppies[selectedPublicFloppyCategoryIndex].public_floppies" class="item" :data-value="publicFloppy.id" v-on:click="targetFloppy.mediaId = publicFloppy.id">{{ publicFloppy.name }}</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" style="display: none;">提交</button>
            </form>
        </form-modal>
    </div>
</template>

<script>
    import ShowUtils from "./ShowUtils";

    export default {
        name: "VirtualMedia",
        mixins: [ShowUtils],
        props: ["instance", "operationRoutePrefix"],
        data: function () {
            return {
                isLoading: false,

                isRefreshingMedia: false,
                refreshPublicISOs: true,
                refreshPublicFloppies: true,

                availablePublicISOs: [],
                availablePublicFloppies: [],

                selectedPublicISOCategoryIndex: null,
                selectedPublicFloppyCategoryIndex: null,

                targetCDROM: {
                    index: null,
                    mediaId: null,
                },
                targetFloppy:  {
                    index: null,
                    mediaId: null,
                },
            }
        },
        mounted: function () {
        },
        updated: function () {
            $(".ui.selection.dropdown").dropdown({showOnFocus: false});
        },
        methods: {
            showChangeCDROMMediaModal: function (index) {
                if (this.refreshPublicISOs)
                    this.updateAvailablePublicISOs();
                this.targetCDROM.index = index;
                $(".ui.selection.dropdown").dropdown("clear");
                this.$refs.cdromFormModal.show();
            },
            showChangeFloppyMediaModal: function (index) {
                if (this.refreshPublicFloppies)
                    this.updateAvailablePublicFloppies();
                this.targetFloppy.index = index;
                $(".ui.selection.dropdown").dropdown("clear");
                this.$refs.floppyFormModal.show();
            },
            changeCDROMMedia: function () {
                this.isLoading = true;
                axios.post(route(this.operationRoutePrefix + "computeInstances.operation.cdroms.changeMedia", [this.instance.id]), this.targetCDROM, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$emit("operationRequestCreated", data.operationRequest);
                            this.positiveFloatingMessage("请求已提交");
                            this.$refs.cdromFormModal.hideIfNotStay(null);
                        } else {
                            this.negativeFloatingMessage(data.message);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            },
            changeFloppyMedia: function () {
                this.isLoading = true;
                axios.post(route(this.operationRoutePrefix + "computeInstances.operation.floppies.changeMedia", [this.instance.id]), this.targetFloppy, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$emit("operationRequestCreated", data.operationRequest);
                            this.positiveFloatingMessage("请求已提交");
                            this.$refs.floppyFormModal.hideIfNotStay(null);
                        } else {
                            this.negativeFloatingMessage(data.message);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            },
            ejectCDROMMedia: function (index) {
                this.targetCDROM.index = index;
                this.targetCDROM.mediaId = null;
                this.changeCDROMMedia();
            },
            ejectFloppyMedia: function (index) {
                this.targetFloppy.index = index;
                this.targetFloppy.mediaId = null;
                this.changeFloppyMedia();
            },
            selectedPublicISOCategoryChanged: function (event) {
                $(this.$refs.publicISOSelector).dropdown('clear');
                this.selectedPublicISOCategoryIndex = event.target.value;
            },
            selectedPublicFloppyCategoryChanged: function (event) {
                $(this.$refs.publicFloppySelector).dropdown('clear');
                this.selectedPublicFloppyCategoryIndex = event.target.value;
            },
            selectedPublicISOChanged: function (event) {
                this.targetCDROM.mediaId = event.target.value;
            },
            selectedPublicFloppyChanged: function (event) {
                this.targetFloppy.mediaId = event.target.value;
            },
            updateAvailablePublicISOs: function () {
                this.refreshing = true;
                axios.get(route('client.publicISOs.index'), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.refreshPublicISOs = false;
                            this.availablePublicISOs = data.categories;
                        } else {
                            this.refreshPublicISOs = true;
                            this.negativeFloatingMessage(data.message);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.refreshing = false;
                    })
                ;
            },
            updateAvailablePublicFloppies: function () {
                this.refreshing = true;
                axios.get(route('client.publicFloppies.index'), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.refreshPublicFloppies = false;
                            this.availablePublicFloppies = data.categories;
                        } else {
                            this.refreshPublicFloppies = true;
                            this.negativeFloatingMessage(data.message);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.refreshing = false;
                    })
                ;
            }
        },
        computed: {
            isChangingMedia: function () {
                return this.isOperationRequestTypeExists(8);
            },
            changeMediaRequestData: function () {
                var operationRequest = this.isChangingMedia;
                if (operationRequest) {
                    return JSON.parse(operationRequest.data);
                }
                return null;
            },
            changingCDROMIndex: function () {
                var data = this.changeMediaRequestData;
                if (data === null)
                    return -1;
                if (data.diskDeviceCode === 1)
                    return data.deviceIndex;
                return -1;
            },
            changingFloppyIndex: function () {
                var data = this.changeMediaRequestData;
                if (data === null)
                    return -1;
                if (data.diskDeviceCode === 2)
                    return data.deviceIndex;
                return -1;
            }
        }
    }
</script>

<style scoped>

</style>