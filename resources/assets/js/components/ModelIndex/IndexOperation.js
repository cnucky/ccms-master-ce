export default {
    data: function () {
        return {
            items: [],
            paginator: {},
            positive: {},
            negative: {},
            isLoading: false,
            filterKey: "",

            page: 1,
            prePageItem: 15,
        }
    },
    methods: {
        getSelectedItems: function () {
            return this.$refs.listTable.selectedItemList;
        },
        selectedItemCount: function () {
            if (this.$refs.listTable) {
                return this.$refs.listTable.selectedItemCount;
            }
            return 0;
        },
        emptySelectedItem: function () {
            return this.selectedItemCount() <= 0;
        },
        massDestroyRouteParameters: function () {
            return [];
        },
        massDestroy: function () {
            this.doMassDestroy(route(this.massDestroyRouteName, this.massDestroyRouteParameters()), this.onMassDestroyedItems);
        },
        massDestroyConfirmText: function () {
            return "确定要删除选中的项目吗？此操作不可恢复！";
        },
        doMassDestroy: function (massDestroyAPIURL, destroyItem = undefined) {
            this.$root.confirmModal()
                .withHeader("注意")
                .withContent(this.massDestroyConfirmText())
                .withOnApprove(() => {
                    this.isLoading = true;
                    var items = this.$refs.listTable.selectedItemList;
                    axios
                        .post(massDestroyAPIURL, { items: items })
                        .then((response) => {
                            if (response.data.result) {
                                if (typeof destroyItem === "function") {
                                    destroyItem(items);
                                } else {
                                    items.forEach((key) => {
                                        this.$refs.listTable.destroyByKey(key);
                                    });
                                }
                                this.positiveFloatingMessage("成功删除" + response.data.deletedItemCount + "个项目");
                            }
                        })
                        .catch(this.$errorToNegativeFloatingMessage)
                        .then(() => {
                            this.isLoading = false;
                        })
                    ;
                })
                .show()
            ;
        },
        itemCreated: function (item) {
            this.items.push(item);
        },
        itemUpdated: function (item) {
            var index = this.$refs.listTable.findItemIndex(item);
            if (index >= 0) {
                this.items.splice(index, 1, item);
            }
        },
        sortBy: function (key, isAsc) {
            this.filter();
        },
        tableUpdated: function () {
            $(".popup-table-row").popup({
                delay: {
                    show: 50,
                    hide: 100
                },
                hoverable: true,
                distanceAway: 42,
                offset: 100,
            });
        },
        additionalFilterArguments: function (filterArguments) {
            return filterArguments;
        },
        createFilterArguments: function () {
            var filterArguments = {
                page: this.page,
                prePage: this.prePageItem,
            };

            if (this.filterKey && this.filterKey.toString().trim().length) {
                filterArguments.search = this.filterKey.toString().trim();
            }

            if (this.$refs.listTable.sortKey.length) {
                filterArguments.sortKey = this.$refs.listTable.sortKey;
                filterArguments.isAsc = this.$refs.listTable.sortOrders[filterArguments.sortKey];
            }

            filterArguments = this.additionalFilterArguments(filterArguments);

            return filterArguments;
        },
        indexItems: function (filterArguments, noChangeLoadingStatus = false) {
            if (typeof noChangeLoadingStatus !== "boolean" || !noChangeLoadingStatus)
                this.isLoading = true;
            axios.get(route(this.indexRouteName, filterArguments), {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.loadSuccessCallback(data);
                    } else {
                        this.$globalErrnoHandler(data);
                    }
                })
                .catch(this.$axiosCatchError2Console)
                .then(() => {
                    if (typeof noChangeLoadingStatus !== "boolean" || !noChangeLoadingStatus)
                        this.isLoading = false;
                })
            ;
        },
        filter: function (noChangeLoadingStatus = false) {
            var filterArguments = this.createFilterArguments();

            // if (!this.noChangeURL)
            this.$router.push({path: this.$router.currentRoute.path, query: filterArguments});

            this.$refs.listTable.unselectAllItem();
            this.indexItems(filterArguments, noChangeLoadingStatus);
        }
    }
}