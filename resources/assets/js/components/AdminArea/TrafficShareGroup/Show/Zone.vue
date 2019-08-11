<template>
    <div class="ui grid">
        <div class="eight wide column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>
        </div>

        <div class="eight wide column">
            <button class="ui button" style="float: right; margin-left: 20px;" v-on:click="assign" v-bind:class="{loading: isAssigning}" :disabled="isAssigning || selectedZone === null">添加到列表</button>

            <div ref="assignableZoneSelect" class="ui selection search dropdown" style="float: right; width: 300px;" v-bind:class="{loading: isAssigning}">
                <i class="dropdown icon"></i>
                <div class="default text"></div>
                <div class="menu">
                    <template v-for="region in assignableZones">
                        <div class="header">
                            <i :class="region.iconClass + ' flag'"></i> {{ region.name }}
                        </div>
                        <template v-for="zone in region.zones">
                            <div class="item" v-on:click="selectedZone = zone">{{ zone.name }}</div>
                        </template>
                    </template>
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <sortable-table
                    class="selectable fixed unstackable"
                    :data="items"
                    :filter-key="filterKey"
                    :selectable="true"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="true"
                    operation-component="traffic-share-group-zone-index-operation-column"
                    :is-loading="isLoading"
                    :sort-disabled="true"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
            >
            </sortable-table>
        </div>
    </div>
</template>

<script>
    import Vue from "vue";
    import indexOperation from "./../../../ModelIndex/IndexOperation";
    import pageOperation from "./../../../ModelIndex/PageOperation";

    Vue.component('traffic-share-group-zone-index-operation-column', require("./OperationColumn"));

    export default {
        name: "Zone",
        mixins: [indexOperation, pageOperation],
        props: ["trafficShareGroup"],
        data: function () {
            return {
                isLoading: false,
                isAssigning: false,

                columns: {
                    id: "可用区ID",
                    name: "可用区名称",
                },
                columnComponents: {
                    id: "show-value",
                    name: "show-value",
                },

                indexRouteName: "trafficShareGroups.zones.index",
                massDestroyRouteName: "trafficShareGroups.zones.massDestroy",

                availableZones: [],

                selectedZone: null,
            };
        },
        created: function () {
        },
        mounted: function () {
            this.filter();
            $(this.$refs.assignableZoneSelect).dropdown({
                clearable: true,
                forceSelection: false,
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                // If array, it's empty
                if (Array.isArray(data.zones))
                    this.items = {};
                else
                    this.items = data.zones;
                this.availableZones = data.availableZones;
            },
            additionalFilterArguments: function (filterArguments) {
                filterArguments.id = this.trafficShareGroup.id;
                return filterArguments;
            },
            assign: function () {
                this.isAssigning = true;
                var selectedZone = this.selectedZone;
                this.$axiosPost(route("trafficShareGroups.zones.assign", [this.trafficShareGroup.id, selectedZone.id]), null, (data) => {
                    this.selectedZone = null;
                    $(this.$refs.assignableZoneSelect).dropdown("clear");
                    this.$set(this.items, selectedZone.id, selectedZone);
                    this.positiveFloatingMessage("添加成功");
                }, () => {
                    this.isAssigning = false;
                });
            }
        },
        computed: {
            assignableZones: function () {
                var assignableZones = [];
                for (var regionIndex in this.availableZones) {
                    var region = this.availableZones[regionIndex];
                    var assignableZoneInCategory = [];
                    for (var zoneIndex in region.zones) {
                        var zone = region.zones[zoneIndex];
                        if (this.items.hasOwnProperty(zone.id))
                            continue;
                        assignableZoneInCategory.push(zone);
                    }
                    if (assignableZoneInCategory.length) {
                        assignableZones.push({
                            id: region.id,
                            name: region.name,
                            iconClass: region.icon_class,
                            zones: assignableZoneInCategory,
                        });
                    }
                }

                return assignableZones;
            }
        }
    }
</script>

<style scoped>

</style>