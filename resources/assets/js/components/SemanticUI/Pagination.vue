<template>
    <div class="ui pagination menu">
        <!-- Previous Page Link -->
        <a v-if="onFirstPage" class="icon item disabled"> <i class="left chevron icon"></i> </a>
        <a v-else class="icon item" href="#" v-on:click.prevent="$emit('prev')" rel="prev"> <i class="left chevron icon"></i> </a>

        <template v-for="element in elements">
            <!-- "Three Dots" Separator -->
            <a v-if="typeof element === 'string'" class="icon item disabled">{{ element }}</a>
            <!-- LInk -->
            <span v-else-if="element === currentPage" class="item active">{{ element }}</span>
            <a v-else class="item" href="#" v-on:click.prevent="$emit('jump-to', element)" :class="{ active: element === currentPage }">{{ element }}</a>
        </template>

        <!-- Next Page Links -->
        <a v-if="hasMorePages" class="icon item" href="#" v-on:click.prevent="$emit('next')" rel="next"> <i class="right chevron icon"></i> </a>
        <a v-else class="icon item disabled"> <i class="right chevron icon"></i> </a>
    </div>
</template>

<script>
    export default {
        name: "Pagination",
        props: ["paginator"],
        computed: {
            currentPage: function () {
                return this.paginator.current_page;
            },
            lastPage: function () {
                return this.paginator.last_page;
            },
            onFirstPage: function () {
                return this.currentPage === 1;
            },
            hasMorePages: function () {
                return this.currentPage < this.lastPage;
            },
            elements: function () {
                var elements = [];
                var start = Math.max(1, this.currentPage - 3);
                var end = Math.min(this.lastPage, this.currentPage + 3);

                // debugger;
                var i;
                for (i = start; i <= end; i++) {
                    elements.push(i);
                }

                var leftPageEnd = start - 1;
                // Is current page far away from page 2?
                if (start > 3) {
                    elements.unshift('...');
                    leftPageEnd = 2;
                }

                for (i = leftPageEnd; i >= 1; --i)
                    elements.unshift(i);

                var rightPageStart = end + 1;
                if (end < this.lastPage - 3) {
                    elements.push('...');
                    rightPageStart = this.lastPage - 1;
                }

                for (i = rightPageStart; i <= this.lastPage; ++i)
                    elements.push(i);

                return elements;
            }
        }
    }
</script>

<style scoped>

</style>