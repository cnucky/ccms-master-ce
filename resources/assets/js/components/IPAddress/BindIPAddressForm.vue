<template>
    <form class="ui form" v-on:submit.prevent="attachVolume">
        <div class="ui field">
            <label>可绑定的实例</label>
            <div ref="instanceSelect" class="ui selection dropdown" v-bind:class="{loading: isLoadingInstances}">
                <input type="hidden" v-on:change="selectedInstanceId = $event.target.value">
                <i class="dropdown icon"></i>
                <div class="default text">实例</div>
                <div class="menu">
                    <div v-for="availableInstance in availableInstances" class="item" :data-value="availableInstance.id">{{ availableInstance.name }}</div>
                </div>
            </div>
        </div>

        <button type="submit" v-show="showSubmitButton" :disabled="isSubmitting">提交</button>
    </form>
</template>

<script>
    export default {
        name: "BindIPAddressForm",
        props: ["ipAssignment", "modelRouteName", "showSubmitButton"],
        data: function () {
            return {
                isLoadingInstances: false,
                isSubmitting: false,
                capacity: 20,
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
            bind: function () {
                if (!this.selectedInstanceId) {
                    this.negativeFloatingMessage("请选择实例");
                    return;
                }

                this.isSubmitting = true;
                this.$emit("beforeSubmit");
                axios.post(route(this.modelRouteName + ".bind", [this.ipAssignment.id, this.selectedInstanceId]), {bus: this.bus}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("IP绑定请求已提交");
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
            ipAssignment: function (newValue, oldValue) {
                this.isLoadingInstances = true;
                if (newValue && newValue.hasOwnProperty("id")) {
                    axios.get(route(this.modelRouteName + ".bindableInstances", [newValue.id]))
                        .then((response) => {
                            $(this.$refs.instanceSelect).dropdown("clear");
                            var data = response.data;
                            if (data.result) {
                                this.availableInstances = data.bindableInstances;
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