require('./bootstrap');

import Vue from 'vue';
import store from './store';

import BalanceComponent from './components/transactions/Balance.vue'
import ItemComponent from './components/transactions/Item.vue'
import ItemsGroupComponent from './components/transactions/Group.vue'

Vue.component('Balance', BalanceComponent);
Vue.component('Item', ItemComponent);
Vue.component('Group', ItemsGroupComponent);

const app = new Vue({
    el: '#app',
    store,
    created() {
        store.dispatch('getBalance');
    }

});

global.Vue = Vue;
global.app = app;
