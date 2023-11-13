import axios from 'axios';

const API_BASE_URL = 'http://127.0.0.1:8000/api';

async function apiCall(method, url, data = null) {
  try {
    const response = await axios({
      method,
      url: `${API_BASE_URL}/${url}`,
      data,
    });
    return response.data;
  } catch (error) {
    console.error('API call error:', error);
    throw error;
  }
}

export default {
  async createCollection(collectionData) {
    return apiCall('post', 'apiCollections', collectionData);
  },

  async getAllCollections() {
    return apiCall('get', 'apiCollections');
  },

  async getCollectionById(collectionId) {
    return apiCall('get', `apiCollections/${collectionId}`);
  },

  async updateCollection(collectionId, collectionData) {
    return apiCall('put', `apiCollections/${collectionId}`, collectionData);
  },

  async deleteCollection(collectionId) {
    return apiCall('delete', `apiCollections/${collectionId}`);
  },

  async uploadPhotos(collectionId, formData) {
    return apiCall('post', `apiCollections/${collectionId}/photos`, formData);
  },

  async createPhoto(collectionId, photoData) {
    return apiCall('post', `apiCollections/${collectionId}/photos`, photoData);
  },

  async getAllPhotosByCollectionId(collectionId) {
    return apiCall('get', `apiCollections/${collectionId}/photos`);
},

async getPhotoById(collectionId, photoId) {
  return apiCall('get', `apiCollections/${collectionId}/photos/${photoId}`);
},

  async deletePhoto(collectionId, photoId) {
    return apiCall('delete', `apiCollections/${collectionId}/photos/${photoId}`);
  },  
};



