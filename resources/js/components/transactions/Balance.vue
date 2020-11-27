<template>
    <div class="my-auto text-right font-bold text-xs uppercase tracking-tight leading-7 text-gray-400">
        Total Balance
        <span class="block text-3xl font-normal" :class="{'text-green-500': isPositive}">${{ cleanBalance }}</span>
    </div>
</template>

<script>
export default {
    name: 'Balance',
    computed: {
        isPositive() {
            return this.balance > 0;
        },
        balance() {
            return this.$store.state.balance;
        },
        cleanBalance() {
            return this.balance.toString().replace('-', '');
        }
    }
}
</script>
