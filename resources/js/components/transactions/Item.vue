<template>
    <div class="transaction-item">
        <div class="flex items-center mb-4 px-4 py-2 shadow-md bg-white rounded-md">
            <div class="flex-grow">
                <div class="font-bold">
                    {{ label }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ date }}
                </div>
            </div>
            <div class="text-lg font-bold" :class="{'text-green-500': isPositive}">
                {{ amount < 0 ? '-' : '' }} ${{ amount }}
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'Item',
    props: ['label', 'date', 'amount'],
    mounted() {
    },
    computed: {
        isPositive() {
            return this.amount > 0;
        }
    }
}
</script>
