<template>
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <span class="flex-grow text-gray-500 font-bold text-sm uppercase tracking-tight">{{ humanDate }}</span>
            <span class="text-lg text-gray-500 font-bold">- $50.<span class="text-sm">00</span></span>
        </div>

        <div v-for="transaction in transactions" :id="transaction.id">
            <Item :label="transaction.label" :date="transaction.date" :amount="transaction.amount"></Item>
        </div>
    </div>
</template>

<script>
export default {
    props: ['transactions', 'date'],
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
        }
    }
}
</script>
