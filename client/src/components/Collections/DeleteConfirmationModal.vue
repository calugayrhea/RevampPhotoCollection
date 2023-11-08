<template>
    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="bg-white p-4 rounded-lg shadow-md w-1/3">
        <h2 class="text-xl font-semibold mb-4">Confirm Deletion</h2>
        <p class="mb-4">Are you sure you want to delete this collection?</p>
        <div class="flex justify-end">
          <button @click="cancelDelete" class="px-4 py-2 text-gray-500 mr-2">Cancel</button>
          <button @click="confirmDelete" class="px-4 py-2 bg-red-500 text-white">Delete</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      showModal: Boolean,
      onConfirm: Function,
      onCancel: Function,
    },
    methods: {
      confirmDelete() {
        this.onConfirm();
      },
      cancelDelete() {
        this.onCancel();
      },
    },
  };
  </script>
  
  <style scoped>
  
  </style>
  