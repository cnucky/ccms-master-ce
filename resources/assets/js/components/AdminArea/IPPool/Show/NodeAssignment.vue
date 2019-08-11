<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h3 class="ui header">
                节点分配详情
            </h3>
        </div>
        <div class="six wide column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>
            <model-index-mass-destroy-button :selectedItemCount="selectedItemCount()" v-on:click="massDestroy"></model-index-mass-destroy-button>
        </div>
        <div class="ten wide column">
            <button class="ui button" style="float: right; margin-left: 20px;" v-on:click="assignNode" v-bind:class="{loading: isAssigning}">添加到列表</button>

            <div class="ui selection search dropdown" style="float: right; width: 300px;">
                <input type="hidden" v-on:change="selectedNodeId = $event.target.value">
                <i class="dropdown icon"></i>
                <div class="default text">请选择节点</div>
                <div class="menu">
                    <div v-for="node in assignableNodes" class="item" :data-value="node.id"><i :class="node.zone.region.icon_class"></i> #{{ node.id }} {{ node.zone.region.name }}-{{ node.zone.name }} {{ node.name }}</div>
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

    Vue.component('node-region-area-column', require("./NodeRegionAreaColumn"));
    export default {
        name: "NodeAssignment",
        mixins: [indexOperation, pageOperation],
        props: ["modelInternalName", "modelName", "pool"],
        data: function () {
            return {
                assignmentName: "nodeAssignments",

                columns: {
                    id: "节点ID",
                    regionArea: "地区",
                    name: "节点名称"
                },
                columnComponents: {
                    id: "show-value",
                    regionArea: "node-region-area-column",
                    name: "show-value",
                },

                selectedNodeId: null,
                isAssigning: false,

                indexRouteName: this.modelInternalName + ".nodeAssignments.index",
                massDestroyRouteName: this.modelInternalName + ".nodeAssignments.massDestroy",

                availableNodes: [],
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
                this.availableNodes = data.availableNodes;
            },
            additionalFilterArguments: function (filterArguments) {
                filterArguments.id = this.pool.id;
                return filterArguments;
            },
            assignNode: function () {
                if (this.selectedNodeId === null || this.selectedNodeId === "") {
                    this.negativeFloatingMessage("请选择可用区");
                    return;
                }

                this.isAssigning = true;
                axios.post(route(this.modelInternalName + ".nodeAssignments.assign", [this.pool.id, this.selectedNodeId]), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$set(this.items, data.nodeId, this.availableNodes[data.nodeId]);
                            this.selectedNodeId = null;
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
            assignableNodes: function () {
                var nodes = [];
                for (var i in this.availableNodes) {
                    if (!this.items.hasOwnProperty(this.availableNodes[i].id))
                        nodes.push(this.availableNodes[i]);
                }
                return nodes;
            }
        }
    }
</script>

<style scoped>

</style>