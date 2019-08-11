<template>
    <form class="ui form" v-on:submit.prevent="attachVolume">
        <div class="ui two fields">
            <div class="ui field">
                <label>可连接的实例</label>
                <div ref="instanceSelect" class="ui selection dropdown" v-bind:class="{loading: isLoadingInstances}">
                    <input type="hidden" v-on:change="selectedInstanceId = $event.target.value">
                    <i class="dropdown icon"></i>
                    <div class="default text">实例</div>
                    <div class="menu">
                        <div v-for="availableInstance in availableInstances" class="item" :data-value="availableInstance.id">{{ availableInstance.name }}</div>
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

        <button type="submit" v-show="showSubmitButton" :disabled="isSubmitting">提交</button>
    </form>
</template>

<script>
    import PriceCalculator from "./PriceCalculator"

    export default {
        name: "AttachVolumeForm",
        mixins: [PriceCalculator],
        props: ["volume", "showSubmitButton"],
        data: function () {
            return {
                isLoadingInstances: false,
                isSubmitting: false,
                capacity: 20,
                bus: "0",
                selectedInstanceId: null,
                availableInstances: [],
            };
        },
        mounted: function () {
            $(this.$refs.busSelect).dropdown({
                showOnFocus: false,
            });
        },
        updated: function () {
            $(this.$refs.instanceSelect).dropdown({
                showOnFocus: false,
            });
        },
        methods: {
            attachVolume: function () {
                if (!this.selectedInstanceId) {
                    this.negativeFloatingMessage("请选择实例");
                    return;
                }

                this.isSubmitting = true;
                this.$emit("beforeSubmit");
                axios.post(route("localVolumes.operation.attach", {localVolume: this.volume.id, computeInstance: this.selectedInstanceId}), {bus: this.bus}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("卷连接请求已提交");
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
        watch: {
            volume: function (newValue, oldValue) {
                this.isLoadingInstances = true;
                if (newValue && newValue.hasOwnProperty("id")) {
                    axios.get(route("volumes.attachableInstances", [newValue.id]))
                        .then((response) => {
                            $(this.$refs.instanceSelect).dropdown("clear");
                            var data = response.data;
                            if (data.result) {
                                this.availableInstances = data.attachableInstances;
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
            }
        }
    }
</script>

<style scoped>

</style>