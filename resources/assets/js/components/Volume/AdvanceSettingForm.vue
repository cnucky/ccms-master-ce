<template>
    <form class="ui form" v-on:submit.prevent="changeBus">
        <div class="ui warning message" style="display: block;">
            注意：使用virtio或scsi总线，可能需要为操作系统安装相关驱动程序
        </div>

        <div class="ui field">
            <label>总线</label>
            <select ref="busSelect" class="ui dropdown" v-model="busChange2">
                <option value="0">virtio</option>
                <option value="1">scsi</option>
                <option value="2">sata</option>
                <option value="3">ide</option>
            </select>
        </div>

        <div class="ui field">
            <div class="ui checkbox">
                <input id="save-and-apply" type="checkbox" v-model="saveAndApply">
                <label for="save-and-apply">保存并应用</label>
            </div>
        </div>

        <button type="submit" v-show="showSubmitButton" :disabled="isSubmitting">提交</button>
    </form>
</template>

<script>
    export default {
        name: "AdvanceSettingForm",
        props: ["isAdmin", "volume", "showSubmitButton"],
        data: function () {
            return {
                isSubmitting: false,
                busChange2: "0",
                saveAndApply: true,
            };
        },
        mounted: function () {
            $(this.$refs.busSelect).dropdown({
                showOnFocus: false
            });
        },
        methods: {
            resetBusChange2: function () {
                if (this.volume && this.volume.hasOwnProperty("bus")) {
                    this.busChange2 = this.volume.bus.toString();
                } else {
                    this.busChange2 = "0";
                }

                $(this.$refs.busSelect).dropdown("set selected", this.busChange2);
            },
            changeBus: function () {
                this.isSubmitting = true;
                this.$emit("beforeSubmit");
                axios.patch(route((this.isAdmin ? "admin." : "") + "localVolumes.operation.changeBus", [this.volume.id]), {bus: this.busChange2, saveAndApply: this.saveAndApply}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            if (data.hasOwnProperty("operationRequest")) {
                                this.$emit("operationRequestCreated", data.operationRequest);
                                this.positiveFloatingMessage(this.$t("common.operationRequestSubmitted"));
                            } else {
                                this.$emit("saved", this.busChange2);
                                this.positiveFloatingMessage(this.$t("common.saveSuccessfully"));
                            }
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
                this.resetBusChange2();
            }
        },
    }
</script>

<style scoped>

</style>