<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1>创建{{ $t('common.computeInstance') }}</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui padded dynamic-shadow segment">
                <div class="ui grid">
                    <div class="three wide middle aligned column">
                        <p class="section-name"><i class="map marker alternate icon dynamic-icon"></i> 区域 <span
                                style="color: red;">*</span></p>
                    </div>
                    <div class="thirteen wide column">
                        <div ref="regionSelect" class="ui basic medium dropdown button"
                             style="min-width: 130px; width: 150px;" v-bind:class="{disabled: isLoadingPackages}">
                            <input type="hidden" v-on:change="selectedRegionChanged">
                            <span class="text">选择地域</span>
                            <i class="dropdown icon"></i>
                            <div class="menu">
                                <div v-for="(availableRegion, index) in availableRegions" class="item"
                                     :data-value="index"><i :class="availableRegion.icon_class"></i> {{
                                    availableRegion.name }}
                                </div>
                            </div>
                        </div>

                        <slide-fade-transition>
                            <div id="zone-select" class="ui small basic buttons" v-if="selectedRegionIndex !== null">
                                <button v-for="availableZone in availableZones" class="ui button"
                                        :class="{ selected: instance.selectedZoneId === availableZone.id }"
                                        v-on:click="() => {instance.selectedZoneId = availableZone.id; selectedZone = availableZone;}" :key="availableZone.id"
                                        :disabled="isLoadingPackages">
                                    {{
                                    availableZone.name }}
                                </button>
                            </div>
                        </slide-fade-transition>
                    </div>
                </div>
            </div>
        </div>

        <slide-fade-transition>
            <div class="sixteen wide column" v-show="selectedZone">
                <div class="ui grid">
                    <div class="sixteen wide column">
                        <div class="ui padded dynamic-shadow segment" v-bind:class="{loading: isLoadingPackages}">
                            <div class="ui grid">
                                <div class="three wide column">
                                    <p class="section-name"><i class="cubes icon dynamic-icon"></i> 实例规格 <span
                                            style="color: red;">*</span>
                                    </p>
                                </div>
                                <div class="thirteen wide column">
                                    <div class="ui type-select-menu secondary pointing menu">
                                        <template v-for="(zoneAvailablePackage, zoneAvailablePackageIndex) in zoneAvailablePackages">
                                            <a
                                                    v-if="zoneAvailablePackage.packages.length"
                                                    class="item"
                                                    v-bind:class="{ active: instance.instancePackageType === zoneAvailablePackageIndex }"
                                                    v-on:click="instance.instancePackageType = zoneAvailablePackageIndex">
                                                {{ zoneAvailablePackage.name }}
                                            </a>
                                        </template>
                                    </div>

                                    <div style="padding: 0 14px 0 14px;">
                                        <div class="ui grid grid-title">
                                            <div class="ui two wide column">
                                                名称
                                            </div>
                                            <div class="ui three wide column">
                                                价格
                                            </div>
                                            <div class="ui one wide column"></div>
                                            <div class="ui ten wide column">
                                                <div class="ui grid">
                                                    <div class="ui four wide column">
                                                        vCPU
                                                    </div>
                                                    <div class="ui four wide column">
                                                        物理内存
                                                    </div>
                                                    <div class="ui four wide column">
                                                        带宽
                                                    </div>
                                                    <div class="ui four wide column">
                                                        流量
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="instance-package"
                                         v-for="(instancePackage, packageIndex) in zoneAvailablePackages[instance.instancePackageType].packages"
                                         v-bind:class="{ selected: instancePackage.id === instance.selectedPackageId, 'out-of-stock': !isPackageAvailable(instancePackage), }"
                                         v-on:click="() => {if (isPackageAvailable(instancePackage)) selectedPackageChanged(packageIndex, instancePackage.id)}">
                                        <div class="ui grid">
                                            <div class="ui two wide middle aligned column light-text light-on-selected-blue package-name">
                                                {{ instancePackage.name }}
                                            </div>
                                            <div class="ui three wide column light-on-selected-blue">
                                                <span class="light-text">￥{{ instancePackage.price_per_hour }}/hr</span>
                                                <br>
                                                <span>￥{{ (instancePackage.price_per_hour * 24 * 30).toFixed(2) }}/mo</span>
                                            </div>
                                            <div style="padding: 0 0 0 0;" class="ui one wide column">
                                                <div class="package-divider"></div>
                                            </div>
                                            <div class="ui ten wide middle aligned column">
                                                <div class="ui grid">
                                                    <div class="ui four wide middle aligned column light-on-selected-black">
                                                        {{ instancePackage.vCPU }} 个
                                                    </div>

                                                    <div class="ui four wide middle aligned column light-on-selected-black">
                                                        {{ instancePackage.memory }} MiB
                                                    </div>

                                                    <div class="ui four wide middle aligned column light-on-selected-black">
                                                        <template v-if="instancePackage.outbound_bandwidth > 0">
                                                            {{ instancePackage.outbound_bandwidth }} Mbps
                                                        </template>
                                                        <template v-else-if="instancePackage.outbound_bandwidth === 0">
                                                            按量收费
                                                        </template>
                                                        <template v-else>
                                                            无限制
                                                        </template>
                                                    </div>

                                                    <div class="ui four wide middle aligned column light-on-selected-black">
                                                        <template
                                                                v-if="!instancePackage.inbound_traffic && !instancePackage.outbound_traffic">
                                                                <span v-if="instancePackage.traffic > 0">
                                                                    <b>{{ instancePackage.traffic }}</b> GiB in&out
                                                                </span>
                                                            <span v-else-if="instancePackage.traffic === 0">
                                                                按量收费
                                                            </span>
                                                            <span v-else>
                                                                    <b>无限</b> 流量
                                                                </span>
                                                        </template>
                                                        <template v-else>
                                                                <span>
                                                                    <template v-if="instancePackage.inbound_traffic === 0">
                                                                        按量收费
                                                                    </template>
                                                                    <template v-else>
                                                                        <b>
                                                                            <template v-if="instancePackage.inbound_traffic > 0">
                                                                                {{ instancePackage.inbound_traffic }}
                                                                            </template>
                                                                            <template v-else>
                                                                                无限
                                                                            </template>
                                                                        </b>
                                                                        GiB in
                                                                    </template>
                                                                </span>
                                                            <br>
                                                            <span>
                                                                    <template v-if="instancePackage.outbound_traffic === 0">
                                                                        按量收费
                                                                    </template>
                                                                    <template v-else>
                                                                        <b>
                                                                            <template v-if="instancePackage.outbound_traffic > 0">
                                                                                {{ instancePackage.outbound_traffic }}
                                                                            </template>
                                                                            <template v-else>
                                                                                无限
                                                                            </template>
                                                                        </b>
                                                                        GiB out
                                                                    </template>
                                                                </span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sixteen wide column">
                        <div class="ui padded dynamic-shadow segment">
                            <div class="ui grid">
                                <div class="three wide column">
                                    <p class="section-name"><i class="hdd icon dynamic-icon"></i> 卷</p>
                                </div>
                                <div class="thirteen wide column">
                                    <div class="ui grid">
                                        <div class="row">
                                            <div class="four wide column">
                                                <div ref="volumeTypeSelect" class="ui basic fluid medium dropdown button">
                                                    <input type="hidden" value="0">
                                                    <span class="text">请选择卷类型</span>
                                                    <i class="dropdown icon"></i>
                                                    <div class="menu">
                                                        <div class="item selected" data-value="0">本地储存</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="five wide column">
                                                <div class="ui small right labeled input">
                                                    <input type="number" min="20" step="1" v-model="instance.storage_capacity">
                                                    <div class="ui basic label">
                                                        GiB
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="four wide column"></div>
                                            <div class="six wide middle aligned column">
                                                <div class="thirteen wide column">
                                                    <p>￥ <b>{{ volumePricePerHourString }}</b>/小时</p>
                                                    <p style="color: gray;">
                                                        ￥ {{ volumePricePerMonthString }}/月
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sixteen wide column">
                        <div class="ui padded dynamic-shadow segment">
                            <div class="ui grid">
                                <div class="three wide column">
                                    <p class="section-name"><i class="copy icon dynamic-icon"></i> 镜像 <span
                                            style="color: red;">*</span></p>
                                </div>
                                <div class="thirteen wide column">
                                    <div class="ui type-select-menu secondary pointing menu">
                                        <a class="item" v-bind:class="{ active: instance.imageType === 0 }"
                                           v-on:click="instance.imageType = 0">
                                            公共镜像
                                        </a>
                                        <a class="item disabled">
                                            自定义镜像
                                        </a>
                                        <a class="item" v-bind:class="{ active: instance.imageType === 2 }"
                                           v-on:click="instance.imageType = 2">
                                            自行安装
                                        </a>
                                    </div>

                                    <div v-show="instance.imageType === 0">
                                        <div class="ui grid">
                                            <div class="four wide column">
                                                <div ref="imageCategorySelect" class="ui basic fluid medium dropdown button">
                                                    <input type="hidden" v-on:change="selectedImageCategoryChanged">
                                                    <span class="text">请选择操作系统</span>
                                                    <i class="dropdown icon"></i>
                                                    <div class="menu">
                                                        <div v-for="(category, index) in zoneAvailableImages" class="item"
                                                             :data-value="index">
                                                            <i :class="category.icon_class"></i> {{ category.name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="six wide column">
                                                <image-select :images="selectedImageCategoryIndex === null ? [] : zoneAvailableImages[selectedImageCategoryIndex].images" v-model="instance.selectedImageId"></image-select>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-show="instance.imageType === 2">
                                        <p style="color: gray;">实例创建成功后，需自行安装所需操作系统。</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sixteen wide column">
                        <div class="ui padded dynamic-shadow segment">
                            <div class="ui grid">
                                <div class="three wide column">
                                    <p class="section-name"><i class="cogs icon dynamic-icon"></i> 实例属性</p>
                                </div>
                                <div class="thirteen wide column">
                                    <div class="ui grid">
                                        <div class="sixteen wide column">
                                            <div class="ui grid">
                                                <div class="three wide inline-input-label column">实例名称 <span
                                                        style="color: red;">*</span>：
                                                </div>
                                                <div class="six wide column">
                                                    <div class="ui small fluid input">
                                                        <input type="text" placeholder="" v-model="instance.name">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ui grid">
                                        <div class="sixteen wide column">
                                            <div class="ui grid">
                                                <div class="three wide inline-input-label column">描述：</div>
                                                <div class="six wide column">
                                                    <div class="ui form">
                                                        <textarea rows="3" placeholder="" v-model="instance.description"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ui grid">
                                        <div class="sixteen wide column">
                                            <div class="ui grid">
                                                <div class="three wide inline-input-label column">主机名：</div>
                                                <div class="six wide column">
                                                    <div class="ui small fluid input">
                                                        <input type="text" placeholder="" v-model="instance.hostname">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <slide-fade-transition>
                        <div class="sixteen wide column">
                            <div class="ui padded dynamic-shadow segment" v-bind:class="{disabled: instance.imageType !== 0}">
                                <div class="ui grid">
                                    <div class="three wide column">
                                        <p class="section-name"><i class="key icon dynamic-icon"></i> 实例登录信息</p>
                                    </div>
                                    <div class="thirteen wide column">
                                        <div class="ui grid">
                                            <div class="eleven wide column">
                                                <div class="ui type-select-menu secondary pointing menu">
                                                    <a class="item active">
                                                        密码
                                                    </a>
                                                    <a class="item disabled">
                                                        秘钥
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="sixteen wide column">
                                                <div class="ui warning message">
                                                    提示：实例创建成功并运行后，请第一时间修改密码
                                                </div>

                                                <div class="ui grid">
                                                    <div class="three wide inline-input-label column">用户名：</div>
                                                    <div class="six wide column">
                                                        <div class="ui small fluid input">
                                                            <input type="text" value="root/Administrator" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="ui grid">
                                                    <div class="three wide inline-input-label column">密码：
                                                    </div>
                                                    <div class="six wide column">
                                                        <div class="ui small fluid input">
                                                            <input type="text" value="由系统自动生成" disabled>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="ui grid">
                                                    <div class="three wide inline-input-label column">确认密码：
                                                    </div>
                                                    <div class="six wide column">
                                                        <div class="ui small fluid input">
                                                            <input type="text" value="由系统自动生成" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </slide-fade-transition>

                    <div class="sixteen wide column">
                        <div class="ui padded dynamic-shadow segment">
                            <div class="ui grid">
                                <div class="three wide column">
                                    <p class="section-name"><i class="shopping cart icon dynamic-icon"></i> 确认订单 <span
                                            style="color: red;">*</span>
                                    </p>
                                </div>
                                <div class="thirteen wide column">
                                    <div class="ui grid">
                                        <div class="sixteen wide column ui form">

                                            <div class="ui grid">
                                                <div class="three wide inline-input-label column">实例数量：</div>
                                                <div class="six wide column">
                                                    <div class="ui small fluid input">
                                                        <input type="number" min="1" max="30" step="1" placeholder=""
                                                               v-model="instance.count">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="ui grid">
                                                <div class="three wide right aligned column">总计：</div>
                                                <div class="thirteen wide column">
                                                    <p>￥ <span style="font-size: 25px;">{{ totalPricePerHourString }}</span>/小时</p>
                                                    <p style="color: gray;">
                                                        ￥ {{ totalPricePerMonthMonth }}/月
                                                        <br>
                                                        <br>
                                                        实际收费以账单为准
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="sixteen wide column">
                                            <div class="ui grid">
                                                <div class="three wide right aligned column"></div>
                                                <div class="six wide column">
                                                    <div class="ui checkbox">
                                                        <label>已阅读并同意服务条款</label>
                                                        <input type="checkbox" v-model="accept_tos">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ui sixteen wide column">
                                    <button v-if="enableSubmitButton !== true" type="button"
                                            class="ui positive fluid disabled button" disabled>{{ enableSubmitButton }}
                                    </button>
                                    <button v-else type="button" class="ui positive fluid button" v-on:click="createInstance"
                                            :disabled="isLoading" v-bind:class="{ loading: isLoading }">创建实例
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </slide-fade-transition>
    </div>
</template>

<script>
    import ImageSelect from "../../ComputeInstance/ImageSelect";

    export default {
        name: "Create",
        components: {ImageSelect},
        data: function () {

            function addZ(n) {
                return n < 10 ? '0' + n : '' + n;
            }

            var now = new Date();

            var dateString = parseInt(now.getFullYear().toString() + addZ(now.getMonth() + 1) + addZ(now.getDay()) + addZ(now.getHours()) + addZ(now.getMinutes()) + addZ(now.getSeconds())).toString(36);

            return {
                isLoading: false,

                isLoadingPackages: false,

                availableRegions: [],
                selectedRegionIndex: null,

                zoneAvailablePackages: [
                    {packages: []}
                ],

                zoneResourceCounters: {},

                zoneAvailableImages: [],

                selectedImageCategoryIndex: null,
                selectedPackageIndex: null,

                selectedZone: null,

                instance: {
                    selectedRegionId: null,
                    selectedZoneId: null,

                    instancePackageType: 0,
                    instancePackage: null,

                    selectedPackageId: null,

                    storage_capacity: 20,

                    imageType: 0,

                    selectedImageId: null,

                    name: "compute-instance-" + dateString,
                    description: "",
                    hostname: "ci-" + dateString,

                    password: "",
                    password_confirmation: "",

                    count: 1,
                },

                accept_tos: false,
            }
        },
        created: function () {
            axios
                .get(route('computeInstances.createOptions'), {}, {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.availableRegions = data.availableRegions;
                        // this.zoneAvailablePackages = data.zoneAvailablePackages;
                        this.zoneAvailableImages = data.zoneAvailableImages;
                    }
                })
            ;
        },
        mounted: function () {
            $(this.$refs.regionSelect).dropdown();
            $(this.$refs.volumeTypeSelect).dropdown();
            $(this.$refs.imageCategorySelect).dropdown();
            $(".ui.checkbox").checkbox();
        },
        computed: {
            availableZones: function () {
                if (this.selectedRegionIndex === null)
                    return [];
                return this.availableRegions[this.selectedRegionIndex].zones;
            },
            totalPricePerHour: function () {
                var selectedPackageIndex = this.selectedPackageIndex;
                var count = this.instance.count;
                try {
                    if (selectedPackageIndex !== null && count > 0) {
                        var pricePerHour = new Decimal(this.zoneAvailablePackages[this.instance.instancePackageType].packages[selectedPackageIndex].price_per_hour);
                        return pricePerHour.plus(this.volumePricePerHour).mul(count);
                    }
                } catch (e) {
                }
                return new Decimal("0");
            },
            totalPricePerHourString: function () {
                return this.totalPricePerHour.toString();
            },
            totalPricePerMonth: function () {
                return this.totalPricePerHour.mul(24).mul(30);
            },
            totalPricePerMonthMonth: function () {
                return this.totalPricePerMonth.toString();
            },
            volumePricePerHour: function () {
                if (Number.isNaN(parseInt(this.instance.storage_capacity)))
                    return new Decimal(0);
                var unitPrice = new Decimal(this.volumePricePerGiBPerHour);
                return unitPrice.mul(this.instance.storage_capacity);
            },
            volumePricePerMonth: function () {
                return this.volumePricePerHour.mul(24).mul(30);
            },
            volumePricePerHourString: function () {
                return this.volumePricePerHour.toString();
            },
            volumePricePerMonthString: function () {
                return this.volumePricePerMonth.toString();
            },
            volumePricePerGiBPerHour: function () {
                if (this.selectedZone) {
                    return this.selectedZone.storage_price_per_hour_per_gib;
                }
                return 0;
            },
            enableSubmitButton: function () {
                if (this.selectedRegionIndex === null)
                    return "请选择实例区域";
                if (this.instance.selectedZoneId === null)
                    return "请选择实例可用区";
                if (this.instance.selectedPackageId === null)
                    return "请选择实例规格";
                if (this.instance.imageType !== 2 && this.instance.selectedImageId === null)
                    return "请为实例选择镜像";
                if (!this.accept_tos)
                    return "同意服务条款后方可继续";
                return true;
            },
        },
        methods: {
            selectedRegionChanged: function (event) {
                this.selectedZone = null;
                this.instance.selectedZoneId = null;
                this.selectedRegionIndex = event.target.value;
                this.instance.selectedRegionId = this.availableRegions[this.selectedRegionIndex].id;
            },
            selectedImageCategoryChanged: function (event) {
                this.instance.selectedImageId = null;
                this.selectedImageCategoryIndex = event.target.value;
            },
            selectedPackageChanged: function (packageIndex, packageId) {
                this.selectedPackageIndex = packageIndex;
                this.instance.selectedPackageId = packageId;
            },
            selectedImageChanged: function (event) {
                this.instance.selectedImageId = event.target.value;
            },
            createInstance: function () {
                this.isLoading = true;
                axios.post(route('computeInstances.setupRequest'), this.instance, {vueInstance: this})
                    .then((response) => {
                        if (response.data.result) {
                            if (response.data.actuallyCreated === 0) {
                                if (response.data.exceptionRenderResult) {
                                    this.$globalErrnoHandler(response.data.exceptionRenderResult);
                                }
                                this.negativeFloatingMessage("暂时无法为分配所需资源，请稍后再试");
                            } else {
                                this.$router.push({name: "computeInstances.index"});
                                this.positiveFloatingMessage("已为" + response.data.actuallyCreated + "个实例成功分配资源，实例创建请求已提交");
                            }
                        } else {
                            this.$globalErrnoHandler(response.data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            },
            isPackageAvailable: function (instancePackage) {
                if (instancePackage.stocks[0].stock !== 0) {
                    if (this.zoneResourceCounters.hasOwnProperty("zone_total_memory_capacity")) {
                        return instancePackage.memory < parseInt(this.zoneResourceCounters.zone_total_memory_capacity);
                    } else {
                        return true;
                    }
                } else {
                    return false;
                }
            }
        },
        watch: {
            selectedZone: function (newValue, oldValue) {
                if (!newValue || oldValue && newValue.id === oldValue.id)
                    return;
                this.isLoadingPackages = true;
                this.$axiosGet(route("zones.availablePackages", [newValue.id]), (data) => {
                    this.zoneResourceCounters = data.zoneResourceCounters;
                    this.zoneAvailablePackages = data.packageCategories;
                }, () => {
                    this.isLoadingPackages = false;
                })
            }
        }
    }
</script>

<style scoped>
    .dynamic-shadow {
        box-shadow: 0 1px 2px #ebecec;
    }

    .dynamic-shadow:hover {
        box-shadow: 0 1px 5px #ccc;
    }

    .dynamic-shadow:hover .dynamic-icon {
        color: #00c1de;
    }

    .section-tips {
        padding-left: 21px;
    }

    .section-tips > li {
        color: #999;
        font-size: 12px;
        font-weight: 400;
    }

    #zone-select {
        margin-left: 10px;
    }

    #zone-select > button.selected:hover {
        color: #fff !important;
    }

    #zone-select > button:hover {
        color: #00c1de !important;
        border-color: #00c1de !important;

        -webkit-box-shadow: 0 0 0 1px #00c1de inset, 0 0 0 0 rgba(34, 36, 38, .15) inset;
        box-shadow: 0 0 0 1px #00c1de inset, 0 0 0 0 rgba(34, 36, 38, .15) inset;
    }

    #zone-select .selected {
        /* background-color: #21ba45 !important; */
        background-color: #00c1de !important;
        border-color: #00c1de !important;
        color: #fff !important;
    }

    .type-select-menu .active.item {
        color: #00c1de !important;
        border-color: #00c1de !important;
        font-weight: initial !important;
    }

    .inline-input-label {
        text-align: right;
        line-height: 2.4285em;
    }

    .ui.card.selected, .ui.card.selected:hover {
        -webkit-box-shadow: 0 0 0 1px #00c1de, 0 2px 4px 0 rgba(34, 36, 38, .15), 0 2px 10px 0 rgba(34, 36, 38, .25);
        box-shadow: 0 0 0 1px #00c1de, 0 2px 4px 0 rgba(34, 36, 38, .15), 0 2px 10px 0 rgba(34, 36, 38, .25);
    }

    .ui.card.selected > .content {
        border-top: 1px solid rgb(0, 193, 222);
    }

    .ui.card.selected > .content:first-child > * {
        color: #00c1de !important;
    }

    .ui.card .price.content {
    }

    i.full-size-icon {
        font-size: 100%;
    }

    /*
    .ui.dropdown {
        -webkit-box-shadow: 0 0 0 1px #00c1de inset;
        box-shadow: 0 0 0 1px #00c1de inset;
    }
    */

    .ui.dropdown .item.selected {
        /* color: #00c1de !important; */
    }

    .no-leave-enter-active {
        transition: all .3s ease;
    }

    .no-leave-enter {
        transform: translateX(10px);
        opacity: 0;
    }

    .slide-fade-enter-active {
        transition: all .3s ease;
    }

    .slide-fade-leave-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .slide-fade-enter, .slide-fade-leave-to {
        transform: translateX(10px);
        opacity: 0;
    }

    .slide-fade-move {
        transition: transform 1s;
    }

    .grid-title {
        color: rgb(178, 182, 195);
    }

    .instance-package {
        color: rgb(178, 182, 195);
        position: relative;
        font-weight: 500;
        border-radius: 4px;
        border-style: solid;
        border-color: rgb(212, 218, 231);
        border-width: 1px;
        padding: 14px;
        transition: color 0.2s ease 0s, border-color 0.2s ease 0s, box-shadow 0.2s ease 0s;
        margin-top: 16px;
        cursor: pointer;
        line-height: 1em;
    }

    .instance-package.selected {
        cursor: default;
    }

    .instance-package .light-text {
        font-size: 16px;
        font-weight: 500;
        color: rgb(21, 26, 45);
    }

    .instance-package:hover, .instance-package.selected {
        color: rgb(178, 182, 195);
        box-shadow: rgb(238, 238, 255) 0 0 8px 2px;
        border-color: #00c1de !important;
    }

    .instance-package.selected .light-on-selected-blue, .instance-package.selected .light-text {
        color: #00c1de !important;
    }

    .instance-package.selected .light-on-selected-black {
        color: rgb(74, 79, 98);
    }

    .instance-package .package-divider {
        background-color: rgb(212, 218, 231);
        width: 1px;
        height: 100%;
    }

    .instance-package.selected .package-divider {
        background-color: #00c1de;
    }

    .instance-package.out-of-stock:hover {
        cursor: no-drop;
        border-color: rgb(212, 218, 231) !important;
        box-shadow: none !important;
    }

    .instance-package.out-of-stock .package-name {
        text-decoration: line-through;
    }
</style>