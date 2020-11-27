<template>
    <!-- This example requires Tailwind CSS v2.0+ -->
    <div class="fixed z-10 inset-0 overflow-y-auto insertModal" id="insertModal">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!--
              Background overlay, show/hide based on modal state.

              Entering: "ease-out duration-300"
                From: "opacity-0"
                To: "opacity-100"
              Leaving: "ease-in duration-200"
                From: "opacity-100"
                To: "opacity-0"
            -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <!--
              Modal panel, show/hide based on modal state.

              Entering: "ease-out duration-300"
                From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                To: "opacity-100 translate-y-0 sm:scale-100"
              Leaving: "ease-in duration-200"
                From: "opacity-100 translate-y-0 sm:scale-100"
                To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            -->
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                                Add Balance Entry
                            </h3>

                            <div class="inputs px-4 my-10">
                                <div class="grid grid-cols-3">
                                    <div class="px-4">
                                        <label for="label"
                                               class="block text-sm font-medium text-gray-700 uppercase">Label</label>
                                        <input type="text" id="label" name="label"
                                               class="form-input rounded-md shadow-sm w-full">
                                    </div>
                                    <div class="px-4">
                                        <label for="date"
                                               class="block text-sm font-medium text-gray-700 uppercase">Date</label>
                                        <input type="date" id="date" name="date"
                                               class="form-input rounded-md shadow-sm w-full">
                                    </div>
                                    <div class="px-4">
                                        <label for="amount"
                                               class="block text-sm font-medium text-gray-700 uppercase">Amount</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                              <span class="text-gray-500 sm:text-sm">
                                                $
                                              </span>
                                            </div>
                                            <input type="text" id="amount" name="amount"
                                                   class="form-input rounded-md shadow-sm w-full pl-7 pr-12">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="createTransaction"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save Entry
                    </button>
                    <button type="button" @click="close"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    name: 'insert-modal',
    methods: {
        open() {
            console.log("OPENING IN COMPOENNT");
        },
        close() {
            console.log("trying to close");
            this.$emit('close');
        },
        createTransaction(e) {
            e.preventDefault();

            console.log("updateTransaction");
            let editorEl = document.getElementById("insertModal");
            let labelEl = editorEl.querySelector('#label');
            let label = labelEl.value;
            let dateEl = editorEl.querySelector('#date');
            let date = dateEl.value;
            let amountEl = editorEl.querySelector('#amount');
            let amount = amountEl.value;

            //Update on server.
            axios.post('/transactions/', {label: label, date: date, amount: amount})
                .then(result => {
                    if (result.data == null) {
                        console.log(result.data);
                        return;
                    }

                    // NEed to add transaction to list.
                    this.$store.dispatch('getBalance');
                    this.$emit('changeEntry', {idx: this.idx, entry: this.entry});
                    this.$store.dispatch('getTransactions');

                    this.close();

                })
                .catch((err) => {
                    console.log("ERROR");
                    if (err.response.status === 422) {
                        Object.entries(err.response.data.errors).forEach(([key, value]) => {
                            editorEl.querySelector("#"+key).classList.add('border-red-500');
                        });
                    }
                });


            console.log(this.idx);

        }
    }
}
</script>
