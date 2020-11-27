import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        balance: 0,
    },
    getters: {
        balance: state => state.balance,
    },
    mutations: {
        setBalance(state, balance) {
            state.balance = (Math.round(balance * 100) / 100).toFixed(2);;
        },
    },
    actions: {
        getBalance({ commit }) {
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
    }
})
