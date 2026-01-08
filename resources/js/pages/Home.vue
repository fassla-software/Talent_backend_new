<template>
    <div>
        <HeroBanner :banners="banner" :ads="ads" />
        <AboutSupport />
        <Categories :categories="categories" />
        <div v-if="incomingFlashSale">
            <FlashSaleIncoming :flashSale="incomingFlashSale" />
        </div>
        <div v-if="runningFlashSale">
            <FlashSaleRunning :flashSale="runningFlashSale" />
        </div>
        <PopularProducts :products="popularProducts" />
        <div v-if="master.getMultiVendor">
            <TopRatedShops :shops="topRatedShops" />
        </div>
        <JustForYou :justForYou="justForYou" />
        <RecentlyViews :products="recentlyViewProducts" />
    </div>
</template>

<script setup>
import { useMaster } from "../stores/MasterStore";
import { useBaskerStore } from "../stores/BasketStore";
import { onMounted, ref } from "vue";
import HeroBanner from "../components/HeroBanner.vue";
import AboutSupport from "../components/AboutSupport.vue";
import Categories from "../components/Categories.vue";
import FlashSaleRunning from "../components/FlashSaleRunning.vue";
import FlashSaleIncoming from "../components/FlashSaleIncoming.vue";
import PopularProducts from "../components/PopularProducts.vue";
import TopRatedShops from "../components/TopRatedShops.vue";
import JustForYou from "../components/JustForYou.vue";
import RecentlyViews from "../components/RecentlyViews.vue";

import { useAuth } from "../stores/AuthStore";
import axios from "axios";

const master = useMaster();
const baskerStore = useBaskerStore();

const authStore = useAuth();

onMounted(() => {
    getData();
    master.fetchData();
    baskerStore.fetchCart();
    fetchViewProducts();
    master.basketCanvas = false;
    authStore.loginModal = false;
    authStore.registerModal = false;
    authStore.showAddressModal = false;
    authStore.showChangeAddressModal = false;
});

const banner = ref([]);
const categories = ref([]);
const incomingFlashSale = ref(null);
const runningFlashSale = ref(null);
const popularProducts = ref([]);
const topRatedShops = ref([]);
const justForYou = ref([]);
const recentlyViewProducts = ref([]);
const ads = ref([]);

const getData = () => {
    axios
        .get("/home?page=1&per_page=12", {
            headers: {
                Authorization: authStore.token,
            },
        })
        .then((response) => {
            ads.value = response.data.data.ads;
            banner.value = response.data.data.banners;
            categories.value = response.data.data.categories;
            justForYou.value = response.data.data.just_for_you;
            popularProducts.value = response.data.data.popular_products;
            topRatedShops.value = response.data.data.shops.slice(0, 4);
            incomingFlashSale.value = response.data.data.incoming_flash_sale;
            runningFlashSale.value = response.data.data.running_flash_sale;
        })
        .catch(() => {});

    // fetch categories
    axios
        .get("/categories")
        .then((response) => {
            master.categories = response.data.data.categories;
        })
        .catch(() => {});
};

const fetchViewProducts = () => {
    if (authStore.token) {
        axios
            .get("/recently-views", {
                headers: {
                    Authorization: authStore.token,
                },
            })
            .then((response) => {
                recentlyViewProducts.value = response.data.data.products;
            })
            .catch(() => {});
    }
};
</script>

<style scoped></style>
