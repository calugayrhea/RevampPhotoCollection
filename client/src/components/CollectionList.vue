<template>
  <div class="w-full">
    <Header />
    <div class="flex flex-col items-center mt-10">
      <div class="bg-white rounded-lg shadow-lg p-4 w-full lg:w-3/4 xl:w-3/4">
        <div class="relative">
          <img src="@/assets/images/search.png" alt="Search Icon"
            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 pointer-events-none" />
          <input v-model="filter" placeholder="Filter collections..." class="pl-10 p-3 border rounded-md mb-4 w-full" />
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-100">
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Owner</th>
                <th class="p-3 text-left">Created</th>
                <th class="p-3 text-left">Actions</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="collection in displayedCollections" :key="collection.id"
                class="border-b border-gray-300 hover:bg-gray-50 transition">
                <td class="p-3">
                  <div class="flex items-center">
                    <img src="@/assets/images/folder.png" alt="Folder Icon" class="w-6 h-6 mr-2" />
                    {{ collection.name.charAt(0).toUpperCase() + collection.name.slice(1) }}
                  </div>
                </td>
                <td class="p-3">{{ collection.owner_email }}</td>
                <td class="p-3">{{ formatCreationDate(collection.created_at) }}</td>
                <td class="p-3 flex items-center space-x-2">
                  <img src="@/assets/images/edit.png" alt="Edit Icon" class="w-6 h-6 cursor-pointer"
                    @click="editCollection(collection.id)" />
                  <img src="@/assets/images/delete.png" alt="Delete Icon" class="w-6 h-6 cursor-pointer"
                    @click="showDeleteConfirmationModal(collection.id)" />
                  <img src="@/assets/images/upload.png" alt="Upload Icon" class="w-6 h-6 cursor-pointer"
                    @click="uploadFile(collection.id)" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold mb-4">Confirm Deletion</h3>
            <p class="mb-4">Are you sure you want to delete this collection?</p>
            <div class="flex justify-end">
              <button @click="cancelDeleteCollection"
                class="px-4 py-2 bg-gray-300 text-gray-600 rounded-md mr-2">Cancel</button>
              <button @click="confirmDeleteCollection" class="px-4 py-2 bg-red-500 text-white rounded-md">Confirm</button>
            </div>
          </div>
        </div>

        <div v-if="showSuccessModal" class="fixed bottom-0 right-0 m-4 shadow-md">
          <div class="bg-white rounded-lg p-4">
            <div class="flex items-center justify-center mb-2">
              <img src="@/assets/images/right.png" alt="Success Icon" class="w-8 h-8 mr-2" />
              <p class="text-green-500 font-bold">{{ successMessage }}</p>
            </div>
          </div>
        </div>

        <div class="mt-4 flex flex-col lg:flex-row justify-between items-center border-t border-gray-300 pt-4">
          <div class="text-gray-600 mb-2 lg:mb-0">
            Showing {{ startResult }} to {{ endResult }} of {{ totalResults }} results
          </div>
          <div class="flex items-center space-x-2">
            <label for="pageSize" class="mr-2">Items per page:</label>
            <select v-model="pageSize" id="pageSize" class="p-2 border rounded-md">
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="200">200</option>
              <option value="250">250</option>
              <option value="300">300</option>
            </select>

            <div class="flex mt-2 lg:mt-0">
              <button v-for="page in pageCount" :key="page" @click="goToPage(page)"
                class="px-4 py-2 bg-indigo-500 text-white rounded-md cursor-pointer border border-gray-400 border-solid border-2"
                :class="{ 'bg-gray-300 text-gray-600': currentPage === page }">
                {{ page }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/plugins/api';
import Header from '@/components/Header.vue';
import { formatDistanceToNow } from 'date-fns';

export default {
  components: {
    Header,
  },

  setup() {
    const collections = ref([]);
    const filter = ref('');
    const pageSize = ref(50);
    const currentPage = ref(1);
    const showSuccessModal = ref(false);
    const successMessage = ref('');
    const showDeleteModal = ref(false);
    const collectionToDeleteId = ref(null);

    const router = useRouter();

    const filteredCollections = computed(() => {
      const filterText = filter.value.toLowerCase();
      return collections.value.filter((collection) => {
        const name = collection.name.toLowerCase();
        const ownerEmail = collection.owner_email.toLowerCase();
        return name.includes(filterText) || ownerEmail.includes(filterText);
      });
    });

    const displayedCollections = computed(() => {
      const startIndex = (currentPage.value - 1) * pageSize.value;
      const endIndex = startIndex + pageSize.value;
      return filteredCollections.value.slice(startIndex, endIndex);
    });

    const pageCount = computed(() => {
      return Math.ceil(filteredCollections.value.length / pageSize.value);
    });

    const startResult = computed(() => {
      if (filteredCollections.value.length === 0) return 0;
      return (currentPage.value - 1) * pageSize.value + 1;
    });

    const endResult = computed(() => {
      const end = currentPage.value * pageSize.value;
      return end > filteredCollections.value.length
        ? filteredCollections.value.length
        : end;
    });

    const totalResults = computed(() => {
      return filteredCollections.value.length;
    });

    const formatCreationDate = (date) => {
      const createdDate = new Date(date);
      const currentDate = new Date();
      const timeAgo = formatDistanceToNow(createdDate, { addSuffix: true });
      return timeAgo;
    };

    const showDeleteConfirmationModal = (collectionId) => {
      collectionToDeleteId.value = collectionId;
      showDeleteModal.value = true;
    };

    const confirmDeleteCollection = () => {
      if (collectionToDeleteId.value) {
        deleteCollection(collectionToDeleteId.value);
        showDeleteModal.value = false;
      }
    };

    const cancelDeleteCollection = () => {
      showDeleteModal.value = false;
    };

    const deleteCollection = async (collectionId) => {
      try {
        await api.deleteCollection(collectionId);
        const indexToDelete = collections.value.findIndex((collection) => collection.id === collectionId);
        if (indexToDelete !== -1) {
          collections.value.splice(indexToDelete, 1);
        }

        successMessage.value = 'Collection deleted successfully.';
        showSuccessModal.value = true;

        setTimeout(() => {
          showSuccessModal.value = false;
        }, 1000);

        console.log('Collection deleted successfully');
      } catch (error) {
        console.error('Error deleting collection:', error);
      }
    };

    const goToPage = (page) => {
      if (page >= 1 && page <= pageCount.value) {
        currentPage.value = page;
      }
    };

    const editCollection = (collectionId) => {
      router.push({ name: 'edit-collection', params: { id: collectionId } });
    };

    const uploadFile = (collectionId) => {
      router.push({ name: 'photo-upload', params: { collectionId } });
    };

    const fetchCollections = async () => {
      try {
        const collectionsData = await api.getAllCollections();
        collections.value = collectionsData.data;
      } catch (error) {
        console.error('Error fetching collections:', error);
      }
    };


    fetchCollections();

    return {
      collections,
      filter,
      pageSize,
      currentPage,
      showSuccessModal,
      successMessage,
      showDeleteModal,
      collectionToDeleteId,
      filteredCollections,
      displayedCollections,
      pageCount,
      startResult,
      endResult,
      totalResults,
      formatCreationDate,
      showDeleteConfirmationModal,
      confirmDeleteCollection,
      cancelDeleteCollection,
      deleteCollection,
      goToPage,
      editCollection,
      uploadFile,
    };
  },
};
</script>