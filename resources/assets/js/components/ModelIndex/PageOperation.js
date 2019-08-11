export default {
    methods: {
        prevPage: function () {
            --this.page;
            this.filter();
        },
        nextPage: function () {
            ++this.page;
            this.filter();
        },
        jumpToPage: function (page) {
            this.page = page;
            this.filter();
        },
    }
}