<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">{{ $t('common.image') }}管理</h1>
        </div>

        <div class="sixteen wide column">
            <model-index-refresh-button v-on:click="filter()"></model-index-refresh-button>
            <model-index-create-button v-on:click="$refs.imageCategoryFormModal.create()" model-name="类别"></model-index-create-button>
            <model-index-create-button v-on:click="$refs.imageFormModal.create()" model-name="镜像"></model-index-create-button>
            <model-index-mass-destroy-button :selectedItemCount="selectedItemCount()" v-on:click="massDestroy"></model-index-mass-destroy-button>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <sortable-table
                    id="region-table"
                    class="selectable unstackable"
                    :data="images"
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
                    <tr>
                        <td></td>
                        <td colspan="5">
                            <b>镜像类别：</b><i :class="categories[categoryIndex].icon_class"></i> {{ categories[categoryIndex].name }}
                        </td>
                        <td>
                            <div class="ui tiny buttons">
                                <button class="ui positive button" v-on:click="editImageCategory(categoryIndex)">编辑</button>
                                <div class="or"></div>
                                <button class="ui negative button" v-on:click="deleteImageCategory(categoryIndex)">删除</button>
                            </div>
                        </td>
                    </tr>

                    <template v-if="categories[categoryIndex].images.length > 0">
                    <tr v-for="(item, index) in sortedImages[categories[categoryIndex].id]">
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
                            {{ item.force_version }}
                        </td>
                        <td>
                            <status-label :status-code="item.status"></status-label>
                        </td>
                        <td>
                            <div class="ui tiny buttons">
                                <button class="ui positive button" v-on:click="editImage(categoryIndex, item)">编辑</button>
                                <div class="or"></div>
                                <button class="ui negative button" v-on:click="deleteImage(categoryIndex, item)">删除</button>
                            </div>
                        </td>
                    </tr>
                    </template>
                    <tr v-else>
                        <td colspan="7" class="center aligned" style="color: gray;">该类别下无镜像</td>
                    </tr>
                </template>
            </sortable-table>
        </div>

        <image-form-modal ref="imageFormModal" :options="categories" v-on:item-created="imageCreated" v-on:item-updated="imageUpdated"></image-form-modal>
        <image-category-image-form-modal ref="imageCategoryFormModal" v-on:item-created="imageCategoryCreated" v-on:item-updated="imageCategoryUpdated"></image-category-image-form-modal>
    </div>
</template>

<script>
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";
    import ImageFormModal from "./ImageFormModal";
    import ImageCategoryImageFormModal from "../ImageCategory/ImageCategoryFormModal";
    import StatusLabel from "./StatusLabel";

    export default {
        name: "Index",
        components: {StatusLabel, ImageCategoryImageFormModal, ImageFormModal},
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    name: "名称",
                    internal_name: "内部名称",
                    force_version: "锁定版本",
                    status: "状态"
                },
                indexRouteName: "images.index",
                massDestroyRouteName: "images.massDestroy",

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
            editItem: function (item) {
                this.$refs.zoneFormModal.edit(item);
            },
            imageCategoryCreated: function (imageCategory) {
                imageCategory.images = []; // Fix undefined error on sort images
                this.categories.push(imageCategory);
            },
            imageCategoryUpdated: function (imageCategory) {
                imageCategory.images = this.categories[this.categoryIdMap2Index[imageCategory.id]].images;
                this.$set(this.categories, this.categoryIdMap2Index[imageCategory.id], imageCategory);
            },
            imageCreated: function (image) {
                var categoryId = image.image_category_id;
                this.categories[this.categoryIdMap2Index[categoryId]].images.push(image);
            },
            imageUpdated: function (image) {
                var categoryIndex = this.categoryIdMap2Index[image.image_category_id];
                var imageIndex = this.imageIndex(categoryIndex, image);
                this.$set(this.categories[categoryIndex].images, imageIndex, image);
            },
            additionalFilterArguments: function (filterArguments) {
            },
            deleteImageCategory: function (categoryIndex) {
                this
                    .confirmModal()
                    .withHeader("提示")
                    .withContent("删除此类别将同时删除其下的所有镜像记录，确定删除？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.delete(route('imageCategories.destroy', [this.categories[categoryIndex].id])).
                        then((response) => {
                            if (response.data) {
                                this.positiveFloatingMessage("镜像类别删除成功");
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
            deleteImage: function (categoryIndex, item) {
                this
                    .confirmModal()
                    .withHeader("提示")
                    .withContent("确定删除此项？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        axios.delete(route('images.destroy', [item.id])).
                            then((response) => {
                                if (response.data) {
                                    this.positiveFloatingMessage("删除成功");
                                    this.$delete(this.categories[categoryIndex].images, this.imageIndex(categoryIndex, item));
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
            imageIndex: function (categoryIndex, image) {
                for (var imageIndex in this.categories[categoryIndex].images) {
                    if (this.categories[categoryIndex].images[imageIndex].id === image.id)
                        break;
                }
                return imageIndex;
            },
            editImageCategory: function (categoryIndex) {
                this.$refs.imageCategoryFormModal.edit(this.categories[[categoryIndex]]);
            },
            editImage: function (categoryIndex, image) {
                this.$refs.imageFormModal.edit(image);
            },
            onMassDestroyedItems: function (keys) {
                KEY_ITERATOR: for (var i in keys) {
                    var intKey = parseInt(keys[i]);

                    for (var categoryIndex in this.categories) {
                        for (var imageIndex in this.categories[categoryIndex].images) {
                            var imageId = this.categories[categoryIndex].images[imageIndex].id;
                            if (imageId === intKey) {
                                this.$delete(this.categories[categoryIndex].images, imageIndex);
                                continue KEY_ITERATOR;
                            }
                        }
                    }
                }

            }
        },
        computed: {
            images: function () {
                var images = [];
                for (var categoryIndex in this.categories) {
                    for (var imageIndex in this.categories[categoryIndex].images) {
                        images.push(this.categories[categoryIndex].images[imageIndex]);
                    }
                }

                return images;
            },
            sortedImages: function () {
                var images = {};

                for (var categoryIndex in this.categories) {
                    var categoryId = this.categories[categoryIndex].id;

                    var clonedImages = _.cloneDeep(this.categories[categoryIndex].images);

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
                return Object.keys(this.images);
            }
        }
    }
</script>

<style scoped>

</style>