<!-- resources/js/components/contracts/steps/StepContractDetails.vue -->
<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">{{ $t('contract.contract_details') }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Contract basic details -->
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.price') }} *</label>
          <div class="relative">
            <input type="number" v-model.number="formData.price"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
              :placeholder="$t('contract.enter_price')" min="0">
            <span class="absolute right-2 top-2 text-gray-500">VND</span>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.pay_period') }} *</label>
          <select v-model="formData.pay_period"
            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300">
            <option value="3">3 {{ $t('contract.months') }}</option>
            <option value="6">6 {{ $t('contract.months') }}</option>
            <option value="12">12 {{ $t('contract.months') }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.number_of_tenant_current') }}
            *</label>
          <input type="number" v-model.number="formData.number_of_tenant_current"
            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
            min="1">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.note') }}</label>
          <textarea v-model="formData.note"
            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
            rows="3" :placeholder="$t('contract.enter_note')"></textarea>
        </div>
      </div>

      <!-- Utilities and dates -->
      <div class="space-y-4">
        <!-- Electricity section -->
        <div class="p-3 border border-gray-200 rounded-md">
          <h3 class="font-medium mb-2">{{ $t('contract.electricity_details') }}</h3>

          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.electricity_pay_type')
              }}</label>
            <select v-model="formData.electricity_pay_type"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300">
              <option value="1">{{ $t('contract.per_person') }}</option>
              <option value="2">{{ $t('contract.fixed_price') }}</option>
              <option value="3">{{ $t('contract.by_usage') }}</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.electricity_price') }} *</label>
            <div class="relative">
              <input type="number" v-model.number="formData.electricity_price"
                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
                min="0">
              <span class="absolute right-2 top-2 text-gray-500">VND</span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.electricity_number_start') }}
              *</label>
            <input type="number" v-model.number="formData.electricity_number_start"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
              min="0">
          </div>
        </div>

        <!-- Water section -->
        <div class="p-3 border border-gray-200 rounded-md">
          <h3 class="font-medium mb-2">{{ $t('contract.water_details') }}</h3>

          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.water_pay_type') }}</label>
            <select v-model="formData.water_pay_type"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300">
              <option value="1">{{ $t('contract.per_person') }}</option>
              <option value="2">{{ $t('contract.fixed_price') }}</option>
              <option value="3">{{ $t('contract.by_usage') }}</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.water_price') }} *</label>
            <div class="relative">
              <input type="number" v-model.number="formData.water_price"
                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
                min="0">
              <span class="absolute right-2 top-2 text-gray-500">VND</span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.water_number_start') }}
              *</label>
            <input type="number" v-model.number="formData.water_number_start"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
              min="0">
          </div>
        </div>

        <!-- Contract dates -->
        <div class="p-3 border border-gray-200 rounded-md">
          <h3 class="font-medium mb-2">{{ $t('contract.contract_dates') }}</h3>

          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.start_date') }} *</label>
            <input type="date" v-model="formData.start_date"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('contract.end_date') }} *</label>
            <input type="date" v-model="formData.end_date"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
              :min="formData.start_date">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, watch } from 'vue';

export default {
  name: 'StepContractDetails',
  props: {
    formData: {
      type: Object,
      required: true
    }
  },
  emits: ['update', 'validate'],

  setup(props, { emit }) {
    // Calculate suggested end date based on start date and pay period
    const calculateEndDate = () => {
      if (!props.formData.start_date) return;

      const startDate = new Date(props.formData.start_date);
      const payPeriod = parseInt(props.formData.pay_period);

      const endDate = new Date(startDate);
      endDate.setMonth(endDate.getMonth() + payPeriod);

      const formattedEndDate = endDate.toISOString().split('T')[0];
      emit('update', 'end_date', formattedEndDate);
    };

    // Watch for changes in start date or pay period to update end date
    watch(() => [props.formData.start_date, props.formData.pay_period], () => {
      calculateEndDate();
    });

    onMounted(() => {
      // Calculate end date on mount if start date is already set
      if (props.formData.start_date && !props.formData.end_date) {
        calculateEndDate();
      }
    });

    return {};
  }
};
</script>