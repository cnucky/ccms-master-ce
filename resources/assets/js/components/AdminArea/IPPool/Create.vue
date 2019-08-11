<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">创建{{ modelName }}</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded top attached segment">
                <form class="ui form" v-on:submit.prevent="importFromCIDR">
                    <div class="ui field">
                        <label>通过CIDR导入</label>
                        <input type="text" v-model="autoImport.cidr" required>
                    </div>
                    <div class="ui field" v-if="modelInternalName === 'ipv4Pools'">
                        <div class="ui checkbox">
                            <input id="auto_remove_unusable_address" type="checkbox" v-model="autoImport.autoRemoveUnusableAddress">
                            <label for="auto_remove_unusable_address">自动去除网络、网关与广播地址</label>
                        </div>
                    </div>
                    <button type="submit" class="ui small teal button" style="display: block; margin-left: auto;" v-bind:class="{loading: isImporting}">导入</button>
                </form>
            </div>
            <div class="ui very padded bottom attached no-shadow segment" v-bind:class="{loading: isLoading}">
                <div class="ui warning message" v-if="Object.keys(messages).length">
                    <ul v-for="message in messages">
                        <li>{{ message }}</li>
                    </ul>
                </div>

                <form class="ui form" v-on:submit.prevent="store">
                    <div class="two fields">
                        <div class="ui field">
                            <label class="required">首可用IP</label>
                            <input type="text" required autofocus v-model="pool.human_readable_first_usable_ip" v-on:blur="validate">
                        </div>
                        <div class="ui field">
                            <label class="required">末可用IP</label>
                            <input type="text" required v-model="pool.human_readable_last_usable_ip" v-on:blur="validate">
                        </div>
                    </div>

                    <div class="two field">
                        <div class="two fields">
                            <div class="ui field">
                                <label class="required">掩码长度</label>
                                <input type="number" min="0" required v-model="pool.network_bits" v-on:blur="validate">
                            </div>
                            <div class="ui field">
                                <label>网关</label>
                                <input type="text" v-model="pool.human_readable_gateway">
                            </div>
                        </div>
                    </div>

                    <div class="two fields">
                        <div class="ui field">
                            <label class="required">子网分配掩码长度</label>
                            <input type="number" min="0" v-model="pool.subnet_network_bits" v-on:blur="validate">
                        </div>
                        <div class="ui field" v-bind:class="{error: total_subnet === 0}">
                            <label>预计可分配数量</label>
                            <input type="number" readonly v-model="total_subnet">
                        </div>
                    </div>

                    <div class="two fields">
                        <div class="ui field">
                            <label class="required">可为新实例分配</label>
                            <select class="ui dropdown" v-model="pool.assign_for_new_instance">
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
                        </div>
                        <div class="ui field">
                            <label class="required">可作为弹性IP分配</label>
                            <select class="ui dropdown" v-model="pool.assign_for_extra_ip">
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
                        </div>
                    </div>

                    <div class="two fields">
                        <div class="ui field">
                            <label class="required">类型</label>
                            <select class="ui dropdown" v-model="pool.type">
                                <option value="0">公网</option>
                                <option value="1">内网</option>
                            </select>
                        </div>
                        <div class="ui field">
                            <label class="required">状态</label>
                            <select class="ui dropdown" v-model="pool.status">
                                <option value="0">正常</option>
                                <option value="1">停用</option>
                            </select>
                        </div>
                    </div>

                    <div class="ui field">
                        <label>{{ $t('common.pricePerHour') }}</label>
                        <input type="number" step="0.0001" max="9999.9999" min="0" placeholder="如：0.1235，最多可有四位整数与四位小数" v-model="pool.price_per_hour">
                    </div>

                    <div class="ui field">
                        <label>描述</label>
                        <textarea v-model="pool.description"></textarea>
                    </div>

                    <div class="ui divider"></div>

                    <div class="ui field">
                        <div class="ui checkbox">
                            <input id="stay" type="checkbox" v-model="stay">
                            <label for="stay">保存成功后留在此页面</label>
                        </div>
                    </div>

                    <button type="submit" class="ui teal fluid button" style="display: block; margin-left: auto;">保存</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import ImportFromCIDR from "./ImportFromCIDR";
    import Error2Message from "./Errno2Message";

    export default {
        name: "Create",
        mixins: [ImportFromCIDR, Error2Message],
        props: ["modelInternalName", "modelName", "defaultSubnetBits"],
        data: function () {
            return {
                isLoading: false,
                stay: false,

                messages: [],

                total_subnet: 0,

                pool: this.initPool()
            };
        },
        mounted: function () {
            $(".ui.dropdown").dropdown();
            $(".ui.checkbox").checkbox();
        },
        methods: {
            store: function () {
                this.isLoading = true;
                axios.post(route(this.modelInternalName + ".store"), this.pool, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("地址池创建成功");
                            if (this.stay) {
                                this.pool = this.initPool();
                            } else {
                                this.$router.push({name: this.modelInternalName + ".index"});
                            }
                        } else {
                            this.errno2Message(data);
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
            validate: function () {
                if (!this.pool.human_readable_first_usable_ip.length || !this.pool.human_readable_last_usable_ip.length || !this.pool.network_bits.toString().length || !this.pool.subnet_network_bits.toString().length)
                    return;
                axios.post(route(this.modelInternalName + ".validate"), this.pool)
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.messages = [];

                            this.total_subnet = data.values.total_subnet;
                            if (data.isAligned) {
                                this.messages.push("根据当前所设定的子网长度，首末可用IP将会自动对齐到" + data.values.human_readable_first_usable_ip + " - " + data.values.human_readable_last_usable_ip);
                            }
                        }
                    })
                    .catch((error) => {
                        this.total_subnet = 0;
                    })
                ;
            },
            initPool: function () {
                $(".ui.dropdown").dropdown("clear");
                return {
                    human_readable_first_usable_ip: "",
                    human_readable_last_usable_ip: "",
                    network_bits: "",
                    human_readable_gateway: "",
                    subnet_network_bits: this.defaultSubnetBits,
                    type: "0",
                    status: "0",
                    description: "",
                };
            }
        }
    }
</script>

<style scoped>

</style>