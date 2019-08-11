<template>
    <form class="ui form" v-on:submit.prevent="$emit('query', start, end)">
        <div class="ui grid">
            <div class="seven wide column">
                <div class="ui mini fluid labeled input">
                    <div class="ui label" style="line-height: 17px;">
                        起始时间
                    </div>
                    <input type="month" v-model="start">
                </div>
            </div>
            <div class="seven wide column">
                <div class="ui mini fluid labeled input">
                    <div class="ui label" style="line-height: 17px;">
                        结束时间
                    </div>
                    <input type="month" v-model="end">
                </div>
            </div>
            <div class="two wide column" style="line-height: 34px;">
                <button type="submit" class="ui tiny button">查询</button>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        name: "MonthInput",
        data: function () {
            return {
                start: "",
                end: "",
            };
        }
    }
</script>

<style scoped>

</style>