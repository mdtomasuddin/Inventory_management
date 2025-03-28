<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: "top-right" });
import { ref } from 'vue';
let page = usePage();

const selectedProduct = ref([]);
const selectedCustomer = ref(null);

//customer
const CustomerHeader = [
    { text: "Name", value: "name" },
    { text: "Pick", value: "number" }
];

const CustomerItem = ref(page.props.customers);
const addCustomerToSale = (customer) => {
    selectedCustomer.value = customer;
};

const searchCustomerField = ref();
const searchCustomerValue = ref();
//end customer

//product
const ProductHeader = [
    { text: "Image", value: "image" },
    { text: "Name", value: "name" },
    { text: "Price", value: "price" },
    { text: "QTY", value: "unit" },
    { text: "Action", value: "action" },
];

const ProductItem = ref(page.props.products);

const searchProductField = ref('name');
const searchProductValue = ref();

const addProductToSale = (id, image, name, price, productUnit) => {
    const existingProduct = selectedProduct.value.find(product => product.id === id);
    if (existingProduct) {
        if (existingProduct.existingQty > 0) {
            existingProduct.unit++;
            existingProduct.existingQty--;
        } else {
            toaster.warning(`Product ${name} is out of stock`);
        }
    } else {
        if (productUnit > 0) {
            const product = {
                id: id,
                image: image,
                name: name,
                price: price,
                unit: 1,
                existingQty: productUnit - 1
            };
            selectedProduct.value.push(product);
            calculateTotal();
        } else {
            toaster.warning(`Product ${name} is out of stock`);
        }
    }
};//end method

const addQty = (id) => {
    const product = selectedProduct.value.find(product => product.id === id);
    if (product.existingQty > 0) {
        product.unit++;
        product.existingQty--;
        calculateTotal();
    } else {
        toaster.warning(`Product ${product.name} is out of stock`);
    }
};

const removeQty = (id) => {
    const product = selectedProduct.value.find(product => product.id === id);
    if (product.unit > 1) {
        product.existingQty++;
        product.unit--;
        calculateTotal();
    }
};

const removeProductFromSale = (index) => {
    selectedProduct.value.splice(index, 1);
    calculateTotal();
};

const vatRate = ref(5);
const flatDiscount = ref(0);
const discountPercent = ref(0);
const total = ref(0);
const vatAmount = ref(0);
const discountAmount = ref(0);
const usePercentageDiscount = ref(false);

const calculateTotal = () => {
    return selectedProduct.value.reduce((sum, product) => sum + product.price * product.unit, 0);
};

const applyVat = () => {
    vatAmount.value = (calculateTotal() * vatRate.value) / 100;
    calculateTotal();
}

const removeVat = () => {
    vatAmount.value = 0;
    calculateTotal();
}

</script>

<template>
    <!-- start content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Billing Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Billed To</h4>
                        <div class="shadow-sm h-100 bg-white rounded-3 p-3 mt-4">
                            <div class="row">
                                <div class="col-8">
                                    <span class="text-bold text-dark">BILLED TO</span>
                                    <p class="text-xs mx-0 my-1">Name: <span>{{ selectedCustomer?.name }}</span></p>
                                    <p class="text-xs mx-0 my-1">Email: <span>{{ selectedCustomer?.email }}</span></p>
                                    <p class="text-xs mx-0 my-1">User ID: <span>{{ selectedCustomer?.id }}</span></p>
                                </div>
                                <div class="col-4">
                                    <p class="text-bold mx-0 my-1 text-dark">Invoice</p>
                                    <p class="text-xs mx-0 my-1">Date: {{ new Date().toISOString().slice(0, 10) }}</p>
                                </div>
                            </div>
                            <hr class="mx-0 my-2 p-0 bg-secondary" />
                            <div class="row">
                                <div class="col-12">
                                    <table class="table w-100">
                                        <thead>
                                            <tr class="text-xs">
                                                <th>Name</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="selectedProduct.length > 0"
                                                v-for="(product, index) in selectedProduct" :key="index"
                                                class="text-center">
                                                <td> {{ product.name }} </td>
                                                <td> {{ product.unit }}</td>
                                                <td> {{ product.price }}</td>
                                                <td>
                                                    <button @click="removeQty(product.id)" class="">-</button>
                                                    <button @click="addQty(product.id)" class="">+</button>
                                                    <button @click="removeProductFromSale(index)"
                                                        class="btn btn-danger btn-sm">Remove</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr class="mx-0 my-2 p-0 bg-secondary" />
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-bold text-xs my-1 text-dark">Total: <i
                                            class="bi bi-currency-dollar"></i>{{ calculateTotal() }}</p>
                                    <p class="text-bold text-xs my-1 text-dark">VAT (5%): <i
                                            class="bi bi-currency-dollar"></i> {{ vatAmount }}</p>
                                    <p>
                                        <button @click="applyVat"
                                            class="btn btn-info btn-sm my-1 bg-gradient-primary w-40">Apply
                                            VAT</button>

                                    </p>
                                    <p><button @click="removeVat" class="btn btn-secondary btn-sm my-1 bg-gradient-primary w-40">Remove
                                            VAT</button></p>

                                    <p><span class="text-xxs">Discount Mode:</span></p>
                                    <select>
                                        <option value="false">Flat Discount</option>
                                        <option value="true">Percentage Discount</option>
                                    </select>
                                    <p class="text-bold text-xs my-1 text-dark">Discount: <i
                                            class="bi bi-currency-dollar"></i> {{ discountAmount }} </p>
                                    <div>
                                        <span class="text-xxs">Flat Discount:</span>
                                        <input type="number" class="form-control w-40" min="0" />
                                        <p><button class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40">Apply
                                                Flat Discount</button></p>
                                    </div>
                                    <div>
                                        <span class="text-xxs">Discount (%):</span>
                                        <input type="number" class="form-control w-40" min="0" max="100" step="0.25" />
                                        <p><button class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40">Apply
                                                Percentage Discount</button></p>
                                    </div>
                                    <p><button class="btn btn-secondary btn-sm my-1 bg-gradient-primary w-40">Remove
                                            Discount</button></p>

                                    <hr class="mx-0 my-2 p-0 bg-secondary" />
                                    <p class="text-bold text-xs my-1 text-dark">Payable: <i
                                            class="bi bi-currency-dollar"></i> payable</p>
                                    <p><button
                                            class="btn btn-success btn-sm my-3 bg-gradient-primary w-40">Confirm</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Select Product</h4>
                        <input placeholder="Search..." class="form-control mb-2 w-auto form-control-sm" type="text"
                            v-model="searchProductValue" />
                        <EasyDataTable buttons-paginations alternating :items="ProductItem" :headers="ProductHeader"
                            :rows-per-page="10" :search-value="searchProductValue" :seach-field="searchProductField"
                            show-index>
                            <template #item-image="{ image }">
                                <img :src="image" alt="Product Image" height="40px" width="40px" />
                            </template>

                            <template #item-action="{ id, image, name, price, unit }">
                                <button :class="['btn btn-sm', unit > 0 ? 'btn-success' : 'btn-danger']"
                                    :disabled="unit <= 0" @click="addProductToSale(id, image, name, price, unit)">
                                    {{ unit >= 0 ? 'Add' : 'Stock Out' }}
                                </button>
                            </template>
                        </EasyDataTable>
                        <p>{{ selectedProduct }}</p>
                    </div>
                </div>
            </div>

            <!-- Customer Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Select Customer</h4>
                        <input placeholder="Search..." class="form-control mb-2 w-auto form-control-sm" type="text"
                            v-model="searchCustomerValue" />
                        <EasyDataTable buttons-pagination alternating :headers="CustomerHeader" :items="CustomerItem"
                            :rows-per-page="10" :search-field="searchCustomerField" :search-value="searchCustomerValue"
                            show-index>
                            <template #item-number="{ id, name, email }">
                                <button @click="addCustomerToSale({ id, name, email })"
                                    class="btn btn-success btn-sm">Add</button>
                            </template>
                        </EasyDataTable>
                        <p>{{ selectedCustomer }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end content -->
</template>

<style scoped></style>