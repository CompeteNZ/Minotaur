<template></template>

<script>

import { required, email, minLength, maxLength, sameAs } from 'vuelidate/lib/validators';

export default{
    data: () => ({
      email: '',
      password: '',
    }),
    props: {
      source: String,
    },
    validations: {
      email: {required, email},
      password: {required, minLength: minLength(10)},
    },
    computed: {
      emailErrors () {
        const errors = []
        if (!this.$v.email.$dirty) return errors
        !this.$v.email.email && errors.push('Must be a valid email address')
        !this.$v.email.required && errors.push('Email is required')
        return errors
      },
      passwordErrors () {
        const errors = []
        if (!this.$v.password.$dirty) return errors
        !this.$v.password.minLength && errors.push('Password must be at least 10 characters long')
        !this.$v.password.required && errors.push('Password is required')
        return errors
      },
    },
    methods: {
      submit () {
        this.$v.$touch();
        console.log(this.$v.$invalid);
        console.log(this.$v.$error);
        if(this.$v.$invalid)
        {
          event.preventDefault()
        }
      },
      setEmail(value)
      {
        this.email = value;
      }
    },
};
</script>