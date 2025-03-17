<!-- resources/js/components/contracts/steps/StepReview.vue -->
<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">{{ $t('contract.review_confirm') }}</h2>

    <div class="bg-gray-50 p-4 rounded-md mb-6">
      <p class="text-gray-700 mb-2">{{ $t('contract.review_instructions') }}</p>
    </div>

    <div class="space-y-6">
      <!-- Room Information -->
      <div class="border rounded-md overflow-hidden">
        <div class="bg-gray-100 px-4 py-2 font-medium">{{ $t('room.information') }}</div>
        <div class="p-4">
          <div v-if="formData.selectedRoom" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <p><span class="font-medium">{{ $t('apartment.name') }}:</span> {{ formData.selectedRoom.apartment.name }}
              </p>
              <p><span class="font-medium">{{ $t('room.room_number') }}:</span> {{ formData.selectedRoom.room_number }}
              </p>
            </div>
            <div>
              <p><span class="font-medium">{{ $t('room.max_tenant') }}:</span> {{ formData.selectedRoom.max_tenant }}
              </p>
              <p><span class="font-medium">{{ $t('room.default_price') }}:</span> {{
                formatCurrency(formData.selectedRoom.default_price) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tenant Information -->
      <div class="border rounded-md overflow-hidden">
        <div class="bg-gray-100 px-4 py-2 font-medium">{{ $t('tenant.information') }}</div>
        <div class="p-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <p>
                <span class="font-medium">{{ $t('tenant.name') }}:</span>
                {{ formData.is_create_tenant ? formData.name : selectedTenant?.name }}
              </p>
              <p>
                <span class="font-medium">{{ $t('tenant.tel') }}:</span>
                {{ formData.is_create_tenant ? formData.tel : selectedTenant?.tel }}
              </p>
            </div>
            <div>
              <p>
                <span class="font-medium">{{ $t('tenant.email') }}:</span>
                {{ formData.is_create_tenant ? formData.email : selectedTenant?.email }}
              </p>
              <p>
                <span class="font-medium">{{ $t('tenant.identity_card_number') }}:</span>
                {{ formData.is_create_tenant ? formData.identity_card_number : selectedTenant?.identity_card_number }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contract Details -->
      <div class="border rounded-md overflow-hidden">
        <div class="bg-gray-100 px-4 py-2 font-medium">{{ $t('contract.details') }}</div>
        <div class="p-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <p><span class="font-medium">{{ $t('contract.price') }}:</span> {{ formatCurrency(formData.price) }}</p>
              <p>
                <span class="font-medium">{{ $t('contract.pay_period') }}:</span>
                {{ formData.pay_period }} {{ $t('contract.months') }}
              </p>
              <p>
                <span class="font-medium">{{ $t('contract.number_of_tenant_current') }}:</span>
                {{ formData.number_of_tenant_current }}
              </p>

              <div class="mt-2">
                <p><span class="font-medium">{{ $t('contract.start_date') }}:</span> {{ formatDate(formData.start_date)
                  }}</p>
                <p><span class="font-medium">{{ $t('contract.end_date') }}:</span> {{ formatDate(formData.end_date) }}
                </p>
              </div>
            </div>

            <div>
              <!-- Electricity details -->
              <div class="mb-3">
                <p class="font-medium text-gray-700">{{ $t('contract.electricity_details') }}</p>
                <p>
                  <span class="font-medium">{{ $t('contract.payment_type') }}:</span>
                  {{ getPaymentTypeName(formData.electricity_pay_type) }}
                </p>
                <p>
                  <span class="font-medium">{{ $t('contract.price') }}:</span>
                  {{ formatCurrency(formData.electricity_price) }}
                </p>
                <p>
                  <span class="font-medium">{{ $t('contract.starting_reading') }}:</span>
                  {{ formData.electricity_number_start }}
                </p>
              </div>

              <!-- Water details -->
              <div>
                <p class="font-medium text-gray-700">{{ $t('contract.water_details') }}</p>
                <p>
                  <span class="font-medium">{{ $t('contract.payment_type') }}:</span>
                  {{ getPaymentTypeName(formData.water_pay_type) }}
                </p>
                <p>
                  <span class="font-medium">{{ $t('contract.price') }}:</span>
                  {{ formatCurrency(formData.water_price) }}
                </p>
                <p>
                  <span class="font-medium">{{ $t('contract.starting_reading') }}:</span>
                  {{ formData.water_number_start }}
                </p>
              </div>
            </div>
          </div>

          <!-- Note -->
          <div class="mt-4" v-if="formData.note">
            <p class="font-medium">{{ $t('contract.note') }}:</p>
            <p class="text-gray-700">{{ formData.note }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue';
import axios from 'axios';

export default {
  name: 'StepReview',
  props: {
    formData: {
      type: Object,
      required: true
    }
  },

  setup(props) {
    // Look up selected tenant data if using existing tenant
    const selectedTenant = computed(() => {
      if (props.formData.is_create_tenant) return null;

      if (props.formData.selectedTenantInfo) {
        return props.formData.selectedTenantInfo;
      }

      return {
        name: props.formData.name || 'N/A',
        tel: props.formData.tel || 'N/A',
        email: props.formData.email || 'N/A',
        identity_card_number: props.formData.identity_card_number || 'N/A'
      };
    });

    // Format currency
    const formatCurrency = (value) => {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(value);
    };

    // Format date
    const formatDate = (dateString) => {
      if (!dateString) return '';

      const date = new Date(dateString);
      return new Intl.DateTimeFormat('vi-VN').format(date);
    };

    // Get payment type name
    const getPaymentTypeName = (type) => {
      switch (parseInt(type)) {
        case 1: return 'Theo người';
        case 2: return 'Giá cố định';
        case 3: return 'Theo usage';
        default: return '';
      }
    };

    return {
      selectedTenant,
      formatCurrency,
      formatDate,
      getPaymentTypeName
    };
  }
};
</script>