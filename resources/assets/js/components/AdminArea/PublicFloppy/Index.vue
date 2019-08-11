<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">{{ $t('common.publicFloppyImage') }}管理</h1>
        </div>

        <div class="sixteen wide column">
            <model-index-refresh-button v-on:click="filter()"></model-index-refresh-button>
            <model-index-create-button v-on:click="$refs.floppyCategoryFormModal.create()" model-name="类别"></model-index-create-button>
            <model-index-create-button v-on:click="$refs.floppyFormModal.create()" model-name="软盘镜像"></model-index-create-button>
            <model-index-mass-destroy-button :selectedItemCount="selectedItemCount()" v-on:click="massDestroy"></model-index-mass-destroy-button>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <sortable-table
                    id="region-table"
                    class="selectable unstackable"
                    :data="computedItems"
                    :filter-key="filterKey"
                    :selectable="true"
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
                <template v-for="categoryIndex in sortedCategoryIndex">
                    <tr v-if="categoryIndex > 0">
                        <td colspan="6" class="disabled" style="padding: 2px 0 0 0;"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="4">
                            <b>软盘镜像类别：</b><i :class="categories[categoryIndex].icon_class"></i> {{ categories[categoryIndex].name }}
                        </td>
                        <td>
                            <div class="ui tiny buttons">
                                <button class="ui positive button" v-on:click="editCategory(categoryIndex)">编辑</button>
                                <div class="or"></div>
                                <button class="ui negative button" v-on:click="deleteCategory(categoryIndex)">删除</button>
                            </div>
                        </td>
                    </tr>

                    <template v-if="categories[categoryIndex].public_floppies.length > 0">
                        <tr v-for="(item, index) in sortedItems[categories[categoryIndex].id]">
                            <td>
                                <div class="ui fitted child checkbox" v-if="item.id">
                                    <input type="checkbox" v-model="$refs.listTable.isItemSelected[item.id]">
                                    <label></label>
                                </div>
                            </td>
                            <td>
                                {{ item.id }}
                            </td>
                            <td>
                                {{ item.name }}
                            </td>
                            <td>
                                {{ item.internal_name }}
                            </td>
                            <td>
                                <status-label :status-code="item.status"></status-label>
                            </td>
                            <td>
                                <div class="ui tiny buttons">
                                    <button class="ui positive button" v-on:click="editItem(categoryIndex, item)">编辑</button>
                                    <div class="or"></div>
                                    <button class="ui negative button" v-on:click="deleteItem(categoryIndex, item)">删除</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr v-else>
                        <td colspan="6" class="center aligned" style="color: gray;">该类别下无镜像</td>
                    </tr>
                </template>
            </sortable-table>
        </div>

        <public-floppy-form-modal ref="floppyFormModal" :options="categories" v-on:item-created="itemCreated" v-on:item-updated="itemUpdated"></public-floppy-form-modal>
        <public-floppy-category-form-modal ref="floppyCategoryFormModal" v-on:item-created="categoryCreated" v-on:item-updated="categoryUpdated"></public-floppy-category-form-modal>
    </div>
</template>

<script>
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";
    import PublicFloppyFormModal from "./PublicFloppyFormModal";
    import PublicFloppyCategoryFormModal from "../PublicFloppyCategory/PublicFloppyCategoryFormModal";
    import StatusLabel from "./../../CommonStatusLabel";

    export default {
        name: "Index",
        components: {StatusLabel, PublicFloppyCategoryFormModal, PublicFloppyFormModal},
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    name: "名称",
                    internal_name: "内部名称",
                    status: "状态"
                },
                indexRouteName: "publicFloppies.index",
                massDestroyRouteName: "publicFloppies.massDestroy",

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
                this.categories = data.categories;
            },
            tableCreated: function () {
                // this.filter();
            },
            categoryCreated: function (category) {
                category.public_floppies = []; // Fix undefined error on sort images
                this.categories.push(category);
            },
            categoryUpdated: function (category) {
                category.public_floppies = this.categories[this.categoryIdMap2Index[category.id]].public_floppies;
                this.$set(this.categories, this.categoryIdMap2Index[category.id], category);
            },
            itemCreated: function (item) {
                var categoryId = item.category_id;
                this.categories[this.categoryIdMap2Index[categoryId]].public_floppies.push(item);
            },
            itemUpdated: function (originItem, item) {
                var originCategoryIndex = this.categoryIdMap2Index[originItem.category_id];
                var newCategoryIndex = this.categoryIdMap2Index[item.category_id];

                var itemIndex = this.itemIndex(originCategoryIndex, item);
                this.$delete(this.categories[originCategoryIndex].public_floppies, itemIndex);
                this.categories[newCategoryIndex].public_floppies.push(item);
            },
            additionalFilterArguments: function (filterArguments) {
            },
            deleteCategory: function (categoryIndex) {
                this
                    .confirmModal()
                    .withHeader("提示")
                    .withContent("删除此类别将同时删除其下的所有记录，确定删除？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.delete(route('publicFloppyCategories.destroy', [this.categories[categoryIndex].id]), {vueInstance: this}).
                        then((response) => {
                            if (response.data) {
                                this.positiveFloatingMessage("类别删除成功");
                                this.$delete(this.categories, categoryIndex);
                            }
                        }).catch((error) => {
                            console.log(error);
                        }).then(() => {
                            this.isLoading = false;
                        })
                        ;
                    })
                    .show()
                ;
            },
            deleteItem: function (categoryIndex, item) {
                this
                    .confirmModal()
                    .withHeader("提示")
                    .withContent("确定删除此项？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.delete(route('publicFloppies.destroy', [item.id]), {vueInstance: this}).
                        then((response) => {
                            if (response.data) {
                                this.positiveFloatingMessage("删除成功");
                                this.$delete(this.categories[categoryIndex].public_floppies, this.itemIndex(categoryIndex, item));
                            }
                        }).catch((error) => {
                            console.log(error);
                        }).then(() => {
                            this.isLoading = false;
                        })
                        ;
                    })
                    .show()
                ;
            },
            dataKeysGetter: function () {
                return this.dataKeys;
            },
            itemIndex: function (categoryIndex, item) {
                for (var itemIndex in this.categories[categoryIndex].public_floppies) {
                    if (this.categories[categoryIndex].public_floppies[itemIndex].id === item.id)
                        break;
                }
                return itemIndex;
            },
            editCategory: function (categoryIndex) {
                this.$refs.floppyCategoryFormModal.edit(this.categories[[categoryIndex]]);
            },
            editItem: function (categoryIndex, image) {
                this.$refs.floppyFormModal.edit(image);
            },
            onMassDestroyedItems: function (keys) {
                KEY_ITERATOR: for (var i in keys) {
                    var intKey = parseInt(keys[i]);

                    for (var categoryIndex in this.categories) {
                        for (var imageIndex in this.categories[categoryIndex].public_floppies) {
                            var imageId = this.categories[categoryIndex].public_floppies[imageIndex].id;
                            if (imageId === intKey) {
                                this.$delete(this.categories[categoryIndex].public_floppies, imageIndex);
                                continue KEY_ITERATOR;
                            }
                        }
                    }
                }

            }
        },
        computed: {
            computedItems: function () {
                var images = [];
                for (var categoryIndex in this.categories) {
                    for (var imageIndex in this.categories[categoryIndex].public_floppies) {
                        images.push(this.categories[categoryIndex].public_floppies[imageIndex]);
                    }
                }

                return images;
            },
            sortedItems: function () {
                var images = {};

                for (var categoryIndex in this.categories) {
                    var categoryId = this.categories[categoryIndex].id;

                    var clonedImages = _.cloneDeep(this.categories[categoryIndex].public_floppies);

                    clonedImages.sort(function (a, b) {
                        return b.order - a.order;
                    });

                    images[categoryId] = clonedImages;
                }

                return images;
            },
            sortedCategoryIndex: function () {
                var categoryIndex = Object.keys(this.categories);
                categoryIndex.sort((a, b) => {
                    return this.categories[b].order - this.categories[a].order;
                });
                return categoryIndex;
            },
            categoryIdMap2Index: function () {
                var map = {};
                for (var categoryIndex in this.categories) {
                    map[this.categories[categoryIndex].id] = categoryIndex;
                }

                return map;
            },
            dataKeys: function () {
                return Object.keys(this.public_floppies);
            }
        }
    }
</script>

<style scoped>

</style>