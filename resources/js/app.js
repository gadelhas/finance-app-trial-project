require('./bootstrap');

import Vue from 'vue';

import BalanceComponent from './components/transactions/Balance.vue'
import ItemComponent from './components/transactions/Item.vue'
import ItemsGroupComponent from './components/transactions/Group.vue'

Vue.component('Balance', BalanceComponent);
Vue.component('Item', ItemComponent);
Vue.component('Group', ItemsGroupComponent);

const app = new Vue({
    el: '#app',
    data: {
        balance: 0.00,
    },

});
