<template>
    <div class="transaction-item mb-4 shadow-md bg-white rounded-md">
        <div class="flex items-center px-4 py-2">
            <div class="flex-grow">
                <div class="font-bold">
                    {{ entry.label }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ entry.date }}
                </div>
            </div>
            <div class="text-lg mr-10 rollover-actions">
                <a href="#" @click="openCloseEditor">EDIT</a> <a href="#" @click="deleteTransaction">DELETE</a>
            </div>
            <div class="text-lg font-bold" :class="{'text-green-500': isPositive}">
                {{ entry.amount < 0 ? '-' : '' }} ${{ cleanAmount }}
            </div>
        </div>
        <div class="transaction-editor w-full bg-white hidden" :id="transactionEditorId">
            <form action="#" method="POST">
                <div class="inputs px-4 py-2 my-10">
                    <div class="grid grid-cols-3">
                        <div class="px-4">
                            <label for="label"
                                   class="block text-sm font-medium text-gray-700 uppercase">Label</label>
                            <input type="text" id="label" name="label" class="form-input rounded-md shadow-sm w-full"
                                   :value="entry.label">
                        </div>
                        <div class="px-4">
                            <label for="date"
                                   class="block text-sm font-medium text-gray-700 uppercase">Date</label>
                            <input type="text" id="date" name="date" class="form-input rounded-md shadow-sm w-full"
                                   :value="entry.date">
                        </div>
                        <div class="px-4">
                            <label for="amount"
                                   class="block text-sm font-medium text-gray-700 uppercase">Amount</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                              <span class="text-gray-500 sm:text-sm">
                                $
                              </span>
                                </div>
                                <input type="text" id="amount" name="amount"
                                       class="form-input rounded-md shadow-sm w-full pl-7 pr-12"
                                       :value="entry.amount">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-3 text-right sm:px-6">
                    <button type="cancel" v-on:click="openCloseEditor"
                            class="inline-flex items-center px-4 py-2 bg-blue-100 border border-transparent rounded-md font-semibold text-xs text-blue-900 uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Cancel
                    </button>
                    <button type="submit" v-on:click="updateTransaction"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Update Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<style>
.transaction-item {
    transition: all 1s;
}

.transaction-item .rollover-actions {
    transition: 0.3s;
    display: none;
}

.transaction-item:hover .rollover-actions {
    transition: 0.3s;
    display: block;
}
</style>
<script>
export default {
    name: 'Item',
    props: ['transaction', 'idx'],
    mounted() {
    },
    data() {
        return {
            entry: {...this.transaction},
            csrf: document.head.querySelector('meta[name="csrf-token"]').content
        }
    },
    computed: {
        isPositive() {
            return this.entry.amount > 0;
        },
        transactionEditorId() {
            return "transaction-editor-" + this.entry.id;
        },
        cleanAmount() {
            let amount = (Math.round(this.entry.amount * 100) / 100).toFixed(2);
            return amount.toString().replace('-', '');
        }
    },
    methods: {
        openCloseEditor() {
            let el = document.getElementById("transaction-editor-" + this.entry.id);
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        },
        updateTransaction(e) {
            e.preventDefault();
            console.log("updateTransaction");
            let editorEl = document.getElementById(this.transactionEditorId);
            let label = editorEl.querySelector('#label').value;
            let date = editorEl.querySelector('#date').value;
            let amount = editorEl.querySelector('#amount').value;

            //Update on server.
            axios.patch('/transactions/' + this.entry.id, {label: label, date: date, amount: amount})
                .then(result => {
                    if (result.data == null) {
                        return;
                    }
                    // Update only when receive data from server.
                    this.entry.label = label;
                    this.entry.date = date;
                    this.entry.amount = parseFloat(amount);

                    this.$store.dispatch('getBalance');
                    this.$store.dispatch('getTransactions');

                    this.$emit('changeEntry', {idx: this.idx, entry: this.entry});
                    this.openCloseEditor();
                })
                .catch((err) => {
                    console.log("ERROR");
                    if (err.response.status === 422) {
                        Object.entries(err.response.data.errors).forEach(([key, value]) => {
                            let el = editorEl.querySelector("#" + key);
                            el.classList.add('border-red-500');
                            let errorEl = document.createElement("div");
                            errorEl.classList.add("flex");
                            errorEl.classList.add("items-center");
                            errorEl.classList.add("font-medium");
                            errorEl.classList.add("tracking-wide");
                            errorEl.classList.add("text-red-500");
                            errorEl.classList.add("text-xs");
                            errorEl.classList.add("mt-1");
                            errorEl.classList.add("ml-1");
                            errorEl.innerHTML = value;
                            el.parentNode.insertBefore(errorEl, el.nextSibling);
                        });
                    }
                });
            ;


        },
        deleteTransaction() {
            axios.delete('/transactions/' + this.entry.id)
                .then(result => {
                    if (result.data == null) {
                        return;
                    }

                    this.$store.dispatch('getBalance');
                    this.$store.dispatch('getTransactions');
                    this.$emit('deleteEntry', {idx: this.idx, entry: this.entry});
                })
        }
    }
}
</script>
