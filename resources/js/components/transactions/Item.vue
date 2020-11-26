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
                <a href="#" v-on:click="openCloseEditor">EDIT</a> <a href="#">DELETE</a>
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
            return this.entry.amount.toString().replace('-', '');
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
            axios.patch('/transactions/' + this.entry.id, {label: label, date: date, amount:amount})
                .then(result => {
                    if (result.data == null) {
                        return;
                    }
                    // Update only when receive data from server.
                    this.entry.label = label;
                    this.entry.date = date;
                    this.entry.amount = parseFloat(amount);

                    console.log(result);

                    this.$store.dispatch('getBalance');
                });


            console.log(this.idx);
            this.$emit('changeEntry', {idx: this.idx, entry: this.entry});

            this.openCloseEditor();
        }
    }
}
</script>
