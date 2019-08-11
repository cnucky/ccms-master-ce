<template>
    <div class="column">
        <dl>
            <dt v-if="typeof name === 'string' && name.length" class="name" v-bind:class="{ inline: inline }" v-bind:style="dtStyle">{{ name }}</dt>
            <dd class="value" v-bind:class="{ inline: inline }" v-bind:style="ddStyle"><slot></slot></dd>
        </dl>
    </div>
</template>

<script>
    export default {
        name: "Column",
        props: ["name", "inline", "dtStyle", "ddStyle"]
    }
</script>

<style scoped>
    dl {
        margin-top: 0;
    }

    dt, dd {
    }

    .column {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    .name {
        font-weight: 700;
        color: rgb(21, 26, 45);
    }

    .name:after {
        content: ":";
    }

    .value {
        color: rgb(74, 79, 98);
        margin: 16px 0 0;
    }

    .inline {
        display: inline-block;
    }

    dd.inline {
        margin-left: 8px !important;
    }
</style>