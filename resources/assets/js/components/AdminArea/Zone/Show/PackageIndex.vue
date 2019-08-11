<template>
    <div class="ui grid">
        <div class="eight wide column">
            <model-index-refresh-button v-on:click="filter"></model-index-refresh-button>
            <model-index-mass-destroy-button :selectedItemCount="selectedItemCount()" v-on:click="massDestroy"></model-index-mass-destroy-button>
        </div>

        <div class="eight wide column">
            <button class="ui button" style="float: right; margin-left: 20px;" v-on:click="assign" v-bind:class="{loading: isAssigning}" :disabled="isAssigning || selectedPackage === null">添加到列表</button>

            <div ref="assignablePackageSelect" class="ui selection search dropdown" style="float: right; width: 300px;" v-bind:class="{loading: isAssigning}">
                <i class="dropdown icon"></i>
                <div class="default text"></div>
                <div class="menu">
                    <template v-for="packageCategory in assignablePackages">
                        <div class="header">
                            {{ packageCategory.name }}
                        </div>
                        <template v-for="assignablePackage in packageCategory.packages">
                            <div class="item" v-on:click="selectedPackage = assignablePackage">{{ assignablePackage.name }}</div>
                        </template>
                    </template>
                </div>
            </div>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <sortable-table
                    id="ip-pool-table"
                    class="selectable fixed unstackable"
                    :data="items"
                    :filter-key="filterKey"
                    :selectable="true"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="true"
                    operation-component="zone-package-index-operation-column"
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
    import Vue from "vue";
    import indexOperation from "./../../../ModelIndex/IndexOperation";
    import pageOperation from "./../../../ModelIndex/PageOperation";

    Vue.component("zone-package-index-category-column", require("./Column/PackageCategoryColumn"));
    Vue.component("zone-package-index-stock-column", require("./Column/StockColumn"));
    Vue.component("zone-package-index-operation-column", require("./Column/PackageAssignmentOperation"));

    export default {
        name: "PackageIndex",
        mixins: [indexOperation, pageOperation],
        props: ["data"],
        data: function () {
            return {
                isLoading: false,
                isAssigning: false,

                columns: {
                    id: "规格ID",
                    category: "规格类别",
                    name: "规格名称",
                    stock: "余量",
                },
                columnComponents: {
                    id: "show-value",
                    category: "zone-package-index-category-column",
                    name: "show-value",
                    stock: "zone-package-index-stock-column",
                },

                indexRouteName: "zones.packages.index",
                massDestroyRouteName: "zones.packages.massDestroy",

                availablePackages: [],

                selectedPackage: null,
            };
        },
        created: function () {
        },
        mounted: function () {
            this.filter();
            $(this.$refs.assignablePackageSelect).dropdown({
                clearable: true,
                forceSelection: false,
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                // If array, it's empty
                if (Array.isArray(data.packages))
                    this.items = {};
                else
                    this.items = data.packages;
                this.availablePackages = data.availablePackages;
            },
            additionalFilterArguments: function (filterArguments) {
                filterArguments.id = this.data.zone.id;
                return filterArguments;
            },
            assign: function () {
                this.isAssigning = true;
                var selectedPackage = _.cloneDeep(this.selectedPackage);
                axios.post(route("zones.packages.assign", [this.data.zone.id, selectedPackage.id]), null, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            selectedPackage.pivot = {
                                id: data.assignmentId,
                                package_id: selectedPackage.id,
                                stock: null,
                                zone_id: this.data.zone.id,
                            };
                            // this.$set(this.items, selectedPackage.id, selectedPackage);
                            this.filter();
                            this.selectedPackage = null;
                            $(this.$refs.assignablePackageSelect).dropdown("clear");
                            this.positiveFloatingMessage("添加成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isAssigning = false;
                    })
                ;
            },
            massDestroyRouteParameters: function () {
                return [this.data.zone.id];
            }
        },
        computed: {
            assignablePackages: function () {
                var packages = [];
                for (var categoryIndex in this.availablePackages) {
                    var category = this.availablePackages[categoryIndex];
                    var assignablePackagesInCategory = [];
                    for (var packageIndex in category.packages) {
                        if (!this.items.hasOwnProperty(category.packages[packageIndex].id)) {
                            assignablePackagesInCategory.push(category.packages[packageIndex]);
                        }
                    }
                    if (assignablePackagesInCategory.length) {
                        packages.push({
                            name: category.name,
                            packages: assignablePackagesInCategory,
                        });
                    }
                }
                return packages;
            }
        }
    }
</script>

<style scoped>

</style>