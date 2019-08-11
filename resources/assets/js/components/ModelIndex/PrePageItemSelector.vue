<template>
    <div class="ui mini right labeled input">
        <label class="ui label">每页</label>
        <select class="ui dropdown" ref="select" v-model="prePage" v-on:change="change"
                v-bind:class="{ loading: isLoading }">
            <option value="15">15</option>
            <option value="30">30</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <div class="ui basic label">项</div>
    </div>
</template>

<script>
    export default {
        name: "PrePageItemSelector",
        props: ["isLoading", "value"],
        data: function () {
            return {
                prePage: 15
            }
        },
        created: function () {
            this.prePage = this.prePageValueInQueryOrDefault();
            this.$emit('input', this.prePage);
        },
        mounted: function () {
            $(this.$refs.select).dropdown({
                placeholder: false,
            });
        },
        methods: {
            change: function (event) {
                this.$emit('input', parseInt(event.target.value));
                this.$emit('pre-page-item-change');
            },
            prePageValueInQueryOrDefault: function () {
                var prePage = this.prePage;
                if (this.$router.currentRoute.query.hasOwnProperty("prePage")) {
                    var userInputPrePageValue = parseInt(this.$router.currentRoute.query.prePage);
                    if (!Number.isNaN(userInputPrePageValue))
                        prePage = userInputPrePageValue;
                }

                return prePage;
            }
        }
    }
</script>

<style>
    .ui.labeled.input .dropdown.selection {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        /* border-left-color: transparent; */

        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        /* border-right-color: transparent !important; */

        min-width: 70px;
    }

    .ui.labeled.input .ui.dropdown .menu>.item {
        font-size: .78571429em;
    }

    .ui[class*="right labeled"].input > .dropdown.selection + .label {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-left-color: transparent!important;
    }

    .ui.mini.labeled.input > .dropdown .dropdown.icon {
        padding-top: 8px;
        padding-bottom: 9px;
        line-height: initial;
        top: initial;
    }
</style>