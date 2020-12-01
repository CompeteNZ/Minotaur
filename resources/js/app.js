/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import vuetify from '~/plugins/vuetify'

//import {mapState} from 'vuex';

import { required, email, minLength, maxLength, sameAs } from 'vuelidate/lib/validators';

require('./bootstrap');

window.moment = require('moment');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('monitors-datatable', require('./components/MonitorsDatatable.vue').default);
Vue.component('user-quick-menu', require('./components/UserQuickMenu.vue').default);
//Vue.component('login-form', require('./components/LoginForm.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    vuetify,
    data: () => ({
      drawer: false,
      group: false,
      loginForm: {
        email: '',
        password: '',
      },
      registerForm: {
        name: '',
        email: '',
        password: '',
        confirmPassword: ''        
      }
    }),
    props: {
      source: String,
    },
    validations: {
        loginForm: {
          email: {required, email},
          password: {required, minLength: minLength(10)}
        },
        registerForm: {
          name: {required, maxLength: maxLength(30)},
          email: {required, email},
          password: {required, minLength: minLength(10)},
          confirmPassword: {required, passwordSameAs: sameAs('password')}          
        }
    },
    computed: {
      emailLoginErrors () {
        const errors = []
        if (!this.$v.loginForm.email.$dirty) return errors
        !this.$v.loginForm.email.email && errors.push('Must be a valid email address')
        !this.$v.loginForm.email.required && errors.push('Email is required')
        return errors
      },
      passwordLoginErrors () {
        const errors = []
        if (!this.$v.loginForm.password.$dirty) return errors
        !this.$v.loginForm.password.minLength && errors.push('Password must be at least 10 characters long')
        !this.$v.loginForm.password.required && errors.push('Password is required')
        return errors
      },
      nameRegisterErrors () {
        const errors = []
        if (!this.$v.registerForm.name.$dirty) return errors
        !this.$v.registerForm.name.maxLength && errors.push('Name must be at most 30 characters long')
        !this.$v.registerForm.name.required && errors.push('Name is required.')
        return errors
      },
      emailRegisterErrors () {
        const errors = []
        if (!this.$v.registerForm.email.$dirty) return errors
        !this.$v.registerForm.email.email && errors.push('Must be a valid email address')
        !this.$v.registerForm.email.required && errors.push('Email is required')
        return errors
      },
      passwordRegisterErrors () {
        const errors = []
        if (!this.$v.registerForm.password.$dirty) return errors
        !this.$v.registerForm.password.minLength && errors.push('Password must be at least 10 characters long')
        !this.$v.registerForm.password.required && errors.push('Password is required')
        return errors
      },
      confirmPasswordRegisterErrors () {
        const errors = []
        if (!this.$v.registerForm.confirmPassword.$dirty) return errors
        !this.$v.registerForm.confirmPassword.passwordSameAs && errors.push('Password and Confirm Password must match')
        !this.$v.registerForm.confirmPassword.required && errors.push('Confirm Password is required')
        return errors
      },
    },
    methods: {
      submitLoginForm () {
        this.$v.loginForm.$touch();
        if(this.$v.loginForm.$invalid)
        {
          event.preventDefault()
        }
      },
      submitRegisterForm () {
        this.$v.registerForm.$touch();
        if(this.$v.registerForm.$invalid)
        {
          event.preventDefault()
        }
      },
    }
});