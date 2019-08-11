<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">常规设置</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: !systemConfigurations}">
                <form v-if="systemConfigurations" class="ui form" v-on:submit.prevent="update">
                    <div class="ui field">
                        <label>系统Host</label>
                        <input type="text" v-model="systemConfigurations.SYSTEM_HOST">
                    </div>
                    <div class="ui field">
                        <label>用户欠费，资源将在多少小时后释放？</label>
                        <input type="number" min="0" max="65535" placeholder="留空关闭自动释放" v-model="systemConfigurations.AUTO_RELEASE_LC_USER_RESOURCE_AFTER">
                    </div>
                    <div class="ui field">
                        <button type="submit" class="ui small teal fluid button" v-bind:class="{loading: isSubmitting}" :disabled="isSubmitting">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Setting",
        data: function () {
            return {
                isSubmitting: false,
                systemConfigurations: null,
            };
        },
        created: function () {
            this.$axiosGet(route("systemConfigurations.show"), (data) => {
                this.systemConfigurations = data.systemConfigurations;
            });
        },
        methods: {
            update: function () {
                this.isSubmitting = true;
                this.$axiosPatch(route("systemConfigurations.update"), this.systemConfigurations, (data) => {
                    this.positiveFloatingMessage("保存成功");
                }, () => {
                    this.isSubmitting = false;
                })
            }
        }
    }
</script>

<style scoped>

</style>