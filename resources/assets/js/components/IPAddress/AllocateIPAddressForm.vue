<template>
    <form class="ui form" v-on:submit.prevent="attachVolume">
        <div class="ui two fields">
            <div class="ui field">
                <label>实例</label>
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
                <label>数量</label>
                <input type="number" min="1" v-model="allocateNum">
            </div>
        </div>

        <button type="submit" v-show="showSubmitButton" :disabled="isSubmitting">提交</button>
    </form>
</template>

<script>
    export default {
        name: "AllocateIPAddressForm",
        props: ["ipAssignment", "modelRouteName", "showSubmitButton"],
        data: function () {
            return {
                isLoadingInstances: false,
                isSubmitting: false,
                capacity: 20,
                selectedInstanceId: null,
                availableInstances: [],

                allocateNum: 1,
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
        created: function () {
            this.isLoadingInstances = true;
            axios.get(route("computeInstances.availableInstances"))
                .then((response) => {
                    $(this.$refs.instanceSelect).dropdown("clear");
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
        },
        methods: {
            allocate: function () {
                if (!this.selectedInstanceId) {
                    this.negativeFloatingMessage("请选择实例");
                    return;
                }

                this.isSubmitting = true;
                this.$emit("beforeSubmit");
                axios.post(route(this.modelRouteName + ".allocate", [this.selectedInstanceId]), {allocateNum: this.allocateNum}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("IP申请成功");
                            this.$emit("operationRequestCreated", data.operationRequest);
                            this.$emit("success");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.$emit("complete");
                        this.isSubmitting = false;
                    })
                ;
            },
        },
        watch: {
        }
    }
</script>

<style scoped>

</style>