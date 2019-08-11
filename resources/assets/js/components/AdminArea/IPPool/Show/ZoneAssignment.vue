<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">
                可用区分配详情
            </h3>
        </div>
        <div class="eight wide column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>
            <model-index-mass-destroy-button :selectedItemCount="selectedItemCount()" v-on:click="massDestroy"></model-index-mass-destroy-button>
        </div>
        <div class="eight wide column">
            <button class="ui button" style="float: right; margin-left: 20px;" v-on:click="assignZone" v-bind:class="{loading: isAssigning}">添加到列表</button>

            <div class="ui selection search dropdown" style="float: right; width: 300px;">
                <input type="hidden" v-on:change="selectedZoneId = $event.target.value">
                <i class="dropdown icon"></i>
                <div class="default text">请选择可用区</div>
                <div class="menu">
                    <div v-for="zone in assignableZones" class="item" :data-value="zone.id"><i :class="zone.region.icon_class"></i> #{{ zone.id }} {{ zone.region.name }}-{{ zone.name }}</div>
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <sortable-table
                    id="ip-pool-table"
                    class="selectable unstackable"
                    :data="items"
                    :filter-key="filterKey"
                    :selectable="true"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="true"
                    operation-component="assignment-operation-column"
                    :is-loading="isLoading"
                    :sort-disabled="true"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
                    v-on:table-updated="tableUpdated"
            >
            </sortable-table>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';

    import indexOperation from "./../../../ModelIndex/IndexOperation";
    import pageOperation from "./../../../ModelIndex/PageOperation";

    Vue.component('zone-with-region-column', require("./ZoneWithRegion"));
    Vue.component('assignment-operation-column', require("./AssignmentOperationColumn"));
    export default {
        name: "ZoneAssignment",
        mixins: [indexOperation, pageOperation],
        props: ["modelInternalName", "modelName", "pool"],
        data: function () {
            return {
                assignmentName: "zoneAssignments",

                columns: {
                    id: "可用区ID",
                    name: "可用区名称",
                },
                columnComponents: {
                    id: "show-value",
                    name: "zone-with-region-column",
                },

                selectedZoneId: null,
                isAssigning: false,

                indexRouteName: this.modelInternalName + ".zoneAssignments.index",
                massDestroyRouteName: this.modelInternalName + ".zoneAssignments.massDestroy",

                availableZones: [],
            };
        },
        created: function () {

        },
        mounted: function () {
            this.filter();
            $(".ui.dropdown").dropdown();
        },
        methods: {
            loadSuccessCallback: function (data) {
                // If array, it's empty
                if (Array.isArray(data.assignments))
                    this.items = {};
                else
                    this.items = data.assignments;
                this.availableZones = data.availableZones;
            },
            additionalFilterArguments: function (filterArguments) {
                filterArguments[this.modelInternalName.replace("Pools", "Pool")] = this.pool.id;
                return filterArguments;
            },
            assignZone: function () {
                if (this.selectedZoneId === null || this.selectedZoneId === "") {
                    this.negativeFloatingMessage("请选择可用区");
                    return;
                }

                this.isAssigning = true;
                axios.post(route(this.modelInternalName + ".zoneAssignments.assign", [this.pool.id, this.selectedZoneId]), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$set(this.items, data.zoneId, this.availableZones[data.zoneId]);
                            this.selectedZoneId = null;
                            $(".ui.dropdown").dropdown("clear");
                            this.positiveFloatingMessage("添加成功");
                        } else {
                            this.negativeFloatingMessage(data.message);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    })
                    .then(() => {
                        this.isAssigning = false;
                    })
                ;
            },
            massDestroyRouteParameters: function () {
                return [this.pool.id];
            }
        },
        computed: {
            assignableZones: function () {
                var zones = [];
                for (var i in this.availableZones) {
                    if (!this.items.hasOwnProperty(this.availableZones[i].id))
                        zones.push(this.availableZones[i]);
                }
                return zones;
            }
        }
    }
</script>

<style scoped>

</style>