import Vue from "vue";
import VueRouter from "vue-router";
import Login from "@/views/Login.vue";
import Dashboard from "@/views/Dashboard.vue";
import Warehouse from "@/views/Warehouse.vue";
import WarehouseForm from "@/components/WarehouseForm.vue";
import WarehouseTrashed from "@/views/Trashed/Warehouse.vue";
import Category from "@/views/Category.vue";
import CategoryTrashed from "@/views/Trashed/Category.vue";
import Brand from "@/views/Brand.vue";
import BrandTrashed from "@/views/Trashed/Brand.vue";
import MainUnit from "@/views/MainUnit.vue";
import Product from "@/views/Product.vue";
import ProductForm from "@/components/ProductForm.vue";
// import ProductTrashed from "@/views/Trashed/Product.vue";
import Supplier from "@/views/Supplier.vue";
import SupplierForm from "@/components/SupplierForm.vue";
import SupplierTrashed from "@/views/Trashed/Supplier.vue";
import PurchaseForm from "@/components/PurchaseForm.vue";
import CustomerDashboard from "@/views/Customer/Dashboard.vue";

Vue.use(VueRouter);

const routes = [
	{
		path: "/login",
		name: "Login",
		component: Login,
		meta: { auth: false }
	},
	{
		path: "/",
		name: "Dashboard",
		component: Dashboard,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouse",
		name: "Warehouse",
		component: Warehouse,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouse/create",
		name: "WarehouseCreate",
		component: WarehouseForm,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouse/:warehouseId/update",
		name: "WarehouseUpdate",
		component: WarehouseForm,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/warehouse/trashed",
		name: "WarehouseTrashed",
		component: WarehouseTrashed,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/category",
		name: "Category",
		component: Category,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/category/trashed",
		name: "CategoryTrashed",
		component: CategoryTrashed,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/brand",
		name: "Brand",
		component: Brand,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/brand/trashed",
		name: "BrandTrashed",
		component: BrandTrashed,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/main-unit",
		name: "MainUnit",
		component: MainUnit,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/product",
		name: "Product",
		component: Product,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/product/create",
		name: "ProductCreate",
		component: ProductForm,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/product/:productId/update",
		name: "ProductUpdate",
		component: ProductForm,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/supplier",
		name: "Supplier",
		component: Supplier,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/supplier/create",
		name: "SupplierCreate",
		component: SupplierForm,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/supplier/:supplierId/update",
		name: "SupplierUpdate",
		component: SupplierForm,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/supplier/trashed",
		name: "SupplierTrashed",
		component: SupplierTrashed,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchase/create",
		name: "PurchaseCreate",
		component: PurchaseForm,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/purchase/:purchaseId/update",
		name: "PurchaseUpdate",
		component: PurchaseForm,
		meta: { auth: true, userType: [0, 1, 2] }
	},
	{
		path: "/",
		name: "CustomerDashboard",
		component: CustomerDashboard,
		meta: { auth: true, userType: [3] }
	}
];

const router = new VueRouter({
	mode: "history",
	base: process.env.BASE_URL,
	routes
});

export default router;
