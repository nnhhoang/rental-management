const apartments = [
  { 
    id: 1, 
    name: 'Blue Sky Apartment', 
    address: '123 Main St',
    province_id: '01',
    district_id: '001',
    ward_id: '00001',
    image: null,
    user_id: 1
  },
  { 
    id: 2, 
    name: 'Green Valley Residence', 
    address: '456 Oak Ave',
    province_id: '01',
    district_id: '002',
    ward_id: '00002',
    image: null,
    user_id: 1
  },
  { 
    id: 3, 
    name: 'City View Tower', 
    address: '789 Pine Rd',
    province_id: '02',
    district_id: '003',
    ward_id: '00003',
    image: null,
    user_id: 1
  },
];

const rooms = [
  { 
    id: 1, 
    apartment_id: 1, 
    room_number: 'A101', 
    default_price: 2500000, 
    max_tenant: 2,
    image: null,
    apartment: apartments[0]
  },
  { 
    id: 2, 
    apartment_id: 1, 
    room_number: 'A102', 
    default_price: 3000000, 
    max_tenant: 3,
    image: null,
    apartment: apartments[0]
  },
  { 
    id: 3, 
    apartment_id: 2, 
    room_number: 'B201', 
    default_price: 2000000, 
    max_tenant: 1,
    image: null,
    apartment: apartments[1]
  },
  { 
    id: 4, 
    apartment_id: 2, 
    room_number: 'B202', 
    default_price: 2200000, 
    max_tenant: 2,
    image: null,
    apartment: apartments[1]
  },
  { 
    id: 5, 
    apartment_id: 3, 
    room_number: 'C301', 
    default_price: 3500000, 
    max_tenant: 2,
    image: null,
    apartment: apartments[2]
  },
];

const tenants = [
  { 
    id: 1, 
    name: 'Nguyễn Văn A', 
    tel: '0123456789', 
    email: 'nguyenvana@example.com', 
    identity_card_number: '123456789012' 
  },
  { 
    id: 2, 
    name: 'Trần Thị B', 
    tel: '0987654321', 
    email: 'tranthib@example.com', 
    identity_card_number: '987654321098' 
  },
  { 
    id: 3, 
    name: 'Lê Văn C', 
    tel: '0369852147', 
    email: 'levanc@example.com', 
    identity_card_number: '456789123456' 
  },
  { 
    id: 4, 
    name: 'Phạm Thị D', 
    tel: '0741852963', 
    email: 'phamthid@example.com', 
    identity_card_number: '321654987321' 
  },
];

// Add delay to simulate network latency
const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

// Mock API methods
export default {
  // Apartments
  getUserApartments: async () => {
    await delay(300);
    return { 
      data: { 
        data: apartments 
      } 
    };
  },
  
  // Rooms
  getRoomsWithoutTenant: async (params) => {
    await delay(500);
    let filteredRooms = [...rooms];
    
    if (params && params.apartment_id) {
      const apartmentId = parseInt(params.apartment_id);
      filteredRooms = filteredRooms.filter(room => room.apartment_id === apartmentId);
    }
    
    return { 
      data: { 
        data: filteredRooms 
      } 
    };
  },
  
  // Contracts
  createContract: async (contractData) => {
    await delay(800);
    console.log('Contract data submitted:', contractData);
    
    // Simulate a successful creation
    return { 
      data: { 
        data: { 
          id: 999, 
          ...contractData,
          created_at: new Date().toISOString()
        },
        message: 'Contract created successfully'
      } 
    };
  },
  
  // Tenants with pagination
  getTenantsByUser: async (params = {}) => {
    await delay(400);
    let filteredTenants = [...tenants];
    
    // Filter by search term if provided
    if (params.search) {
      const searchTerm = params.search.toLowerCase();
      filteredTenants = filteredTenants.filter(tenant => 
        tenant.name.toLowerCase().includes(searchTerm) ||
        tenant.tel.includes(searchTerm) ||
        tenant.email.toLowerCase().includes(searchTerm)
      );
    }
    
    // Calculate pagination
    const page = params.page || 1;
    const perPage = params.per_page || 5;
    const total = filteredTenants.length;
    const lastPage = Math.ceil(total / perPage);
    const from = (page - 1) * perPage + 1;
    const to = Math.min(page * perPage, total);
    
    // Get paginated results
    const paginatedTenants = filteredTenants.slice((page - 1) * perPage, page * perPage);
    
    return { 
      data: { 
        data: paginatedTenants,
        meta: {
          current_page: page,
          from: from,
          last_page: lastPage,
          path: '/api/v1/tenants/user',
          per_page: perPage,
          to: to,
          total: total
        }
      } 
    };
  },
};