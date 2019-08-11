<template>
    <ul class="nav navbar-nav refresh">
        <li class="divided">
            <a href="#" v-on:click.prevent="back"><i class="arrow left icon"></i></a>
        </li>
    </ul>
</template>

<script>
    export default {
        name: "IconBackButton",
        methods: {
            back: function () {
                this.$router.back();
            }
        }
    }
</script>

<style scoped>

</style>