@extends('ClientArea.Desktop.layouts.app')

@section('content')
    <slide-fade-router-view v-bind:style="{ paddingRight: this.$store.getters.isLoggedIn ? '30px' : '0' }"></slide-fade-router-view>
@endsection