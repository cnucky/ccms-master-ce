<template>
    <td>
        <div class="ui mini action input">
            <input type="number" placeholder="自动" v-model="entry.pivot.stock" min="0" v-on:keyup.enter="updateStock">
            <button class="ui icon button" v-on:click="updateStock" v-bind:class="{loading: isSubmitting}" :disalbed="isSubmitting">
                <i class="save icon"></i>
            </button>
        </div>
    </td>
</template>

<script>
    export default {
        name: "StockColumn",
        props: ["entry", "parentApp"],
        data: function () {
            return {
                isSubmitting: false,
            };
        },
        methods: {
            updateStock: function () {
                this.isSubmitting = true;
                axios.patch(route("zones.packages.update", [this.entry.pivot.id]), {stock: this.entry.pivot.stock}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.positiveFloatingMessage("保存成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmitting = false;
                    })
                ;
            }
        }
    }
</script>

<style scoped>

</style>