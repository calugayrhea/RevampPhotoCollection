<template>
  <div class="photo-upload-page">
    <Header />
    <div class="bg-gray-100 p-4 rounded-lg mb-4 shadow-md">
      <div class="bg-white rounded-lg p-4">
        <div class="flex space-x-2 text-gray-600">
          <router-link to="/" class="hover:text-indigo-600 transition">Home</router-link>
          <span>&gt;</span>
          <router-link :to="'/collection-list'" class="hover:text-indigo-600 transition">Collections</router-link>
          <span>&gt;</span>
          <span class="font-semibold">Upload Photos</span>
        </div>
        <h2 class="text-3xl font-semibold mb-4">{{ collectionName }} - Collections Photo</h2>

        <form @submit.prevent="uploadPhotos" class="space-y-4">
          <div class="mb-4">
            <label for="photos" class="block font-semibold mb-2">Select Photos:</label>
            <input type="file" id="photos" ref="photosInput" multiple accept="image/*"
              class="p-2 border rounded-md w-full" @change="onFileChange" required />
            <small class="text-gray-500">Select one or more image files (JPG, PNG, etc.).</small>
          </div>
          <div v-if="selectedPhotos.length > 0" class="mb-4">
            <h3 class="text-lg font-semibold mb-2">Selected Photos:</h3>
            <div class="flex flex-wrap">
              <template v-for="photo in selectedPhotos">
                <transition name="fade" mode="out-in">
                  <img :src="photo" class="w-20 h-20 rounded-md ml-1" :class="{ 'opacity-0': fadeSelectedPhotos }"
                    :key="photo" />
                </transition>
              </template>
            </div>
          </div>

          <div class="flex items-center">
            <button type="submit" class="px-2 py-1 bg-indigo-500 text-white rounded-md">Upload Photos</button>
          </div>
        </form>

        <div class="mx-auto mt-4">
          <div v-if="fetchedPhotos.length > 0">
            <div class="w-1/3 px-4 pb-4">
              <label for="photoSize" class="block font-semibold mb-2">Adjust Photo Size:</label>
              <input type="range" id="photoSize" v-model="photoSizePercentage" min="10" max="200" step="10"
                class="w-full" />
              <span class="text-sm">{{ photoSizePercentage }}%</span>
            </div>
          </div>
        </div>

        <div class="mx-auto mt-4">
          <div v-if="fetchedPhotos.length > 0">
            <h3 class="text-lg font-semibold mb-2">Fetched Photos</h3>
            <div class="flex flex-wrap">
              <template v-for="photo in fetchedPhotos">
                <img :src="photo" class="rounded-md ml-1 mb-3"
                  :style="{ width: photoSizePercentage + '%', height: 'auto' }" />
              </template>
            </div>


          </div>
        </div>
      </div>
    </div>

    <div v-if="showSuccessMessage || showErrorMessage" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="rounded-lg shadow-md p-2 flex flex-col">
        <div v-if="showSuccessMessage" class="text-green-700 px-4 py-3 rounded-md overflow-auto" role="alert">
          <div class="flex items-center">
            <img src="@/assets/images/right.png" alt="Success Icon" class="w-6 h-6 mr-2" />
            <p class="font-semibold text-green-700">{{ successMessage }}</p>
          </div>
        </div>
        <div v-if="showErrorMessage" class="text-red-700 px-4 py-3 rounded-md overflow-auto" role="alert">
          <div class="flex items-center">
            <img src="@/assets/images/wrong.png" alt="Error Icon" class="w-6 h-6 mr-2" />
            <p class="font-semibold text-red-700">{{ errorMessage }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import api from '@/plugins/api';
import Header from '@/components/Header.vue';
const API_BASE_URL = 'http://127.0.0.1:8000/api/';



export default {
  components: {
    Header,
  },
  data() {
    return {
      showSuccessMessage: false,
      successMessage: '',
      showErrorMessage: false,
      errorMessage: '',
      collectionName: '',
      selectedPhotos: [],
      fetchedPhotos: [],
      fadeSelectedPhotos: false,
      photoSizePercentage: 30,
    };
  },

  async mounted() {
    const collectionId = this.$route.params.collectionId;
    await this.fetchCollectionInfo(collectionId); // Pass collectionId here
    await this.fetchAndDisplayFetchedPhotos(collectionId); // Pass collectionId here
  },

  methods: {
    async fetchCollectionInfo() {
      try {
        const collectionId = this.$route.params.collectionId;
        const collection = await api.getCollectionById(collectionId);
        this.collectionName = collection.data.name;
      } catch (error) {
        console.error('Error fetching collection info:', error);
      }
    },

    closeMessages() {
      this.showSuccessMessage = false;
      this.showErrorMessage = false;
    },

    autoCloseMessages() {
      setTimeout(() => {
        this.showSuccessMessage = false;
        this.showErrorMessage = false;
      }, 1000);
    },

    async uploadPhotos() {
      const formData = new FormData();
      const photosInput = this.$refs.photosInput;

      for (let i = 0; i < photosInput.files.length; i++) {
        formData.append('photos[]', photosInput.files[i]);
      }

      try {
        const response = await api.uploadPhotos(this.$route.params.collectionId, formData);
        console.log('Response from the server:', response);

        if (response.status === 201) {
          this.showSuccessMessage = true;
          this.successMessage = response.message;
          photosInput.value = '';
        }
      } catch (error) {
        console.error('Error uploading photos:', error);
        this.showErrorMessage = true;
        this.errorMessage = error.response.data.message || 'An unexpected error occurred. Please try again.';
        photosInput.value = '';
      }
    },

    onFileChange(event) {
      const files = event.target.files;
      this.selectedPhotos = [];

      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const objectURL = URL.createObjectURL(file);
        this.selectedPhotos.push(objectURL);
      }
    },
    async fetchAndDisplayFetchedPhotos(collectionId) {
      try {
        const photosResponse = await api.getAllPhotosByCollectionId(collectionId);
        console.log("Collection ID:", collectionId);
        console.log("PHOTOS", photosResponse);

        if (photosResponse.status === 200 && Array.isArray(photosResponse.data)) {
          console.log("Inside of array", photosResponse.data);
          const filePaths = photosResponse.data.map((photo) => `${API_BASE_URL}${photo}`);
          console.log("File Paths:", filePaths);
          this.fetchedPhotos = filePaths;
          console.log("Fetched Photos Length:", this.fetchedPhotos.length);
        } else {
          console.error('Invalid or unexpected API response:', photosResponse);
          this.fetchedPhotos = [];
        }
      } catch (error) {
        console.error('Error fetching and displaying fetched photos:', error);
        this.fetchedPhotos = [];
      }
    },
  },
};
</script>