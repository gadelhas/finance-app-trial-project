require('./bootstrap');

import Vue from 'vue';
import store from './store';

import BalanceComponent from './components/transactions/Balance.vue'
import ItemComponent from './components/transactions/Item.vue'
import ItemsGroupComponent from './components/transactions/Group.vue'
import insertModalComponent from "./components/transactions/insertModal";
import insertCsvModalComponent from "./components/transactions/insertCsvModal";

Vue.component('Balance', BalanceComponent);
Vue.component('Item', ItemComponent);
Vue.component('Group', ItemsGroupComponent);
Vue.component('insertModal', insertModalComponent);
Vue.component('insertCsvModal', insertCsvModalComponent);

const app = new Vue({
    el: '#app',
    store,
    created() {
        store.dispatch('getBalance');
    },
    data() {
        return {
            isInsertModalVisible: false,
            isInsertCsvModalVisible: false,
        }
    },
    methods: {
        showInsertModal() {
            this.$refs.insertModal.open();
            this.isInsertModalVisible = true;
        },
        closeInsertModal() {
            this.isInsertModalVisible = false;
        },
        showInsertCsvModal() {
            this.$refs.insertCsvModal.open();
            this.isInsertCsvModalVisible = true;
        },
        closeInsertCsvModal() {
            this.isInsertCsvModalVisible = false;
        },
        importCsv() {
            // disable buttons
            document.getElementById('insertModalButton').disabled = true;
            document.getElementById('insertCsvModalButton').disabled = true;
            // show message
            document.getElementById('jobRunning-message').classList.remove('hidden');
        }
    },

});

global.Vue = Vue;
global.app = app;
