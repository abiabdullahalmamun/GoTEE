import './bootstrap';
import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';
import websiteMs from './components/websiteMs.vue';
import Alpine from 'alpinejs';
// import billList from "./components/BillList.vue";
// import RoomBookingTable from "./components/RoomBookingTable.vue";
import AdminDashboard from "./components/AdminDashboard.vue";
// import BillCounterPayment from "./components/BillCounterPayment.vue";
import TapPayGateway from "./components/TapPayGateway.vue";
import BulkSMS from "./components/BulkSMS.vue";

import 'datatables.net-dt';   // DataTables JS
import 'datatables.net-dt/css/dataTables.dataTables.css'; // DataTables CSS

window.Alpine = Alpine;
Alpine.start();
console.log('Alpine script loaded');

import Chart from 'chart.js/auto';
window.Chart = Chart;

// Vue START --
const app = createApp({});
app.config.devtools = true;


// components here
app.component('example-component', ExampleComponent);
app.component('website-ms', websiteMs);
// app.component('bill-list', billList);
// app.component('room-booking-table', RoomBookingTable);
app.component('admin-dashboard', AdminDashboard);
// app.component('bill-counter-payment', BillCounterPayment);
// app.component('tap-pay-gateway', TapPayGateway);
app.component('bulk-sms', BulkSMS);


// mount vue
app.mount('#app');
