<template>
  <div class="w-full relative flex justify-center items-center min-h-screen">
    <div class="w-full sm:w-3/4 md:w-2/3 lg:w-1/2 xl:w-1/2">
      <div class="bg-white rounded-lg shadow-md p-4">
        <h2 class="text-3xl font-semibold mb-4">Edit Collection</h2>

        <div v-if="responseMessage || errorMessage" class="fixed inset-0 flex justify-center items-center z-50">
          <div class="bg-white rounded-lg shadow-md p-4 text-center">
            <div class="flex items-center justify-center space-x-2 mb-2">
              <img v-if="responseMessage" src="@/assets/images/right.png" alt="Right Icon" class="w-6 h-6 text-green-500" />
              <img v-if="errorMessage" src="@/assets/images/wrong.png" alt="Wrong Icon" class="w-6 h-6 text-red-500" />
              <p v-show="responseMessage || errorMessage" :class="responseMessage ? 'text-green-500 font-bold' : 'text-red-500 font-bold'">{{ responseMessage || errorMessage }}</p>
            </div>
          </div>
        </div>

        <form @submit.prevent="updateCollection" class="space-y-4">
          <div class="flex flex-col space-y-2">
            <label for="editName" class="block text-sm font-medium text-gray-700">Name:</label>
            <input v-model="editedCollection.name" type="text" id="editName" required class="p-2 border rounded-md focus:ring focus:ring-indigo-300">
          </div>
          <div class="flex flex-col space-y-2">
            <label for="editEmail" class="block text-sm font-medium text-gray-700">Email:</label>
            <input v-model="editedCollection.owner_email" type="email" id="editEmail" required class="p-2 border rounded-md focus:ring focus:ring-indigo-300">
          </div>
          <div class="flex justify-end space-x-2">
            <button type="button" @click="cancelEdit" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring focus:ring-gray-300">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 focus:outline-none focus:ring focus:ring-indigo-300">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/plugins/api';
import Header from '@/components/Header.vue';

export default {
  components: {
    Header,
  },

  setup() {
    const editedCollection = ref({
      id: null,
      name: '',
      owner_email: '',
    });

    const originalCollection = ref(null);

    const router = useRouter();

    const errorMessage = ref('');
    const responseMessage = ref('');

    const updateCollection = async () => {
      try {
        const { id, name, owner_email } = editedCollection.value;

        console.log('Update Payload:', {
          id,
          name,
          owner_email,
        });

        const response = await api.updateCollection(id, {
          name: name,
          owner_email: owner_email,
        });


        console.log('Update Response:', response);

        if (response.message) {
          responseMessage.value = response.message;

          setTimeout(() => {
            responseMessage.value = '';
            router.push('/collection-list');
          }, 1000);
        }
      } catch (error) {
        console.error('Error updating collection:', error);

        if (error.response && error.response.data) {
         errorMessage.value = error.response.data.message;
          console.log('Error Message:', errorMessage.value);

          setTimeout(() => {
            errorMessage.value = '';
          }, 1000);
        } else {
          errorMessage.value = 'An error occurred while updating the collection.';
        }
      }
    };


    const cancelEdit = () => {
      router.push('/collection-list');
    };

    onMounted(async () => {
      try {
        const collectionId = router.currentRoute.value.params.id;
        const response = await api.getCollectionById(collectionId);
        if (response) {
          const { id, name, owner_email } = response.data;
          editedCollection.value = {
            id,
            name,
            owner_email,
          };
          console.log('Fetched collection data:', response);
          console.log('Edited collection:', editedCollection.value);
          originalCollection.value = { ...response };
        }
      } catch (error) {
        console.error('Error fetching collection data:', error);
      }
    });

    return {
      editedCollection,
      updateCollection,
      errorMessage,
      responseMessage,
      cancelEdit,
    };
  },
};
</script>
