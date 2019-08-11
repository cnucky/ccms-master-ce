<template>
    <form class="ui form" v-on:submit.prevent="newVolume">
        <div class="ui field" v-show="!targetInstance">
            <label>实例</label>
            <div ref="instanceSelect" class="ui selection dropdown" v-bind:class="{loading: isLoadingInstances}">
                <input type="hidden" v-on:change="selectedInstanceId = $event.target.value">
                <i class="dropdown icon"></i>
                <div class="default text">实例</div>
                <div class="menu">
                    <div v-for="availableInstance in availableInstances" class="item" :data-value="availableInstance.id" v-on:click="selectedInstance = availableInstance">{{ availableInstance.name }}</div>
                </div>
            </div>
        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label>容量</label>
                <div class="ui right labeled input">
                    <input type="number" min="1" v-model="capacity">
                    <div class="ui basic label">
                        GiB
                    </div>
                </div>
            </div>

            <div class="ui field">
                <label>总线</label>
                <select ref="busSelect" class="ui dropdown" v-model="bus">
                    <option value="0">virtio</option>
                    <option value="1">scsi</option>
                    <option value="2">sata</option>
                    <option value="3">ide</option>
                </select>
            </div>
        </div>

        <div class="ui horizontal divider">价格</div>

        <div class="two fields">
            <div class="ui field">
                <div class="ui right labeled input">
                    <input type="text" readonly :value="volumePricePerHourString">
                    <div class="ui basic label">
                        CNY/小时
                    </div>
                </div>
            </div>
            <div class="ui field">
                <div class="ui right labeled input">
                    <input type="text" readonly :value="volumePricePerMonthString">
                    <div class="ui basic label">
                        CNY/月
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" v-show="showSubmitButton" :disabled="isSubmitting">提交</button>
    </form>
</template>

<script>
    import PriceCalculator from "./PriceCalculator"

    export default {
        name: "NewVolumeForm",
        mixins: [PriceCalculator],
        props: ["unitPrice", "targetInstance", "showSubmitButton"],
        data: function () {
            var selectedInstanceId = null;
            if (this.targetInstance) {
                selectedInstanceId = this.targetInstance.id;
            }

            return {
                isLoadingInstances: false,
                isSubmitting: false,
                capacity: 20,
                bus: "0",
                selectedInstance: null,
                selectedInstanceId: selectedInstanceId,
                availableInstances: [],
            };
        },
        mounted: function () {
            $(this.$refs.busSelect).dropdown();
        },
        updated: function () {
            $(this.$refs.instanceSelect).dropdown({
                showOnFocus: false,
            });
        },
        created: function () {
            if (!this.targetInstance) {
                this.isLoadingInstances = true;
                axios.get(route("computeInstances.availableInstances"), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.availableInstances = data.availableInstances;
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isLoadingInstances = false;
                    })
                ;
            }
        },
        methods: {
            newVolume: function () {
                this.isSubmitting = true;
                this.$emit("beforeSubmit");
                axios.post(route("localVolumes.new", [this.selectedInstanceId]), {capacity: this.capacity, bus: this.bus, instance: this.selectedInstanceId}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("卷创建请求已提交");
                            this.$emit("operationRequestCreated", data.operationRequest);
                            this.$emit("success");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.$emit("complete");
                        this.isSubmitting = false;
                    })
                ;
            },
        },
        computed: {
            volumeCapacity: function () {
                return this.capacity;
            },
            volumePricePerGiBPerHour: function () {
                if (this.unitPrice) {
                    return this.unitPrice;
                } else if (this.selectedInstance) {
                    return this.selectedInstance.node.zone.storage_price_per_hour_per_gib;
                }
                return "0";
            }
        }
    }
</script>

<style scoped>

</style>