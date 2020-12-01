<template>
    <v-menu bottom left>
        <template v-slot:activator="{ on }">
            <v-btn
            dark
            icon
            v-on="on"
            >
            <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
        </template>

        <v-list>
            <v-list-item
            v-for="(item, i) in quickmenuitems"
            :key="i"
            @click="submitRoute(item)"
            >
            <v-list-item-title>{{ item.title }}</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-menu>
</template>
<script>
    export default {
        data: () => ({
            quickmenuitems: [
            { title: 'Settings', route: 'settings' },
            { title: 'Sign out', route: 'logout' }]
        }),
        methods: {
        submitRoute(item){
            if(item.route == 'logout'){
                document.getElementById('logout-form').submit();
            }
            else{
                window.location.href = item.route;
            }
        }
    }
}
</script>