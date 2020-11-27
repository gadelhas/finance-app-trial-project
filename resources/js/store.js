import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        balance: 0,
        transactions: {},
    },
    getters: {
        balance: state => state.balance,
        transactions: state => state.transactions,
    },
    mutations: {
        setBalance(state, balance) {
            state.balance = (Math.round(balance * 100) / 100).toFixed(2);
        },
        setTransactions(state, transactions) {
            state.transactions = transactions;
        },
    },
    actions: {
        getBalance({commit}) {
            return new Promise((resolve, reject) => {
                axios.get('/balance')
                    .then(result => {
                        commit('setBalance', result.data);
                        resolve();
                    })
                    .catch(error => {
                        reject(error.response && error.response.data.message || 'Error.');
                    });
            });
        },
        getTransactions({commit}) {
            return new Promise((resolve, reject) => {
                axios.get('/transactions/all')
                    .then(result => {
                        commit('setTransactions', result.data);
                        resolve();
                    })
                    .catch(error => {
                        reject(error.response && error.response.data.message || 'Error.');
                    });
            });
        },
    }
})
