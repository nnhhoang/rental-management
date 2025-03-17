// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import ContractForm from '../components/contracts/ContractForm.vue';

// Import các component khác của bạn

const routes = [
  // Các route hiện có của bạn
  
  // Thêm route mới cho form tạo hợp đồng
  {
    path: '/contracts/create',
    name: 'contracts.create',
    component: ContractForm,
    meta: {
      requiresAuth: true,
      title: 'Tạo hợp đồng mới'
    }
  },
  
  // Route để xem chi tiết hợp đồng sau khi tạo
  {
    path: '/contracts/:id',
    name: 'contracts.show',
    component: () => import('../components/contracts/ContractDetail.vue'),
    meta: {
      requiresAuth: true,
      title: 'Chi tiết hợp đồng'
    }
  },
  
  // Route cho danh sách hợp đồng
  {
    path: '/contracts',
    name: 'contracts.index',
    component: () => import('../components/contracts/ContractList.vue'),
    meta: {
      requiresAuth: true,
      title: 'Danh sách hợp đồng'
    }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Middleware xác thực
router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const isAuthenticated = localStorage.getItem('auth_token'); // Hoặc cách bạn xác thực người dùng

  // Cập nhật tiêu đề trang
  document.title = to.meta.title || 'Apartment Manager';

  if (requiresAuth && !isAuthenticated) {
    next({ name: 'login' });
  } else {
    next();
  }
});

export default router;