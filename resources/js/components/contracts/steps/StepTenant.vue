<!-- resources/js/components/contracts/steps/StepTenant.vue -->
<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">{{ $t('contract.tenant_information') }}</h2>

    <div class="mb-4">
      <div class="flex items-center space-x-4 mb-6">
        <label class="inline-flex items-center">
          <input type="radio" v-model="formData.is_create_tenant" :value="true"
            class="form-radio h-4 w-4 text-blue-600">
          <span class="ml-2">{{ $t('tenant.create_new') }}</span>
        </label>
        <label class="inline-flex items-center">
          <input type="radio" v-model="formData.is_create_tenant" :value="false"
            class="form-radio h-4 w-4 text-blue-600">
          <span class="ml-2">{{ $t('tenant.select_existing') }}</span>
        </label>
      </div>

      <!-- Create new tenant form -->
      <div v-if="formData.is_create_tenant">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('tenant.name') }} *</label>
            <input type="text" v-model="formData.name" @blur="validateField('name')"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
              :class="{ 'border-red-500': errors.name }" :placeholder="$t('tenant.enter_name')">
            <span v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</span>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('tenant.tel') }} *</label>
            <input type="text" v-model="formData.tel" @blur="validateField('tel')"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
              :class="{ 'border-red-500': errors.tel }" :placeholder="$t('tenant.enter_tel')">
            <span v-if="errors.tel" class="text-red-500 text-xs mt-1">{{ errors.tel }}</span>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('tenant.email') }} *</label>
            <input type="email" v-model="formData.email" @blur="validateField('email')"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
              :class="{ 'border-red-500': errors.email }" :placeholder="$t('tenant.enter_email')">
            <span v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</span>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('tenant.identity_card_number') }}
              *</label>
            <input type="text" v-model="formData.identity_card_number" @blur="validateField('identity_card_number')"
              class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
              :class="{ 'border-red-500': errors.identity_card_number }"
              :placeholder="$t('tenant.enter_identity_card')">
            <span v-if="errors.identity_card_number" class="text-red-500 text-xs mt-1">{{ errors.identity_card_number
              }}</span>
          </div>
        </div>
      </div>

      <!-- Select existing tenant -->
      <div v-else>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('tenant.search') }}</label>
          <div class="flex">
            <input type="text" v-model="searchQuery" @input="searchTenants"
              class="flex-1 p-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-300"
              :placeholder="$t('tenant.search_placeholder')">
            <button @click="searchTenants"
              class="bg-blue-500 text-white p-2 rounded-r-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>
        </div>

        <div v-if="loading" class="flex justify-center py-6">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        </div>

        <div v-else-if="tenants.length === 0" class="p-4 bg-yellow-50 text-yellow-700 rounded-md">
          {{ $t('tenant.no_tenants_found') }}
        </div>

        <div v-else>
          <!-- Tenant Table with Pagination -->
          <div class="overflow-x-auto rounded-md border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('tenant.select') }}
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('tenant.name') }}
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('tenant.tel') }}
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('tenant.email') }}
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('tenant.identity_card_number') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="tenant in tenants" :key="tenant.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input type="radio" :checked="formData.tenant_id === tenant.id" @change="selectTenant(tenant)"
                      class="form-radio h-4 w-4 text-blue-600" />
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ tenant.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">{{ tenant.tel }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">{{ tenant.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">{{ tenant.identity_card_number }}</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="flex items-center justify-between mt-4">
            <div>
              <p class="text-sm text-gray-700">
                {{ $t('pagination.showing') }}
                <span class="font-medium">{{ paginationStart }}</span>
                {{ $t('pagination.to') }}
                <span class="font-medium">{{ paginationEnd }}</span>
                {{ $t('pagination.of') }}
                <span class="font-medium">{{ totalTenants }}</span>
                {{ $t('pagination.results') }}
              </p>
            </div>

            <div class="flex space-x-2">
              <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
                {{ $t('pagination.previous') }}
              </button>
              <button @click="changePage(currentPage + 1)" :disabled="currentPage >= lastPage"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage >= lastPage }">
                {{ $t('pagination.next') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Selected tenant summary (when existing tenant is selected) -->
    <div v-if="!formData.is_create_tenant && selectedTenant" class="mt-6 p-4 bg-blue-50 rounded-md">
      <h3 class="font-medium text-blue-800">{{ $t('tenant.selected_tenant') }}</h3>
      <p><span class="font-medium">{{ $t('tenant.name') }}:</span> {{ selectedTenant.name }}</p>
      <p><span class="font-medium">{{ $t('tenant.tel') }}:</span> {{ selectedTenant.tel }}</p>
      <p><span class="font-medium">{{ $t('tenant.email') }}:</span> {{ selectedTenant.email }}</p>
      <p><span class="font-medium">{{ $t('tenant.identity_card_number') }}:</span> {{
        selectedTenant.identity_card_number }}</p>
    </div>
  </div>
</template>

<script>
import { ref, watch, computed, reactive } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';
import { USE_MOCK_DATA } from '../../../config';
import mockApi from '../../../services/mockApi';

export default {
  name: 'StepTenant',
  props: {
    formData: {
      type: Object,
      required: true
    }
  },
  emits: ['update', 'validate'],

  setup(props, { emit }) {
    const tenants = ref([]);
    const loading = ref(false);
    const searchQuery = ref('');
    const errors = reactive({});

    // Pagination
    const currentPage = ref(1);
    const perPage = ref(5);
    const totalTenants = ref(0);
    const lastPage = ref(1);

    const paginationStart = computed(() => {
      return (currentPage.value - 1) * perPage.value + 1;
    });

    const paginationEnd = computed(() => {
      return Math.min(currentPage.value * perPage.value, totalTenants.value);
    });

    const selectedTenant = computed(() => {
      if (!props.formData.tenant_id) return null;
      return tenants.value.find(t => t.id === props.formData.tenant_id);
    });

    // Change page
    const changePage = (page) => {
      if (page < 1 || page > lastPage.value) return;
      currentPage.value = page;
      fetchTenants();
    };

    // Fetch tenants from API or mock data
    const fetchTenants = async () => {
      try {
        loading.value = true;

        let response;
        if (USE_MOCK_DATA) {
          response = await mockApi.getTenantsByUser({
            page: currentPage.value,
            per_page: perPage.value,
            search: searchQuery.value
          });
        } else {
          // Use API for landlord's tenants with pagination
          response = await axios.get('/api/v1/tenants/user', {
            params: {
              page: currentPage.value,
              per_page: perPage.value,
              search: searchQuery.value
            }
          });
        }

        tenants.value = response.data.data;
        totalTenants.value = response.data.meta.total || tenants.value.length;
        lastPage.value = response.data.meta.last_page || 1;
      } catch (error) {
        console.error('Error fetching tenants:', error);
      } finally {
        loading.value = false;
      }
    };

    // Search tenants debounced
    const searchTenants = debounce(() => {
      currentPage.value = 1; // Reset to first page when searching
      fetchTenants();
    }, 500);

    // Select a tenant
    const selectTenant = (tenant) => {
      emit('update', 'tenant_id', tenant.id);
      console.log('Đã chọn người thuê:', tenant.name, 'với ID:', tenant.id);
      emit('update', 'selectedTenantInfo', tenant);
    };

    // Validate fields
    const validateField = (field) => {
      errors[field] = null;

      switch (field) {
        case 'name':
          if (!props.formData.name) {
            errors[field] = $t('validation.required', { attribute: $t('tenant.name').toLowerCase() });
          }
          break;
        case 'tel':
          if (!props.formData.tel) {
            errors[field] = $t('validation.required', { attribute: $t('tenant.tel').toLowerCase() });
          } else if (!/^(0|\+84)([0-9]{9}|[0-9]{10})$/.test(props.formData.tel)) {
            errors[field] = $t('validation.tel.invalid_format');
          }
          break;
        case 'email':
          if (!props.formData.email) {
            errors[field] = $t('validation.required', { attribute: $t('tenant.email').toLowerCase() });
          } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(props.formData.email)) {
            errors[field] = $t('validation.email', { attribute: $t('tenant.email').toLowerCase() });
          }
          break;
        case 'identity_card_number':
          if (!props.formData.identity_card_number) {
            errors[field] = $t('validation.required', { attribute: $t('tenant.identity_card_number').toLowerCase() });
          } else if (!/^[0-9]{9,12}$/.test(props.formData.identity_card_number)) {
            errors[field] = $t('validation.id_card.invalid_format');
          }
          break;
      }

      return !errors[field];
    };

    // Validate all fields
    const validateAllFields = () => {
      if (props.formData.is_create_tenant) {
        const isValid =
          validateField('name') &&
          validateField('tel') &&
          validateField('email') &&
          validateField('identity_card_number');

        return isValid;
      }

      return !!props.formData.tenant_id;
    };

    // Watch for tenant selection type change
    watch(() => props.formData.is_create_tenant, (newValue) => {
      if (newValue) {
        // Reset tenant_id when switching to create new tenant
        emit('update', 'tenant_id', null);
      } else {
        // Reset tenant form fields when switching to select existing tenant
        emit('update', 'name', '');
        emit('update', 'tel', '');
        emit('update', 'email', '');
        emit('update', 'identity_card_number', '');

        // Load tenant list
        fetchTenants();
      }
    });

    // Initialize
    if (!props.formData.is_create_tenant) {
      fetchTenants();
    }

    return {
      tenants,
      loading,
      searchQuery,
      selectedTenant,
      searchTenants,
      selectTenant,
      errors,
      validateField,
      validateAllFields,
      // Pagination
      currentPage,
      perPage,
      totalTenants,
      lastPage,
      paginationStart,
      paginationEnd,
      changePage,
      fetchTenants
    };
  }
};
</script>