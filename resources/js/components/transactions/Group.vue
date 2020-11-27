<template>
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <span class="flex-grow text-gray-500 font-bold text-sm uppercase tracking-tight">{{ humanDate }}</span>
            <span class="text-lg text-gray-500 font-bold">{{ dailyBalance < 0 ? '-' : ''}} ${{ cleanDailyBalance }}</span>
        </div>

        <div v-for="(transaction, idx) in transactions" :id="transaction.id">
            <Item :key="idx" :idx="idx" :transaction="transactions[idx]" @changeEntry="changeEntry" @deleteEntry="deleteEntry"></Item>
        </div>
    </div>
</template>

<script>
export default {
    props: ['transactions', 'date'],
    data() {
        return {
            entries: {...this.transactions},
        }
    },
    mounted() {
    },
    computed: {
        humanDate() {
            let today = new Date();
            let humanDate = new Date(this.date);

            if (humanDate.getDate() === today.getDate() &&
                humanDate.getMonth() === today.getMonth() &&
                humanDate.getFullYear() === today.getFullYear()) {
                humanDate = "Today";
            } else {
                humanDate = this.date;
            }

            return humanDate;
        },
        dailyBalance() {
            let bal = 0;
            for (let i = 0; i < this.transactions.length; i++) {
                bal += parseFloat(this.transactions[i].amount);
            }

            return (Math.round(bal * 100) / 100).toFixed(2);;
        },
        cleanDailyBalance() {
            return this.dailyBalance.toString().replace('-', '');
        }
    },
    methods: {
        changeEntry(obj) {
            this.$store.dispatch('getTransactions');
        },
        deleteEntry(obj) {
            console.log("received delete event");
        }
    }
}
</script>
