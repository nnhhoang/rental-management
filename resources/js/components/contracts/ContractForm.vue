<template>
  <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $t('contract.create_contract') }}</h1>

    <!-- Progress bar -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <template v-for="(step, index) in steps" :key="index">
          <div class="flex flex-col items-center">
            <div :class="[
              'w-10 h-10 rounded-full flex items-center justify-center text-white font-medium',
              currentStep > index
                ? 'bg-green-500'
                : currentStep === index
                  ? 'bg-blue-500'
                  : 'bg-gray-300'
            ]">
              <span v-if="currentStep > index">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </span>
              <span v-else>{{ index + 1 }}</span>
            </div>
            <span class="text-sm mt-2 font-medium" :class="currentStep === index ? 'text-blue-500' : 'text-gray-500'">
              {{ step.title }}
            </span>
          </div>

          <!-- Connection line between circles -->
          <div v-if="index < steps.length - 1" class="flex-1 h-1 mx-2"
            :class="currentStep > index ? 'bg-green-500' : 'bg-gray-300'"></div>
        </template>
      </div>
    </div>

    <!-- Form steps -->
    <div class="mb-8">
      <component :is="steps[currentStep].component" :form-data="formData" @update="updateFormData"
        @validate="validateStep" />
    </div>

    <!-- Navigation buttons -->
    <div class="flex justify-between">
      <button v-if="currentStep > 0" @click="prevStep"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
        {{ $t('common.previous') }}
      </button>
      <div></div> <!-- Spacer -->
      <div class="flex space-x-3">
        <button v-if="currentStep < steps.length - 1" @click="nextStep"
          class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300"
          :disabled="loading">
          {{ $t('common.next') }}
        </button>
        <button v-else @click="submitForm"
          class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300"
          :disabled="loading">
          {{ $t('common.submit') }}
          <span v-if="loading" class="ml-2 inline-block animate-spin">⟳</span>
        </button>
      </div>
    </div>

    <!-- Error message -->
    <div v-if="errorMessage" class="mt-4 p-3 bg-red-100 text-red-700 rounded-md">
      {{ errorMessage }}
    </div>
  </div>
</template>


<script>
import { ref, reactive, computed, onMounted } from 'vue';
import StepRoom from './steps/StepRoom.vue';
import StepTenant from './steps/StepTenant.vue';
import StepContractDetails from './steps/StepContractDetails.vue';
import StepReview from './steps/StepReview.vue';
import axios from 'axios';
import { USE_MOCK_DATA } from '../../config';
import mockApi from '../../services/mockApi';

export default {
  name: 'ContractForm',
  components: {
    StepRoom,
    StepTenant,
    StepContractDetails,
    StepReview
  },
  setup() {
    const currentStep = ref(0);
    const loading = ref(false);
    const errorMessage = ref('');

    // Form data
    const formData = reactive({
      // Room selection
      apartment_room_id: null,
      selectedRoom: null,

      // Tenant information
      is_create_tenant: true,
      tenant_id: null,
      name: '',
      tel: '',
      email: '',
      identity_card_number: '',
      selectedTenantInfo: null,

      // Contract details
      pay_period: 3, // Default: 3 months
      price: 0,
      electricity_pay_type: 1, // Default: per person
      electricity_price: 0,
      electricity_number_start: 0,
      water_pay_type: 1, // Default: per person
      water_price: 0,
      water_number_start: 0,
      number_of_tenant_current: 1,
      note: '',

      // Dates
      start_date: new Date().toISOString().split('T')[0], // Current date in YYYY-MM-DD format
      end_date: ''
    });

    // Steps configuration
    const steps = [
      {
        title: 'Chọn phòng',
        component: StepRoom,
        validate: () => {
          if (!formData.apartment_room_id) {
            errorMessage.value = 'Vui lòng chọn phòng';
            return false;
          }
          return true;
        }
      },
      {
        title: 'Thông tin người thuê',
        component: StepTenant,
        validate: () => {
          errorMessage.value = '';

          if (formData.is_create_tenant) {
            if (!formData.name || !formData.tel || !formData.email || !formData.identity_card_number) {
              errorMessage.value = 'Vui lòng điền đầy đủ thông tin người thuê';
              return false;
            }
          } else {
            if (!formData.tenant_id) {
              errorMessage.value = 'Vui lòng chọn người thuê';
              return false;
            }
          }
          return true;
        }
      },
      {
        title: 'Chi tiết hợp đồng',
        component: StepContractDetails,
        validate: () => {
          if (!formData.price || !formData.electricity_price || !formData.water_price ||
            !formData.start_date || !formData.end_date) {
            errorMessage.value = 'Vui lòng điền đầy đủ thông tin hợp đồng';
            return false;
          }

          // Validate end_date is after start_date
          if (new Date(formData.end_date) <= new Date(formData.start_date)) {
            errorMessage.value = 'Ngày kết thúc phải sau ngày bắt đầu';
            return false;
          }

          return true;
        }
      },
      {
        title: 'Xác nhận',
        component: StepReview,
        validate: () => true
      }
    ];

    // Methods
    const nextStep = () => {
      errorMessage.value = '';

      // Validate current step
      if (steps[currentStep.value].validate()) {
        currentStep.value++;
      }
    };

    const prevStep = () => {
      errorMessage.value = '';
      currentStep.value--;
    };

    const updateFormData = (field, value) => {
      formData[field] = value;
    };

    const validateStep = (stepIndex) => {
      return steps[stepIndex]?.validate() || false;
    };

    const submitForm = async () => {
      try {
        loading.value = true;
        errorMessage.value = '';

        let response;
        if (USE_MOCK_DATA) {
          response = await mockApi.createContract(formData);
        } else {
          response = await axios.post('/api/v1/contracts', formData);
        }

        // Show success alert for testing purposes
        alert('Hợp đồng đã được tạo thành công!');
        console.log('Contract created successfully:', response.data.data);

        // Reset form after successful submission
        resetForm();

      } catch (error) {
        console.error('Error submitting contract:', error);

        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            // Format validation errors
            const errorMessages = Object.values(error.response.data.errors).flat();
            errorMessage.value = errorMessages.join(', ');
          } else {
            errorMessage.value = error.response.data.message || 'Đã xảy ra lỗi khi tạo hợp đồng';
          }
        } else {
          errorMessage.value = 'Đã xảy ra lỗi khi tạo hợp đồng';
        }
      } finally {
        loading.value = false;
      }
    };

    const resetForm = () => {
      // Reset form values
      Object.keys(formData).forEach(key => {
        if (key === 'is_create_tenant') {
          formData[key] = true;
        } else if (key === 'pay_period') {
          formData[key] = 3;
        } else if (key === 'electricity_pay_type' || key === 'water_pay_type') {
          formData[key] = 1;
        } else if (key === 'number_of_tenant_current') {
          formData[key] = 1;
        } else if (key === 'start_date') {
          formData[key] = new Date().toISOString().split('T')[0];
        } else if (key === 'end_date') {
          formData[key] = '';
        } else {
          formData[key] = null;
        }
      });

      // Reset to first step
      currentStep.value = 0;
    };

    return {
      currentStep,
      formData,
      steps,
      loading,
      errorMessage,
      nextStep,
      prevStep,
      updateFormData,
      validateStep,
      submitForm
    };
  }
};
</script>