<template>
    <div class="ui long modal" ref="modal">
        <i class="close icon"></i>
        <div class="header" v-if="header.length > 0">
            {{ header }}
        </div>
        <div class="content">
            <semantic-ui-message type="negative" v-bind:messages="errors"></semantic-ui-message>
            <semantic-ui-dynamic-message ref="message"></semantic-ui-dynamic-message>
            <form class="ui form" v-on:submit.prevent="submit">
                <div class="ui two fields">
                    <div class="ui field" v-bind:class="{ error: 'name' in errors }">
                        <label class="required">名称</label>
                        <input type="text" v-model="temporaryRegion.name">
                    </div>

                    <div class="ui field" v-bind:class="{ error: 'icon_class' in errors }">
                        <label>图标类</label>
                        <input type="text" v-model="temporaryRegion.icon_class">
                    </div>
                </div>

                <div class="ui field" v-bind:class="{ error: 'description' in errors }">
                    <label>描述</label>
                    <textarea v-model="temporaryRegion.description"></textarea>
                </div>

                <div class="ui field">
                    <div class="ui checkbox" ref="stay">
                        <input type="checkbox" v-model="notStay">
                        <label>保存成功后关闭此窗口</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <div class="ui small cancel button">
                取消
            </div>
            <div class="ui small green right labeled icon button" v-bind:class="{ loading: isLoading }" v-on:click.prevent="submit">
                <template v-if="closing">
                    成功
                </template>
                <template v-else>
                    保存
                </template>
                <i class="checkmark icon"></i>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "RegionFormModal",
        data: function () {
            return {
                editing: false,
                notStay: true,
                region: {},
                temporaryRegion: {},
                isLoading: false,
                errors: {},
                closing: false,
            };
        },
        created: function () {
        },
        mounted: function () {
            $(this.$refs.stay).checkbox();
        },
        methods: {
            create: function () {
                this.region = {};
                this.temporaryRegion = {};
                this.editing = false;
                this.show();
            },
            edit: function (region) {
                this.region = region;
                this.temporaryRegion = _.cloneDeep(region);
                this.editing = true;
                this.show();
            },
            show: function () {
                this.errors = {};
                this.$refs.message.init();
                $(this.$refs.modal).modal({
                    closable: false
                }).modal('show');
            },
            submit: function () {
                this.errors = {};
                this.$refs.message.init();
                this.isLoading = true;
                if (this.editing) {
                    axios
                        .put(route('regions.update', [this.region.id]), this.temporaryRegion, { vueInstance: this })
                        .then((response) => {
                            this.$emit("item-updated", this.temporaryRegion);
                            this.$refs.message.positiveMessage("保存成功");
                            this.hideIfNotStay();
                        })
                        .catch((error) => {
                            console.log(error);
                        })
                        .then(() => {
                            this.isLoading = false;
                        })
                    ;
                } else {
                    axios.post(route('regions.store'), this.temporaryRegion, { vueInstance: this })
                        .then((response) => {
                            this.$emit("item-created", response.data.item);
                            this.region = {};
                            this.temporaryRegion = {};
                            this.$refs.message.positiveMessage("保存成功");
                            this.hideIfNotStay();
                        })
                        .catch((error) => {
                            console.log(error);
                            // this.$refs.message.negativeMessages(error.response.data.errors);
                        })
                        .then(() => {
                            this.isLoading = false;
                        })
                    ;
                }
            },
            hideIfNotStay: function () {
                if (this.notStay) {
                    // this.closing = true;
                    this.positiveFloatingMessage("保存成功");
                    $(this.$refs.modal).modal('hide');
/*
                    setTimeout(() => {
                        this.closing = false;
                        $(this.$refs.modal).modal('hide');
                    }, 1000);
*/
                }
            }
        },
        computed: {
            header: function () {
                if (this.editing)
                    return this.region.name + " - 编辑";
                return "创建" + this.$t('common.region');
            },
        }
    }
</script>

<style scoped>

</style>