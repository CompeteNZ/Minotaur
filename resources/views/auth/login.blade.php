@extends('layouts.app')

@push('scripts')
<script src="{{ mix('js/login.js') }}" defer></script>
@endpush

@section('content')
<template>
    <v-container
        class="fill-height"
        fluid>

        <v-row
          align="center"
          justify="center">

        <v-col
            cols="12"
            sm="8"
            md="4">
            @if ($errors->any())
            @foreach ($errors->all() as $error)
                <v-alert type="error" outlined>{!! $error !!}</v-alert>
                @break
            @endforeach
            @endif  
            <v-card class="elevation-12">
                    <v-form id="loginForm" ref="loginForm" method="post" @submit="submitLoginForm">
                        @csrf
                        <v-toolbar
                        color="indigo"
                        dark
                        flat>
                            <v-toolbar-title>Sign in to your account</v-toolbar-title>
                        </v-toolbar>
                        <v-card-text>
                            <v-text-field
                            id="email"
                            name="email"
                            label="Email"
                            v-model="loginForm.email"
                            :error-messages="emailLoginErrors"
                            prepend-icon="person"
                            type="text"
                            required
                            @input="$v.loginForm.email.$touch()"
                            @blur="$v.loginForm.email.$touch()"
                            >
                            </v-text-field>

                            <v-text-field
                            id="password"
                            name="password"
                            label="Password"
                            v-model="loginForm.password"
                            :error-messages="passwordLoginErrors"
                            prepend-icon="lock"
                            type="password"
                            required
                            @input="$v.loginForm.password.$touch()"
                            @blur="$v.loginForm.password.$touch()">
                            </v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="indigo" type="submit" dark>Sign in</v-btn>
                        </v-card-actions>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
@endsection