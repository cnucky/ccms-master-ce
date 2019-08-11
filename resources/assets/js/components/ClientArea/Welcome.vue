<template>
    <flex-container>
        <div class="content">
            <div class="title m-b-md">
                Cloud Computing
            </div>
        </div>
    </flex-container>
</template>

<script>
    import FlexContainer from "../FlexContainer";
    export default {
        name: "Welcome",
        components: {FlexContainer}
    }
</script>

<style scoped>
    @import "https://fonts.googleapis.com/css?family=Raleway:100,600";

    .content, .links {
        font-family: 'Raleway', sans-serif;
    }

    .content {
        text-align: center;
    }

    .title {
        font-size: 84px;
    }

    .links > a {
        color: #636b6f;
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
    }

    .m-b-md {
        margin-bottom: 30px;
    }
</style>