<template>
    <v-card>
        <v-card-title>
            <v-spacer></v-spacer>
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Filter"
                single-line
                hide-details>
            </v-text-field>
        </v-card-title>
        <template>
            <v-data-table
                sort-by="created_at"
                sort-desc
                :headers="headers"
                :items="monitors"
                :items-per-page="10"
                :search="search"
                :single-expand="singleExpand"
                :expanded.sync="expanded"
                item-key="id"
                show-expand
                class="elevation-1"
                :footer-props="{showFirstLastPage: true}">

                <template v-slot:top>
                    <v-toolbar flat color="white">
                        <!--<v-toolbar-title>My Monitors</v-toolbar-title>
                        <v-divider
                        class="mx-4"
                        inset
                        vertical></v-divider>-->
                        <v-spacer></v-spacer>
                        <v-dialog v-model="dialog" max-width="500px">
                            <template v-slot:activator="{ on }">
                                <v-btn color="indigo" dark class="mb-2 mr-10" v-on="on" @click="newItem()">Wizard Add <v-icon>auto_fix_high</v-icon></v-btn>
                            </template>
                            <v-card>
                            </v-card>
                        </v-dialog>
                        <span> </span>
                        <v-dialog v-model="dialog" max-width="500px">
                            <template v-slot:activator="{ on }">
                                <v-btn color="indigo" dark class="mb-2" v-on="on" @click="newItem()">Quick Add <v-icon>add</v-icon></v-btn>
                            </template>
                            <v-card>
                                <v-card-title>
                                    <span class="headline">{{ formTitle }}</span>
                                </v-card-title>

                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-select v-model="editedItem.monitor_type" :items="monitorTypeOptions" label="Monitor Type"></v-select>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field v-model="editedItem.monitor_source" label="Source"></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-select v-model="editedItem.monitor_schedule" :items="monitorScheduleOptions" label="Schedule"></v-select>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-select v-model="editedItem.monitor_alert" :items="monitorAlertOptions" label="Alert"></v-select>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-select v-model="editedItem.monitor_state" :items="monitorStateOptions" label="State"></v-select>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>

                                <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                                <v-btn color="blue darken-1" text @click="save">Save</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>

                <template v-slot:[`item.action`]="{ item }">
                    <v-icon
                        small
                        class="mr-2"
                        @click="editItem(item)">
                        edit
                    </v-icon>
                    <v-icon
                        small
                        @click="deleteItem(item)">
                        delete
                    </v-icon>
                </template>

                <template v-slot:footer>
                    <div align="center"><v-chip @click="initialize" dense><v-icon>refresh</v-icon></v-chip></div>
                </template>

                <template v-slot:[`item.monitor_last`]="{ item }">
                    <v-chip>{{ formatDateToTime(item.monitor_last) }}</v-chip>
                </template>
                <template v-slot:[`item.monitor_next`]="{ item }">
                    <v-chip>{{ formatDateToTime(item.monitor_next) }}</v-chip>
                </template>
                <template v-slot:[`item.monitor_state`]="{ item }">
                    <v-chip :color="getStateColor(item.monitor_state)"><v-icon color="white">{{ getStateIcon(item.monitor_state) }}</v-icon></v-chip>
                    <!--<v-chip>{{item.monitor_state}}</v-chip>-->
                </template>
                <template v-slot:[`item.created_at`]="{ item }">
                    <v-chip>{{ formatDate(item.created_at) }}</v-chip>
                </template>
                <template v-slot:[`item.updated_at`]="{ item }">
                    <v-chip>{{ formatDate(item.updated_at) }}</v-chip>
                </template>
                <template v-slot:expanded-item="{ headers, item }" left>
                    <td :colspan="headers.length">
                        <v-chip
                            class="ma-2"
                            color="green"
                            text-color="white"
                            >
                            <v-avatar
                                left
                                class="green darken-4"
                            >
                                {{item.errors_last_1hour}}
                            </v-avatar>
                            Errors last hour
                        </v-chip>
                        <v-chip
                            class="ma-2"
                            color="green"
                            text-color="white"
                            >
                            <v-avatar
                                left
                                class="green darken-4"
                            >
                                {{item.errors_last_1day}}
                            </v-avatar>
                            Errors last day
                        </v-chip>
                                                <v-chip
                            class="ma-2"
                            color="green"
                            text-color="white"
                            >
                            <v-avatar
                                left
                                class="green darken-4"
                            >
                                {{item.errors_last_1week}}
                            </v-avatar>
                            Errors last week
                        </v-chip>
                    </td>
                </template>

            </v-data-table>
        </template>
    </v-card>
</template>
<script>
    export default {
        data: () => ({
                currentPage: 1,
                monitorTypeOptions:  [{value: 'http',text: 'HTTP'},{value: 'ping',text: 'PING'},{value: 'dns',text: 'DNS'}],
                monitorScheduleOptions: [{value: 5,text: '5 minutes'},{value: 15,text: '15 minutes'},{value: 30,text: '30 minutes'}],
                monitorAlertOptions: [{value: 1,text: 'Alert after 1'},{value: 2,text: 'Alert after 2'},{value: 3,text: 'Alert after 3'}],
                monitorStateOptions: [{value: -1,text: 'Disabled'},{value: 0,text: 'Paused'},{value: 1,text: 'Enabled'}],
                dialog: false,
                search: "",
                totalMonitors: 0,
                monitors: [],
                editedIndex: -1,
                editedItem: {
                    id: -1,
                    monitor_id: 0,
                    monitor_type: '',
                    monitor_source: '',
                    monitor_schedule: 5,
                    monitor_alert: 1,
                    monitor_next: '',
                    monitor_last: '',
                    created_at: '',
                    updated_at: '',
                    monitor_state: 1
                },
                defaultItem: {
                    id: -1,
                    monitor_id: 0,
                    monitor_type: '',
                    monitor_source: '',
                    monitor_schedule: 5,
                    monitor_alert: 1,
                    monitor_next: '',
                    monitor_last: '',
                    created_at: '',
                    updated_at: '',
                    monitor_state: 1
                },
                pagination: {},
                expanded: [],
                singleExpand: true,
                headers: [
                    { text: "Monitor", value: "monitor_id" },
                    { text: "Type", value: "monitor_type" },
                    { text: "Source (address)", value: "monitor_source" },
                    { text: "Schedule (mins)", value: "monitor_schedule" },
                    { text: "Alert (threshold)", value: "monitor_alert" },
                    { text: "Last", value: "monitor_last" },
                    { text: "Next", value: "monitor_next" },
                    { text: "State", value: "monitor_state", align: "center" },
                    { text: "Created", value: "created_at"},
                    { text: "Updated", value: "updated_at" },
                    { text: " ", value: "action", sortable: false }
                ]
            }),
        watch: {
            params: {
                handler() {
                    this.getMonitors().then(data => {
                        console.log("GETDATA");
                        this.monitors = data;
                        this.totalMonitors = data.length;
                    });
                },
                deep: true
            },
        },
        mounted() {
            this.getMonitors().then(data => {
                this.monitors = data;
                this.totalMonitors = data.length;
            });
        },
        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'New Monitor' : 'Edit Monitor #' + this.editedItem.monitor_id
            },
        },
        methods: {
            initialize()
            {
                this.monitors = [];
                this.search = "";
                this.currentPage = 1;
                this.getMonitors().then(data => {
                    console.log("GETDATA");
                    setTimeout(() => {
                        this.monitors = data;
                        this.totalMonitors = data.length;
                    }, 300)
                });
            },
            getMonitors() {
                return axios.get('/api/monitors', {responseType: 'json'})
                    .then(response => {return response.data});
            },
            formatDateToTime: function(value){
                return moment(value).format('hh:mm');
            },
            formatDate: function(value){
                return moment(value).format('DD/MM/YY');
            },
            getStateColor: function(state){
                var value = parseInt(state, 10);
                console.log("get state color" + value);
                switch(value){
                    case -1: return 'red';
                    case 0: return 'orange';
                    case 1: return 'green';
                }
            },
            getStateIcon: function(state){
                var value = parseInt(state, 10);
                switch(value){
                    case -1: return 'cancel';
                    case 0: return 'pause';
                    case 1: return 'done';
                }
            },
            newItem ()
            {
                this.editedIndex = -1;
                this.editedItem = Object.assign({}, this.defaultItem);
            },
            editItem (item) {
                this.editedIndex = this.monitors.indexOf(item)
                this.editedItem = Object.assign({}, item)
                this.dialog = true
                console.log(this.editedItem.monitor_schedule);
                console.log(this.editedItem.monitor_state);
            },
            deleteItem (item) {
                const index = this.monitors.indexOf(item)
                this.monitors.splice(index, 1);
                axios.post('/api/monitors/delete', {
                    monitor_id: item.monitor_id
                })
            },
            close () {
                this.dialog = false
                setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
                }, 300)
            },
            save () {
                if (this.editedIndex > -1) { // edit
                    Object.assign(this.monitors[this.editedIndex], this.editedItem);
                    console.log(this.editedItem);
                    this.updateOne(this.editedItem).then(data => {
                        //this.monitors.push(data);
                    })
                } else { // new
                    this.createNew(this.editedItem).then(data => {
                        this.monitors.push(data);
                    })
                }
                // Issue: methods in data table v-slot is not triggered, i.e. monitor_state icon and color is not computed getStateIcon and getStateColor
                this.close()
            },
            createNew(editedItem){
                return axios.post('/api/monitors/create', {
                    monitor_type: editedItem.monitor_type,
                    monitor_source: editedItem.monitor_source,
                    monitor_schedule: editedItem.monitor_schedule,
                    monitor_alert: editedItem.monitor_alert
                })
                .then(
                    response => {return response.data}
                )
                .catch(function (error) {
                    console.log(error);
                });
            },
            updateOne(editedItem){
                return axios.post('/api/monitors/edit', {
                    id: editedItem.id,
                    monitor_type: editedItem.monitor_type,
                    monitor_source: editedItem.monitor_source,
                    monitor_schedule: editedItem.monitor_schedule,
                    monitor_alert: editedItem.monitor_alert,
                    monitor_state: editedItem.monitor_state
                })
                .then(
                    response => {return response.data}
                )
                .catch(function (error) {
                    console.log(error);
                });
            }
        }
    }
</script>
