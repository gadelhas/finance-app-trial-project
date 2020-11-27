<template>
    <div class="my-auto text-right font-bold text-xs uppercase tracking-tight leading-7 text-gray-400">
        Total Balance
        <span class="block text-3xl font-normal text-green-500">${{ balance }}</span>
    </div>
</template>

<script>
export default {
    name: 'Balance',
    computed: {
        balance() {
            return this.$store.state.balance;
        }
    }
}
</script>
