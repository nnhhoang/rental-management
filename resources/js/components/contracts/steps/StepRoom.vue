<!-- resources/js/components/contracts/steps/StepRoom.vue -->
<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">{{ $t('contract.select_room') }}</h2>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('apartment.select_apartment') }}</label>
      <select v-model="selectedApartmentId"
        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300"
        @change="loadRooms">
        <option value="">{{ $t('common.select_option') }}</option>
        <option v-for="apartment in apartments" :key="apartment.id" :value="apartment.id">
          {{ apartment.name }}
        </option>
      </select>
    </div>

    <div class="mb-4" v-if="selectedApartmentId">
      <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('room.select_room') }}</label>

      <div v-if="loadingRooms" class="flex items-center justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
      </div>

      <div v-else-if="availableRooms.length === 0" class="p-4 bg-yellow-50 text-yellow-700 rounded-md">
        {{ $t('room.no_available_rooms') }}
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div v-for="room in availableRooms" :key="room.id" @click="selectRoom(room)"
          class="border p-4 rounded-md cursor-pointer transition-all"
          :class="formData.apartment_room_id === room.id ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-blue-300'">
          <h3 class="font-medium">{{ $t('room.room_number') }}: {{ room.room_number }}</h3>
          <p class="text-gray-600">{{ $t('room.default_price') }}: {{ formatCurrency(room.default_price) }}</p>
          <p class="text-gray-600">{{ $t('room.max_tenant') }}: {{ room.max_tenant }}</p>
        </div>
      </div>
    </div>

    <div v-if="formData.selectedRoom" class="mt-6 p-4 bg-blue-50 rounded-md">
      <h3 class="font-medium text-blue-800">{{ $t('room.selected_room') }}</h3>
      <p><span class="font-medium">{{ $t('apartment.name') }}:</span> {{ formData.selectedRoom.apartment.name }}</p>
      <p><span class="font-medium">{{ $t('room.room_number') }}:</span> {{ formData.selectedRoom.room_number }}</p>
      <p><span class="font-medium">{{ $t('room.default_price') }}:</span> {{
        formatCurrency(formData.selectedRoom.default_price) }}</p>
    </div>
  </div>
</template>

<script>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { USE_MOCK_DATA } from '../../../config';
import mockApi from '../../../services/mockApi';

export default {
  name: 'StepRoom',
  props: {
    formData: {
      type: Object,
      required: true
    }
  },
  emits: ['update', 'validate'],

  setup(props, { emit }) {
    const apartments = ref([]);
    const availableRooms = ref([]);
    const selectedApartmentId = ref('');
    const loadingApartments = ref(false);
    const loadingRooms = ref(false);

    // Load user's apartments
    const loadApartments = async () => {
      try {
        loadingApartments.value = true;

        let response;
        if (USE_MOCK_DATA) {
          response = await mockApi.getUserApartments();
        } else {
          response = await axios.get('/api/v1/apartments/user');
        }

        apartments.value = response.data.data;

        // If formData already has a selected room, set the selectedApartmentId
        if (props.formData.selectedRoom) {
          selectedApartmentId.value = props.formData.selectedRoom.apartment.id;
          await loadRooms();
        }
      } catch (error) {
        console.error('Error loading apartments:', error);
      } finally {
        loadingApartments.value = false;
      }
    };

    // Load rooms for selected apartment
    const loadRooms = async () => {
      if (!selectedApartmentId.value) {
        availableRooms.value = [];
        return;
      }

      try {
        loadingRooms.value = true;

        let response;
        if (USE_MOCK_DATA) {
          // Call mock API
          response = await mockApi.getRoomsWithoutTenant({
            apartment_id: selectedApartmentId.value
          });
        } else {
          // Call real API
          response = await axios.get(`/api/v1/rooms/without-tenant`, {
            params: {
              apartment_id: selectedApartmentId.value
            }
          });
        }

        availableRooms.value = response.data.data;
      } catch (error) {
        console.error('Error loading rooms:', error);
      } finally {
        loadingRooms.value = false;
      }
    };

    // Select a room
    const selectRoom = (room) => {
      emit('update', 'apartment_room_id', room.id);
      emit('update', 'selectedRoom', room);

      // Initialize price with room's default price
      if (room.default_price && (!props.formData.price || props.formData.price === 0)) {
        emit('update', 'price', room.default_price);
      }
    };

    // Format currency
    const formatCurrency = (value) => {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(value);
    };

    // Watch for apartment change
    watch(selectedApartmentId, () => {
      // Reset room selection when apartment changes
      emit('update', 'apartment_room_id', null);
      emit('update', 'selectedRoom', null);
      loadRooms();
    });

    onMounted(() => {
      loadApartments();
    });

    return {
      apartments,
      availableRooms,
      selectedApartmentId,
      loadingApartments,
      loadingRooms,
      loadRooms,
      selectRoom,
      formatCurrency
    };
  }
};
</script>