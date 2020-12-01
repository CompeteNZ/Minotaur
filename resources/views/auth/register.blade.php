@extends('layouts.app')

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
                    <v-form id="registerForm" ref="registerForm" method="post" @submit="submitRegisterForm">
                        @csrf
                        <v-toolbar
                        color="indigo"
                        dark
                        flat>
                            <v-toolbar-title>Register a new account</v-toolbar-title>
                        </v-toolbar>
                        <v-card-text>
                        <v-text-field
                            id="name"
                            name="name"
                            label="Name"
                            v-model="registerForm.name"
                            :counter="30"
                            :error-messages="nameRegisterErrors"
                            prepend-icon="person"
                            type="text"
                            required
                            @change="$v.registerForm.name.$touch()"
                            @blur="$v.registerForm.name.$touch()"
                            >
                            </v-text-field>

                            <v-text-field
                            id="email"
                            name="email"
                            label="Email"
                            v-model="registerForm.email"
                            :error-messages="emailRegisterErrors"
                            prepend-icon="email"
                            type="text"
                            required
                            @change="$v.registerForm.email.$touch()"
                            @blur="$v.registerForm.email.$touch()"
                            >
                            </v-text-field>

                            <v-text-field
                            id="password"
                            name="password"
                            label="Password"
                            v-model="registerForm.password"
                            :error-messages="passwordRegisterErrors"
                            prepend-icon="lock"
                            type="password"
                            required
                            @change="$v.registerForm.password.$touch()"
                            @blur="$v.registerForm.password.$touch()"
                            >
                            </v-text-field>

                            <v-text-field
                            id="confirmPassword"
                            name="password_confirmation"
                            label="Confirm Password"
                            v-model="registerForm.confirmPassword"
                            :error-messages="confirmPasswordRegisterErrors"
                            prepend-icon="lock"
                            type="password"
                            required
                            @change="$v.registerForm.confirmPassword.$touch()"
                            @blur="$v.registerForm.confirmPassword.$touch()"
                            >
                            </v-text-field>

                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="indigo" type="submit" dark>Register now</v-btn>
                        </v-card-actions>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
@endsection




@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
