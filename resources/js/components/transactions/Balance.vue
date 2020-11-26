<template>
    <div class="my-auto text-right font-bold text-xs uppercase tracking-tight leading-7 text-gray-400">
        Total Balance
        <span class="block text-3xl font-normal text-green-500">${{ balance }}</span>
    </div>
</template>

<script>
export default {
    name: 'Balance',
    mounted() {
        console.log('Component mounted.')
        if (localStorage.balance) {
            this.balance = localStorage.balance;
        }
    },
    computed: {
        balance() {
            return this.$store.state.balance;
        }
    }
}
</script>
