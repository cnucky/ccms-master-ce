<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">{{ $t('common.computeInstancePackage') }}管理</h1>
        </div>

        <div class="sixteen wide column">
            <model-index-refresh-button v-on:click="filter()"></model-index-refresh-button>
            <model-index-create-button v-on:click="$refs.categoryFormModal.create()" model-name="类别"></model-index-create-button>
            <model-index-create-button v-on:click="$refs.packageFormModal.create()" :model-name="$t('common.computeInstancePackage')"></model-index-create-button>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <sortable-table
                    id="region-table"
                    class="selectable unstackable"
                    :data="packages"
                    :filter-key="filterKey"
                    :selectable="false"
                    :columns="columns"
                    :operable="true"
                    operation-component="span"
                    :is-loading="isLoading"
                    :use-slot-row="true"
                    :sort-disabled="true"
                    :force-show-table-body="Object.keys(categories).length > 0"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:table-created="tableCreated"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
                    v-on:edit-item="editItem"
                    v-on:table-updated="tableUpdated"
            >
                <template v-for="(category, categoryIndex) in categories">
                    <tr>
                        <td></td>
                        <td colspan="6">
                            <b>规格类别：</b> {{ category.name }}
                        </td>
                        <td>
                            <div class="ui tiny buttons">
                                <button class="ui positive button" v-on:click="editCategory(categoryIndex)">编辑</button>
                                <div class="or"></div>
                                <button class="ui negative button" v-on:click="deleteCategory(categoryIndex)">删除</button>
                            </div>
                        </td>
                    </tr>

                    <template v-if="category.packages.length > 0">
                        <tr v-for="(item, index) in sortedPackages[category.id]">
                            <td>
                                {{ item.id }}
                            </td>
                            <td>
                                {{ item.name }}
                            </td>
                            <td>
                                {{ item.vCPU }}
                            </td>
                            <td>
                                {{ item.memory }} MiB
                            </td>
                            <td>
                                {{ item.price_per_hour }} / 小时
                            </td>
                            <td>
                                {{ item.instances_count }}
                            </td>
                            <td>
                                <status-label :status-code="item.status"></status-label>
                            </td>
                            <td>
                                <div class="ui tiny buttons">
                                    <button class="ui positive button" v-on:click="editPackage(categoryIndex, item)">编辑</button>
                                    <div class="or"></div>
                                    <button class="ui negative button" v-on:click="deletePackage(categoryIndex, item)">删除</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr v-else>
                        <td colspan="8" class="center aligned" style="color: gray;">该类别下无规格</td>
                    </tr>
                </template>
            </sortable-table>
        </div>

        <package-form-modal ref="packageFormModal" :options="categories" v-on:item-created="packageCreated" v-on:item-updated="packageUpdated"></package-form-modal>
        <package-category-form-modal ref="categoryFormModal" v-on:item-created="categoryCreated" v-on:item-updated="categoryUpdated"></package-category-form-modal>
    </div>
</template>

<script>
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";
    import PackageFormModal from "./PackageFormModal";
    import PackageCategoryFormModal from "./CategoryFormModal";
    import StatusLabel from "./StatusLabel";

    export default {
        name: "Index",
        components: {StatusLabel, PackageFormModal, PackageCategoryFormModal},
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    name: "名称",
                    vCPU: "vCPU",
                    memory: "物理内存",
                    price_per_hour: "价格",
                    instances_count: "实例数",
                    status: "状态"
                },
                indexRouteName: "computeInstancePackages.index",
                massDestroyRouteName: "computeInstancePackages.massDestroy",

                categories: [],
            }
        },
        created: function () {
        },
        mounted: function () {
            this.$nextTick(() => {
                this.filter();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                this.categories = data.packageCategories;
            },
            tableCreated: function () {
                // this.filter();
            },
            editItem: function (item) {
                this.$refs.zoneFormModal.edit(item);
            },
            categoryCreated: function (category) {
                category.packages = []; // Fix undefined error on sort
                this.categories.push(category);
            },
            categoryUpdated: function (category) {
                category.packages = this.categories[this.categoryIdMap2Index[category.id]].packages;
                this.$set(this.categories, this.categoryIdMap2Index[category.id], category);
            },
            packageCreated: function (newPackage) {
                var categoryId = newPackage.category_id;
                this.categories[this.categoryIdMap2Index[categoryId]].packages.push(newPackage);
            },
            packageUpdated: function (updatedPackage) {
                var categoryIndex = this.categoryIdMap2Index[updatedPackage.category_id];
                var packageIndex = this.packageIndex(categoryIndex, updatedPackage);
                this.$set(this.categories[categoryIndex].packages, packageIndex, updatedPackage);
            },
            additionalFilterArguments: function (filterArguments) {
            },
            deleteCategory: function (categoryIndex) {
                this
                    .confirmModal()
                    .withHeader("提示")
                    .withContent("删除此类别将同时删除其下的所有规格记录，确定删除？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        this.$axiosDelete(route('computeInstancePackageCategories.destroy', [this.categories[categoryIndex].id]), (response) => {
                            this.positiveFloatingMessage("实例规格类别删除成功");
                            this.$delete(this.categories, categoryIndex);
                        }, () => {
                            this.isLoading = false;
                        });
                    })
                    .show()
                ;
            },
            deletePackage: function (categoryIndex, item) {
                this
                    .confirmModal()
                    .withHeader("提示")
                    .withContent("确定删除此项？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        this.$axiosDelete(route('computeInstancePackages.destroy', [item.id]), (response) => {
                            this.positiveFloatingMessage("删除成功");
                            this.$delete(this.categories[categoryIndex].packages, this.packageIndex(categoryIndex, item));
                        }, () => {
                            this.isLoading = false;
                        });
                    })
                    .show()
                ;
            },
            dataKeysGetter: function () {
                return this.dataKeys;
            },
            packageIndex: function (categoryIndex, image) {
                for (var packageIndex in this.categories[categoryIndex].packages) {
                    if (this.categories[categoryIndex].packages[packageIndex].id === image.id)
                        break;
                }
                return packageIndex;
            },
            editCategory: function (categoryIndex) {
                this.$refs.categoryFormModal.edit(this.categories[[categoryIndex]]);
            },
            editPackage: function (categoryIndex, image) {
                this.$refs.packageFormModal.edit(image);
            },
            onMassDestroyedItems: function (keys) {
                KEY_ITERATOR: for (var i in keys) {
                    var intKey = parseInt(keys[i]);

                    for (var categoryIndex in this.categories) {
                        for (var packageIndex in this.categories[categoryIndex].packages) {
                            var packageId = this.categories[categoryIndex].packages[packageIndex].id;
                            if (packageId === intKey) {
                                this.$delete(this.categories[categoryIndex].packages, packageIndex);
                                continue KEY_ITERATOR;
                            }
                        }
                    }
                }

            }
        },
        computed: {
            packages: function () {
                var packages = [];
                for (var categoryIndex in this.categories) {
                    for (var packageIndex in this.categories[categoryIndex].packages) {
                        packages.push(this.categories[categoryIndex].packages[packageIndex]);
                    }
                }

                return packages;
            },
            sortedPackages: function () {
                var packages = {};

                for (var categoryIndex in this.categories) {
                    var categoryId = this.categories[categoryIndex].id;

                    var clonedPackages = _.cloneDeep(this.categories[categoryIndex].packages);

                    clonedPackages.sort(function (a, b) {
                        return b.order - a.order;
                    });

                    packages[categoryId] = clonedPackages;
                }

                return packages;
            },
            categoryIdMap2Index: function () {
                var map = {};
                for (var categoryIndex in this.categories) {
                    map[this.categories[categoryIndex].id] = categoryIndex;
                }

                return map;
            },
        }
    }
</script>

<style scoped>

</style>