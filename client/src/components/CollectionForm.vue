<template>
  <div class="flex items-center justify-center h-screen">
    <div class="w-full sm:w-3/4 md:w-2/3 lg:w-1/2 xl:w-1/2">
      <div class="bg-white rounded-lg shadow-md p-4 font-roboto">
        <h1 class="font-montserrat text-3xl font-bold mb-4">Create a Collection</h1>
        <form @submit.prevent="createCollection" class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
            <input type="text" id="name" v-model="collection.name" required
              class="mt-1 p-2 border rounded-md w-full focus:ring focus:ring-indigo-300">
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
            <input type="email" id="email" v-model="collection.email" required
              class="mt-1 p-2 border rounded-md w-full focus:ring focus:ring-indigo-300">
          </div>
          <div v-if="collection.created_at">
            <label class="block text-sm font-medium text-gray-700">Created At:</label>
            <span>{{ collection.created_at }}</span>
          </div>
          <div>
            <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-gray-300 opacity-75">
              <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-12 w-12"></div>
            </div>
            <button type="submit"
              class="px-4 py-2 bg-indigo-500 text-white rounded-md hover-bg-indigo-600 focus:outline-none focus:ring focus:ring-indigo-300">
              Create Collection
            </button>
          </div>
        </form>
      </div>
      <div v-if="notification.show" class="fixed inset-0 flex justify-center items-center z-50">
        <div class="bg-white p-2 rounded-md shadow-md w-64">
          <div class="flex items-center space-x-2">
            <img v-if="notification.type === 'success'" src="@/assets/images/right.png" alt="Success Icon"
              class="w-8 h-8" />
            <img v-else src="@/assets/images/wrong.png" alt="Error Icon" class="w-8 h-8 text-red-500" />
            <span class="text-base flex-grow font-bold"
              :class="notification.type === 'success' ? 'text-green-500' : 'text-red-500'">
              {{ notification.message }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/plugins/api';

export default {
  setup() {
    const collection = ref({
      name: '',
      email: '',
      created_at: null,
    });

    const notification = ref({ show: false, message: '', type: '' });
    const loading = ref(false);

    const router = useRouter();

    const createCollection = async () => {
      try {
        loading.value = true;

        const collectionData = {
          name: collection.value.name,
          owner_email: collection.value.email,
        };

        const response = await api.createCollection(collectionData);

        if (response) {
          collection.value.created_at = response.created_at;
          collection.value.name = '';
          collection.value.email = '';

          setTimeout(() => {
            loading.value = false;
            notification.value = {
              message: 'Collection created successfully',
              type: 'success',
              show: true,
            };

            setTimeout(() => {
              router.push({ name: 'collection-list' });
            }, 1000);
          }, 2000);
        }
      } catch (error) {
        console.log('Error response:', error);
        console.error(error);

        if (error.response && error.response.data) {
          loading.value = false;
          notification.value = {
            message: error.response.data.error,
            type: 'error',
            show: true,
          };
        } else {
          loading.value = false;
          notification.value = {
            message: 'An error occurred while creating the collection.',
            type: 'error',
            show: true,
          };
        }
      }
    };

    return {
      collection,
      notification,
      loading,
      createCollection,
    };
  },
};
</script>
