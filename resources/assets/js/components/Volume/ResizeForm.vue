<template>
    <form class="ui form" v-on:submit.prevent="resize">
        <div class="ui field">
            <label>新容量</label>
            <div class="ui right labeled input">
                <input type="number" :min="min" v-model="newCapacity">
                <div class="ui basic label">
                    GiB
                </div>
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
        name: "ResizeForm",
        mixins: [PriceCalculator],
        props: ["isAdmin", "volumePricePerGiBPerHour", "volume", "showSubmitButton"],
        data: function () {
            return {
                isSubmitting: false,
                newCapacity: 0,
            };
        },
        created: function () {
            this.resetNewCapacity();
        },
        methods: {
            resize: function () {
                this.isSubmitting = true;
                this.$emit("beforeSubmit");
                axios.post(route((this.isAdmin ? "admin." : "") + "localVolumes.operation.resize", [this.volume.id]), {newCapacity: this.newCapacity}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("扩容请求已提交");
                            this.$emit("operationRequestCreated", data.operationRequest);
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
            resetNewCapacity: function () {
                var currentCapacity = 0;
                if (this.volume && this.volume.hasOwnProperty("capacity"))
                    currentCapacity = this.volume.capacity;
                this.newCapacity = currentCapacity;
            }
        },
        watch: {
            volume: function (newValue, oldValue) {
                this.resetNewCapacity();
            }
        },
        computed: {
            min: function () {
                if (this.volume && this.volume.hasOwnProperty("capacity"))
                    return this.volume.capacity + 1;
                return 0;
            },
            volumeCapacity: function () {
                return this.newCapacity;
            },
        }
    }
</script>

<style scoped>

</style>