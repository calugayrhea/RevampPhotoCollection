import { createRouter, createWebHistory } from 'vue-router';
import CollectionList from './components/CollectionList.vue';
import CollectionForm from './components/CollectionForm.vue';
import EditCollection from './components/Collections/EditCollection.vue'; 
import PhotoUploadPage from './components/Photos/PhotoUploadPage.vue';

const routes = [
  { path: '/', redirect: '/collection-form' },
  { path: '/collection-list', name: 'collection-list', component: CollectionList },
  { path: '/collection-form', name: 'collection-form', component: CollectionForm },
  { path: '/edit-collection/:id', name: 'edit-collection', component: EditCollection },
  { path: '/photo-upload/:collectionId', name: 'photo-upload', component: PhotoUploadPage }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
